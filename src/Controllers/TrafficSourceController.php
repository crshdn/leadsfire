<?php

namespace LeadsFire\Controllers;

use LeadsFire\Models\TrafficSource;

/**
 * Traffic Source Controller
 */
class TrafficSourceController
{
    private TrafficSource $model;
    
    public function __construct()
    {
        $this->model = new TrafficSource();
    }
    
    /**
     * List all traffic sources
     */
    public function index(): array
    {
        return [
            'trafficSources' => $this->model->getAll(),
        ];
    }
    
    /**
     * Get single traffic source
     */
    public function show(int $id): ?array
    {
        return $this->model->find($id);
    }
    
    /**
     * Store new traffic source
     */
    public function store(array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $sourceData = [
            'CPVSource' => $data['name'],
            'SubIdParameter' => $data['subid_param'] ?? 'subid',
            'CostParameter' => $data['cost_param'] ?? '',
            'ExternalIDParameter' => $data['external_id_param'] ?? '',
            'KeywordParameter' => $data['keyword_param'] ?? 'keyword',
            'Extra1Parameter' => $data['extra1_param'] ?? '',
            'Extra2Parameter' => $data['extra2_param'] ?? '',
            'Extra3Parameter' => $data['extra3_param'] ?? '',
            'Extra4Parameter' => $data['extra4_param'] ?? '',
            'Extra5Parameter' => $data['extra5_param'] ?? '',
            'Extra6Parameter' => $data['extra6_param'] ?? '',
            'Extra7Parameter' => $data['extra7_param'] ?? '',
            'Extra8Parameter' => $data['extra8_param'] ?? '',
            'Extra9Parameter' => $data['extra9_param'] ?? '',
            'Extra10Parameter' => $data['extra10_param'] ?? '',
            'TrackingURLTemplate' => $data['tracking_template'] ?? '',
            'PostbackURL' => $data['postback_url'] ?? '',
        ];
        
        $id = $this->model->create($sourceData);
        
        return ['success' => true, 'id' => $id];
    }
    
    /**
     * Update traffic source
     */
    public function update(int $id, array $data): array
    {
        $errors = $this->validate($data);
        
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }
        
        $sourceData = [
            'CPVSource' => $data['name'],
            'SubIdParameter' => $data['subid_param'] ?? 'subid',
            'CostParameter' => $data['cost_param'] ?? '',
            'ExternalIDParameter' => $data['external_id_param'] ?? '',
            'KeywordParameter' => $data['keyword_param'] ?? 'keyword',
            'Extra1Parameter' => $data['extra1_param'] ?? '',
            'Extra2Parameter' => $data['extra2_param'] ?? '',
            'Extra3Parameter' => $data['extra3_param'] ?? '',
            'Extra4Parameter' => $data['extra4_param'] ?? '',
            'Extra5Parameter' => $data['extra5_param'] ?? '',
            'Extra6Parameter' => $data['extra6_param'] ?? '',
            'Extra7Parameter' => $data['extra7_param'] ?? '',
            'Extra8Parameter' => $data['extra8_param'] ?? '',
            'Extra9Parameter' => $data['extra9_param'] ?? '',
            'Extra10Parameter' => $data['extra10_param'] ?? '',
            'TrackingURLTemplate' => $data['tracking_template'] ?? '',
            'PostbackURL' => $data['postback_url'] ?? '',
        ];
        
        $this->model->update($id, $sourceData);
        
        return ['success' => true];
    }
    
    /**
     * Delete traffic source
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
            $errors['name'] = 'Traffic source name is required';
        }
        
        return $errors;
    }
}

