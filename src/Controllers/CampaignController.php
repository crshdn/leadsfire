<?php

namespace LeadsFire\Controllers;

use LeadsFire\Models\Campaign;
use LeadsFire\Models\TrafficSource;
use LeadsFire\Models\AffiliateNetwork;
use LeadsFire\Models\LandingPage;
use LeadsFire\Models\Offer;

/**
 * Campaign Controller
 */
class CampaignController
{
    private Campaign $campaign;
    private TrafficSource $trafficSource;
    private AffiliateNetwork $affiliateNetwork;
    private LandingPage $landingPage;
    private Offer $offer;
    
    public function __construct()
    {
        $this->campaign = new Campaign();
        $this->trafficSource = new TrafficSource();
        $this->affiliateNetwork = new AffiliateNetwork();
        $this->landingPage = new LandingPage();
        $this->offer = new Offer();
    }
    
    /**
     * List all campaigns
     */
    public function index(): array
    {
        $filters = [
            'search' => $_GET['search'] ?? '',
            'status' => $_GET['status'] ?? '',
            'source_id' => $_GET['source'] ?? '',
            'limit' => 100,
        ];
        
        return [
            'campaigns' => $this->campaign->getAll($filters),
            'trafficSources' => $this->trafficSource->getForSelect(),
        ];
    }
    
    /**
     * Show create form
     */
    public function create(): array
    {
        return [
            'campaign' => null,
            'destinations' => [],
            'offers' => [],
            'trafficSources' => $this->trafficSource->getForSelect(),
            'affiliateNetworks' => $this->affiliateNetwork->getForSelect(),
            'landingPages' => $this->landingPage->getForSelect(),
            'predefOffers' => $this->offer->getForSelect(),
        ];
    }
    
    /**
     * Store new campaign
     */
    public function store(array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $campaignData = [
            'CampaignName' => $data['name'],
            'CPVSourceID' => $data['traffic_source_id'] ?: null,
            'CostModel' => $data['cost_model'] ?? 1,
            'CostValue' => $data['cost_value'] ?? 0,
            'Active' => isset($data['active']) ? 1 : 0,
            'RotationType' => $data['rotation_type'] ?? 2, // Default: Probabilistic
            'RedirectType' => $data['redirect_type'] ?? 302,
            'TrackingDomainID' => $data['tracking_domain_id'] ?? null,
        ];
        
        $campaignId = $this->campaign->create($campaignData);
        
        // Save destinations (landing pages)
        if (!empty($data['destinations'])) {
            $this->campaign->saveDestinations($campaignId, $this->parseDestinations($data['destinations']));
        }
        
        // Save offers
        if (!empty($data['offers'])) {
            $this->campaign->saveOffers($campaignId, $this->parseOffers($data['offers']));
        }
        
        return ['success' => true, 'campaign_id' => $campaignId];
    }
    
    /**
     * Show edit form
     */
    public function edit(int $id): ?array
    {
        $campaign = $this->campaign->find($id);
        
        if (!$campaign) {
            return null;
        }
        
        return [
            'campaign' => $campaign,
            'destinations' => $this->campaign->getDestinations($id),
            'offers' => $this->campaign->getOffers($id),
            'trafficSources' => $this->trafficSource->getForSelect(),
            'affiliateNetworks' => $this->affiliateNetwork->getForSelect(),
            'landingPages' => $this->landingPage->getForSelect(),
            'predefOffers' => $this->offer->getForSelect(),
        ];
    }
    
    /**
     * Update campaign
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $campaignData = [
            'CampaignName' => $data['name'],
            'CPVSourceID' => $data['traffic_source_id'] ?: null,
            'CostModel' => $data['cost_model'] ?? 1,
            'CostValue' => $data['cost_value'] ?? 0,
            'Active' => isset($data['active']) ? 1 : 0,
            'RotationType' => $data['rotation_type'] ?? 2,
            'RedirectType' => $data['redirect_type'] ?? 302,
            'TrackingDomainID' => $data['tracking_domain_id'] ?? null,
        ];
        
        $this->campaign->update($id, $campaignData);
        
        // Save destinations
        if (isset($data['destinations'])) {
            $this->campaign->saveDestinations($id, $this->parseDestinations($data['destinations']));
        }
        
        // Save offers
        if (isset($data['offers'])) {
            $this->campaign->saveOffers($id, $this->parseOffers($data['offers']));
        }
        
        return ['success' => true];
    }
    
    /**
     * Delete campaign
     */
    public function delete(int $id): bool
    {
        return $this->campaign->delete($id);
    }
    
    /**
     * Clone campaign
     */
    public function clone(int $id): ?int
    {
        return $this->campaign->clone($id);
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(int $id): bool
    {
        return $this->campaign->toggleActive($id);
    }
    
    /**
     * Validate campaign data
     */
    private function validate(array $data): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Campaign name is required';
        }
        
        if (strlen($data['name'] ?? '') > 200) {
            $errors['name'] = 'Campaign name must be less than 200 characters';
        }
        
        return $errors;
    }
    
    /**
     * Parse destinations from form data
     */
    private function parseDestinations(array $destinations): array
    {
        $parsed = [];
        
        foreach ($destinations as $dest) {
            if (empty($dest['url']) && empty($dest['predef_id'])) {
                continue;
            }
            
            $parsed[] = [
                'PredefLPID' => $dest['predef_id'] ?? null,
                'DestinationURL' => $dest['url'] ?? '',
                'Weight' => $dest['weight'] ?? 100,
                'Active' => isset($dest['active']) ? 1 : 1,
            ];
        }
        
        return $parsed;
    }
    
    /**
     * Parse offers from form data
     */
    private function parseOffers(array $offers): array
    {
        $parsed = [];
        
        foreach ($offers as $offer) {
            if (empty($offer['url']) && empty($offer['predef_id'])) {
                continue;
            }
            
            $parsed[] = [
                'PredefOfferID' => $offer['predef_id'] ?? null,
                'AffiliateSourceID' => $offer['affiliate_id'] ?? null,
                'OfferURL' => $offer['url'] ?? '',
                'Payout' => $offer['payout'] ?? 0,
                'Weight' => $offer['weight'] ?? 100,
                'Active' => isset($offer['active']) ? 1 : 1,
            ];
        }
        
        return $parsed;
    }
}

