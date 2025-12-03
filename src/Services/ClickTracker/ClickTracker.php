<?php

namespace LeadsFire\Services\ClickTracker;

use LeadsFire\Services\Database;
use LeadsFire\Services\Logger;
use LeadsFire\Services\GeoIP\GeoIPService;

/**
 * Click Tracker Service
 * 
 * Handles all click tracking logic including:
 * - Click registration
 * - SubID generation
 * - Landing page/Offer rotation
 * - Bot detection
 * - Deduplication
 */
class ClickTracker
{
    private Database $db;
    private Logger $logger;
    private ?GeoIPService $geoip = null;
    
    // Rotation types
    const ROTATION_EXACT = 1;
    const ROTATION_PROBABILISTIC = 2;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
    }
    
    /**
     * Process an incoming click
     */
    public function processClick(string $campaignKey, array $params = []): array
    {
        // Start timing
        $startTime = microtime(true);
        
        // Get campaign
        $campaign = $this->getCampaign($campaignKey);
        
        if (!$campaign) {
            $this->logger->warning('Campaign not found', ['key' => $campaignKey]);
            return ['success' => false, 'error' => 'Campaign not found'];
        }
        
        if (!$campaign['Active']) {
            $this->logger->warning('Campaign inactive', ['key' => $campaignKey]);
            return ['success' => false, 'error' => 'Campaign inactive'];
        }
        
        // Check for prefetch
        if (config('app.tracking.ignore_prefetch', true) && is_prefetch()) {
            return ['success' => false, 'error' => 'Prefetch ignored'];
        }
        
        // Check for bot
        $isBot = is_bot();
        if ($isBot) {
            $this->logBlockedClick($campaign['CampaignID'], 'bot');
            return ['success' => false, 'error' => 'Bot detected'];
        }
        
        // Get visitor info
        $visitorInfo = $this->getVisitorInfo();
        
        // Check deduplication
        $dedupSeconds = config('app.tracking.dedup_seconds', 0);
        if ($dedupSeconds > 0 && $this->isDuplicate($campaign['CampaignID'], $visitorInfo['ip'], $dedupSeconds)) {
            $this->logger->debug('Duplicate click ignored', [
                'campaign_id' => $campaign['CampaignID'],
                'ip' => $visitorInfo['ip']
            ]);
            return ['success' => false, 'error' => 'Duplicate click'];
        }
        
        // Get or create SubID
        $subIdData = $this->getOrCreateSubId($campaign['CampaignID'], $params);
        
        // Get destination (landing page)
        $destination = $this->selectDestination($campaign['CampaignID'], $campaign['RotationType'] ?? self::ROTATION_PROBABILISTIC);
        
        // Get offer
        $offer = $this->selectOffer($campaign['CampaignID'], $campaign['RotationType'] ?? self::ROTATION_PROBABILISTIC);
        
        // Generate click ID
        $clickId = $this->generateClickId();
        
        // Record the click
        $clickData = [
            'ClickID' => $clickId,
            'CampaignID' => $campaign['CampaignID'],
            'SubIdID' => $subIdData['SubIdID'],
            'DestinationID' => $destination['DestinationID'] ?? 0,
            'OfferID' => $offer['OfferID'] ?? null,
            'ViewDate' => date('Y-m-d H:i:s'),
            'IPBinary' => inet_pton($visitorInfo['ip']),
            'UserAgentID' => $this->getOrCreateUserAgentId($visitorInfo['user_agent']),
            'ReferrerID' => $this->getOrCreateReferrerId($visitorInfo['referrer']),
            'DeviceID' => $visitorInfo['device_id'] ?? 1,
            'IspID' => $visitorInfo['isp_id'] ?? 1,
        ];
        
        // Add extra params
        for ($i = 1; $i <= 10; $i++) {
            $extraKey = "extra$i";
            if (!empty($params[$extraKey])) {
                $clickData["Extra{$i}ID"] = $this->getOrCreateExtraId($i, $params[$extraKey]);
            }
        }
        
        // Insert click record
        try {
            $this->db->insert('clicks', $clickData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to insert click', ['error' => $e->getMessage()]);
        }
        
        // Update cache
        $this->updateClickCache($campaign['CampaignID'], $subIdData['SubIdID'], $destination['DestinationID'] ?? 0);
        
        // Build redirect URL
        $redirectUrl = $this->buildRedirectUrl($destination, $offer, $clickId, $subIdData, $params);
        
        // Set tracking cookie
        $this->setTrackingCookie($clickId, $campaign['CampaignID']);
        
        $processingTime = (microtime(true) - $startTime) * 1000;
        
        $this->logger->debug('Click processed', [
            'campaign_id' => $campaign['CampaignID'],
            'click_id' => $clickId,
            'subid' => $subIdData['SubId'],
            'processing_time_ms' => round($processingTime, 2)
        ]);
        
        return [
            'success' => true,
            'click_id' => $clickId,
            'subid' => $subIdData['SubId'],
            'redirect_url' => $redirectUrl,
            'redirect_type' => $campaign['RedirectType'] ?? 302,
        ];
    }
    
    /**
     * Get campaign by key
     */
    private function getCampaign(string $key): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM campaigns WHERE CampaignKey = ?",
            [$key]
        );
    }
    
    /**
     * Get visitor information
     */
    private function getVisitorInfo(): array
    {
        $ip = get_client_ip();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        
        // Get GeoIP info
        $geoInfo = [];
        if ($this->geoip === null) {
            $this->geoip = new GeoIPService();
        }
        $geoInfo = $this->geoip->lookup($ip);
        
        return [
            'ip' => $ip,
            'user_agent' => $userAgent,
            'referrer' => $referrer,
            'country' => $geoInfo['country'] ?? '',
            'city' => $geoInfo['city'] ?? '',
            'device_id' => 1, // TODO: Implement device detection
            'isp_id' => 1, // TODO: Implement ISP detection
        ];
    }
    
    /**
     * Check if click is duplicate
     */
    private function isDuplicate(int $campaignId, string $ip, int $seconds): bool
    {
        $ipBinary = inet_pton($ip);
        $cutoff = date('Y-m-d H:i:s', time() - $seconds);
        
        $count = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM clicks 
             WHERE CampaignID = ? AND IPBinary = ? AND ViewDate > ?",
            [$campaignId, $ipBinary, $cutoff]
        );
        
        return $count > 0;
    }
    
    /**
     * Get or create SubID
     */
    private function getOrCreateSubId(int $campaignId, array $params): array
    {
        $subIdValue = $params['subid'] ?? $params['target'] ?? $params['keyword'] ?? '';
        
        if (empty($subIdValue)) {
            $subIdValue = 'direct';
        }
        
        // Check if exists
        $existing = $this->db->fetch(
            "SELECT * FROM subids WHERE CampaignID = ? AND SubId = ?",
            [$campaignId, $subIdValue]
        );
        
        if ($existing) {
            return $existing;
        }
        
        // Create new
        $id = $this->db->insert('subids', [
            'CampaignID' => $campaignId,
            'SubId' => $subIdValue,
            'DateAdded' => date('Y-m-d H:i:s'),
        ]);
        
        return [
            'SubIdID' => $id,
            'SubId' => $subIdValue,
        ];
    }
    
    /**
     * Select destination (landing page) based on rotation type
     */
    private function selectDestination(int $campaignId, int $rotationType): ?array
    {
        $destinations = $this->db->fetchAll(
            "SELECT * FROM destinations WHERE CampaignID = ? AND Active = 1 ORDER BY Weight DESC",
            [$campaignId]
        );
        
        if (empty($destinations)) {
            return null;
        }
        
        if (count($destinations) === 1) {
            return $destinations[0];
        }
        
        if ($rotationType === self::ROTATION_EXACT) {
            // Round-robin based on click count
            $index = $this->getRotationIndex($campaignId, 'destination', count($destinations));
            return $destinations[$index];
        }
        
        // Probabilistic rotation
        return $this->selectByWeight($destinations, 'Weight');
    }
    
    /**
     * Select offer based on rotation type
     */
    private function selectOffer(int $campaignId, int $rotationType): ?array
    {
        $offers = $this->db->fetchAll(
            "SELECT * FROM offers WHERE CampaignID = ? AND Active = 1 ORDER BY Weight DESC",
            [$campaignId]
        );
        
        if (empty($offers)) {
            return null;
        }
        
        if (count($offers) === 1) {
            return $offers[0];
        }
        
        if ($rotationType === self::ROTATION_EXACT) {
            $index = $this->getRotationIndex($campaignId, 'offer', count($offers));
            return $offers[$index];
        }
        
        return $this->selectByWeight($offers, 'Weight');
    }
    
    /**
     * Select item by weight (probabilistic)
     */
    private function selectByWeight(array $items, string $weightKey): array
    {
        $totalWeight = array_sum(array_column($items, $weightKey));
        $random = mt_rand(1, $totalWeight);
        
        $cumulative = 0;
        foreach ($items as $item) {
            $cumulative += $item[$weightKey];
            if ($random <= $cumulative) {
                return $item;
            }
        }
        
        return $items[0];
    }
    
    /**
     * Get rotation index for exact rotation
     */
    private function getRotationIndex(int $campaignId, string $type, int $count): int
    {
        $key = "rotation_{$campaignId}_{$type}";
        
        // Use simple counter in database
        $counter = $this->db->fetchColumn(
            "SELECT ConfigValue FROM config WHERE ConfigName = ?",
            [$key]
        );
        
        $index = ($counter ?? 0) % $count;
        
        // Increment counter
        if ($counter === false) {
            $this->db->insert('config', [
                'ConfigName' => $key,
                'ConfigValue' => '1',
            ]);
        } else {
            $this->db->update('config', 
                ['ConfigValue' => (string)(((int)$counter + 1) % 1000000)],
                'ConfigName = ?',
                [$key]
            );
        }
        
        return $index;
    }
    
    /**
     * Generate unique click ID
     */
    private function generateClickId(): int
    {
        // Use timestamp + random for uniqueness
        return (int)(microtime(true) * 10000) + mt_rand(0, 9999);
    }
    
    /**
     * Build redirect URL with placeholders replaced
     */
    private function buildRedirectUrl(?array $destination, ?array $offer, int $clickId, array $subIdData, array $params): string
    {
        // If we have a landing page, redirect there
        if ($destination && !empty($destination['DestinationURL'])) {
            $url = $destination['DestinationURL'];
        } elseif ($offer && !empty($offer['OfferURL'])) {
            // Direct to offer
            $url = $offer['OfferURL'];
        } else {
            // Fallback - should not happen
            return config('app.url', '/');
        }
        
        // Replace placeholders
        $replacements = [
            '{subid}' => $this->encodeSubId($clickId),
            '{clickid}' => $clickId,
            '{campaignid}' => $subIdData['CampaignID'] ?? '',
            '{keyword}' => $params['keyword'] ?? '',
            '{target}' => $params['target'] ?? '',
        ];
        
        // Add extra params
        for ($i = 1; $i <= 10; $i++) {
            $replacements["{extra$i}"] = $params["extra$i"] ?? '';
        }
        
        $url = str_replace(array_keys($replacements), array_values($replacements), $url);
        
        return $url;
    }
    
    /**
     * Encode click ID to short subid format (CPVLab compatible)
     */
    private function encodeSubId(int $clickId): string
    {
        // Base62 encoding for shorter URLs
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($chars);
        $encoded = '';
        
        while ($clickId > 0) {
            $encoded = $chars[$clickId % $base] . $encoded;
            $clickId = (int)($clickId / $base);
        }
        
        return $encoded ?: '0';
    }
    
    /**
     * Decode subid back to click ID
     */
    public function decodeSubId(string $subId): int
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $base = strlen($chars);
        $clickId = 0;
        
        for ($i = 0; $i < strlen($subId); $i++) {
            $clickId = $clickId * $base + strpos($chars, $subId[$i]);
        }
        
        return $clickId;
    }
    
    /**
     * Set tracking cookie
     */
    private function setTrackingCookie(int $clickId, int $campaignId): void
    {
        $cookieTimeout = config('app.tracking.cookie_timeout', 2592000);
        $secure = config('app.tracking.cookie_secure', true);
        $sameSite = config('app.tracking.cookie_samesite', 'None');
        
        $cookieValue = json_encode([
            'click_id' => $clickId,
            'campaign_id' => $campaignId,
            'timestamp' => time(),
        ]);
        
        setcookie(
            'lf_click',
            $cookieValue,
            [
                'expires' => time() + $cookieTimeout,
                'path' => '/',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => $sameSite,
            ]
        );
    }
    
    /**
     * Get or create user agent ID
     */
    private function getOrCreateUserAgentId(string $userAgent): int
    {
        if (empty($userAgent)) {
            return 1;
        }
        
        $hash = md5($userAgent);
        
        $existing = $this->db->fetchColumn(
            "SELECT UserAgentID FROM useragents WHERE UserAgentHash = ?",
            [$hash]
        );
        
        if ($existing) {
            return (int)$existing;
        }
        
        return $this->db->insert('useragents', [
            'UserAgentHash' => $hash,
            'UserAgent' => substr($userAgent, 0, 500),
        ]);
    }
    
    /**
     * Get or create referrer ID
     */
    private function getOrCreateReferrerId(string $referrer): int
    {
        if (empty($referrer)) {
            return 1;
        }
        
        $domain = parse_url($referrer, PHP_URL_HOST) ?: $referrer;
        
        $existing = $this->db->fetchColumn(
            "SELECT ReferrerID FROM referrers WHERE ReferrerDomain = ?",
            [$domain]
        );
        
        if ($existing) {
            return (int)$existing;
        }
        
        return $this->db->insert('referrers', [
            'ReferrerDomain' => substr($domain, 0, 200),
            'ReferrerURL' => substr($referrer, 0, 500),
        ]);
    }
    
    /**
     * Get or create extra field ID
     */
    private function getOrCreateExtraId(int $fieldNum, string $value): int
    {
        if (empty($value)) {
            return 0;
        }
        
        $table = "extra{$fieldNum}s";
        $idField = "Extra{$fieldNum}ID";
        $valueField = "Extra{$fieldNum}";
        
        $existing = $this->db->fetchColumn(
            "SELECT {$idField} FROM {$table} WHERE {$valueField} = ?",
            [$value]
        );
        
        if ($existing) {
            return (int)$existing;
        }
        
        return $this->db->insert($table, [
            $valueField => substr($value, 0, 200),
        ]);
    }
    
    /**
     * Update click cache
     */
    private function updateClickCache(int $campaignId, int $subIdId, int $destinationId): void
    {
        $today = date('Y-m-d');
        
        // Update campaign cache
        $this->db->query(
            "INSERT INTO cachecampaign (CampaignID, DateInterval, Views) 
             VALUES (?, ?, 1)
             ON DUPLICATE KEY UPDATE Views = Views + 1",
            [$campaignId, $today]
        );
        
        // Update campaign totals
        $this->db->query(
            "INSERT INTO cachetotals (CampaignID, Views, NewViews) 
             VALUES (?, 1, 1)
             ON DUPLICATE KEY UPDATE Views = Views + 1, NewViews = NewViews + 1",
            [$campaignId]
        );
    }
    
    /**
     * Log blocked click
     */
    private function logBlockedClick(int $campaignId, string $reason): void
    {
        $reasonCode = match($reason) {
            'bot' => 1,
            'duplicate' => 2,
            'blocked_ip' => 3,
            'blocked_ua' => 4,
            default => 0,
        };
        
        try {
            $this->db->insert('blockedclicks', [
                'CampaignID' => $campaignId,
                'ViewDate' => date('Y-m-d H:i:s'),
                'BlockReason' => $reasonCode,
                'IPBinary' => inet_pton(get_client_ip()),
                'UserAgent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 191),
                'Referrer' => substr($_SERVER['HTTP_REFERER'] ?? '', 0, 191),
            ]);
        } catch (\Exception $e) {
            // Ignore errors for blocked click logging
        }
    }
}

