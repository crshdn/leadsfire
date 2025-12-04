<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Affiliate Network Model
 */
class AffiliateNetwork
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all affiliate networks
     */
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM affiliate_networks ORDER BY name ASC"
        );
    }
    
    /**
     * Get active affiliate networks for select dropdown
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name FROM affiliate_networks WHERE is_active = 1 ORDER BY name ASC"
        );
    }
    
    /**
     * Get affiliate network by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM affiliate_networks WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Create a new affiliate network
     */
    public function create(array $data): int
    {
        $data['slug'] = $this->generateSlug($data['name']);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('affiliate_networks', $data);
    }
    
    /**
     * Update an affiliate network
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('affiliate_networks', $data, 'id = ?', [$id]) > 0;
    }
    
    /**
     * Delete an affiliate network
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('affiliate_networks', 'id = ?', [$id]) > 0;
    }
    
    /**
     * Generate URL-safe slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        return $slug;
    }
}
