<?php

namespace LeadsFire\Services\GeoIP;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

/**
 * GeoIP Service
 * 
 * Pluggable GeoIP provider architecture.
 * Default: ip-api.com (free tier)
 */
class GeoIPService
{
    private string $provider;
    private ?Client $httpClient = null;
    private array $cache = [];
    
    public function __construct()
    {
        $this->provider = config('app.geoip.provider', 'ip-api');
    }
    
    /**
     * Lookup IP address
     */
    public function lookup(string $ip): array
    {
        // Check cache first
        if (isset($this->cache[$ip])) {
            return $this->cache[$ip];
        }
        
        // Skip private/local IPs
        if ($this->isPrivateIp($ip)) {
            return $this->getDefaultResult();
        }
        
        $result = match($this->provider) {
            'ip-api' => $this->lookupIpApi($ip),
            'maxmind' => $this->lookupMaxMind($ip),
            'ipinfo' => $this->lookupIpInfo($ip),
            default => $this->getDefaultResult(),
        };
        
        // Cache result
        $this->cache[$ip] = $result;
        
        return $result;
    }
    
    /**
     * Lookup using ip-api.com (free tier: 45 req/min)
     */
    private function lookupIpApi(string $ip): array
    {
        try {
            $client = $this->getHttpClient();
            $response = $client->get("http://ip-api.com/json/{$ip}", [
                'query' => [
                    'fields' => 'status,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,mobile,proxy,hosting'
                ],
                'timeout' => 2,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            if ($data['status'] === 'success') {
                return [
                    'country' => $data['country'] ?? '',
                    'country_code' => $data['countryCode'] ?? '',
                    'region' => $data['regionName'] ?? '',
                    'region_code' => $data['region'] ?? '',
                    'city' => $data['city'] ?? '',
                    'zip' => $data['zip'] ?? '',
                    'latitude' => $data['lat'] ?? 0,
                    'longitude' => $data['lon'] ?? 0,
                    'timezone' => $data['timezone'] ?? '',
                    'isp' => $data['isp'] ?? '',
                    'org' => $data['org'] ?? '',
                    'as' => $data['as'] ?? '',
                    'is_mobile' => $data['mobile'] ?? false,
                    'is_proxy' => $data['proxy'] ?? false,
                    'is_hosting' => $data['hosting'] ?? false,
                ];
            }
        } catch (GuzzleException $e) {
            // Log error but don't fail
        }
        
        return $this->getDefaultResult();
    }
    
    /**
     * Lookup using MaxMind GeoLite2 database
     */
    private function lookupMaxMind(string $ip): array
    {
        $dbPath = config('app.geoip.maxmind.database_path', '');
        
        if (!file_exists($dbPath)) {
            return $this->getDefaultResult();
        }
        
        // MaxMind requires their PHP reader library
        // This is a placeholder for the implementation
        // You would need: composer require geoip2/geoip2
        
        return $this->getDefaultResult();
    }
    
    /**
     * Lookup using ipinfo.io
     */
    private function lookupIpInfo(string $ip): array
    {
        try {
            $client = $this->getHttpClient();
            $response = $client->get("https://ipinfo.io/{$ip}/json", [
                'timeout' => 2,
            ]);
            
            $data = json_decode($response->getBody()->getContents(), true);
            
            if (!empty($data['country'])) {
                $loc = explode(',', $data['loc'] ?? '0,0');
                
                return [
                    'country' => $data['country'] ?? '',
                    'country_code' => $data['country'] ?? '',
                    'region' => $data['region'] ?? '',
                    'region_code' => '',
                    'city' => $data['city'] ?? '',
                    'zip' => $data['postal'] ?? '',
                    'latitude' => (float)($loc[0] ?? 0),
                    'longitude' => (float)($loc[1] ?? 0),
                    'timezone' => $data['timezone'] ?? '',
                    'isp' => $data['org'] ?? '',
                    'org' => $data['org'] ?? '',
                    'as' => '',
                    'is_mobile' => false,
                    'is_proxy' => false,
                    'is_hosting' => false,
                ];
            }
        } catch (GuzzleException $e) {
            // Log error but don't fail
        }
        
        return $this->getDefaultResult();
    }
    
    /**
     * Check if IP is private/local
     */
    private function isPrivateIp(string $ip): bool
    {
        return !filter_var(
            $ip, 
            FILTER_VALIDATE_IP, 
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        );
    }
    
    /**
     * Get default result for unknown IPs
     */
    private function getDefaultResult(): array
    {
        return [
            'country' => '',
            'country_code' => '',
            'region' => '',
            'region_code' => '',
            'city' => '',
            'zip' => '',
            'latitude' => 0,
            'longitude' => 0,
            'timezone' => '',
            'isp' => '',
            'org' => '',
            'as' => '',
            'is_mobile' => false,
            'is_proxy' => false,
            'is_hosting' => false,
        ];
    }
    
    /**
     * Get HTTP client instance
     */
    private function getHttpClient(): Client
    {
        if ($this->httpClient === null) {
            $this->httpClient = new Client([
                'timeout' => 3,
                'connect_timeout' => 2,
            ]);
        }
        
        return $this->httpClient;
    }
}

