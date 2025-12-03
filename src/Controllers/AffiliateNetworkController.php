<?php

namespace LeadsFire\Controllers;

use LeadsFire\Models\AffiliateNetwork;

/**
 * Affiliate Network Controller
 */
class AffiliateNetworkController
{
    private AffiliateNetwork $model;
    
    public function __construct()
    {
        $this->model = new AffiliateNetwork();
    }
    
    /**
     * List all affiliate networks
     */
    public function index(): array
    {
        return [
            'networks' => $this->model->getAll(),
        ];
    }
    
    /**
     * Get single network
     */
    public function show(int $id): ?array
    {
        return $this->model->find($id);
    }
    
    /**
     * Store new network
     */
    public function store(array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $networkData = [
            'Affiliate' => $data['name'],
            'RevenueParam' => $data['revenue_param'] ?? 'revenue',
            'SubIdSeparator' => $data['subid_separator'] ?? '_',
            'OfferParameter' => $data['offer_param'] ?? '',
            'OfferTemplate' => $data['offer_template'] ?? '',
            'PostbackURL' => $data['postback_url'] ?? '',
            'SubIdPlace' => $data['subid_place'] ?? '',
            'RevenuePlace' => $data['revenue_place'] ?? '',
            'StatusPlace' => $data['status_place'] ?? '',
            'TransactionPlace' => $data['transaction_place'] ?? '',
            'Custom1Place' => $data['custom1_place'] ?? '',
            'Custom2Place' => $data['custom2_place'] ?? '',
            'Custom3Place' => $data['custom3_place'] ?? '',
            'Custom4Place' => $data['custom4_place'] ?? '',
            'Custom5Place' => $data['custom5_place'] ?? '',
            'StatusValues' => $data['status_values'] ?? '',
        ];
        
        $id = $this->model->create($networkData);
        
        return ['success' => true, 'id' => $id];
    }
    
    /**
     * Update network
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $networkData = [
            'Affiliate' => $data['name'],
            'RevenueParam' => $data['revenue_param'] ?? 'revenue',
            'SubIdSeparator' => $data['subid_separator'] ?? '_',
            'OfferParameter' => $data['offer_param'] ?? '',
            'OfferTemplate' => $data['offer_template'] ?? '',
            'PostbackURL' => $data['postback_url'] ?? '',
            'SubIdPlace' => $data['subid_place'] ?? '',
            'RevenuePlace' => $data['revenue_place'] ?? '',
            'StatusPlace' => $data['status_place'] ?? '',
            'TransactionPlace' => $data['transaction_place'] ?? '',
            'Custom1Place' => $data['custom1_place'] ?? '',
            'Custom2Place' => $data['custom2_place'] ?? '',
            'Custom3Place' => $data['custom3_place'] ?? '',
            'Custom4Place' => $data['custom4_place'] ?? '',
            'Custom5Place' => $data['custom5_place'] ?? '',
            'StatusValues' => $data['status_values'] ?? '',
        ];
        
        $this->model->update($id, $networkData);
        
        return ['success' => true];
    }
    
    /**
     * Delete network
     */
    public function delete(int $id): bool
    {
        return $this->model->delete($id);
    }
    
    /**
     * Validate data
     */
    private function validate(array $data): array
    {
        $errors = [];
        
        if (empty($data['name'])) {
            $errors['name'] = 'Affiliate network name is required';
        }
        
        return $errors;
    }
}

