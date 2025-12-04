<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Offer Model
 */
class Offer
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all offers
     */
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, an.name as network_name, g.name as group_name
             FROM offers o
             LEFT JOIN affiliate_networks an ON o.affiliate_network_id = an.id
             LEFT JOIN `groups` g ON o.group_id = g.id AND g.type = 'offer'
             ORDER BY o.name ASC"
        );
    }
    
    /**
     * Get active offers for select dropdown
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, url, payout FROM offers WHERE is_active = 1 ORDER BY name ASC"
        );
    }
    
    /**
     * Get offer by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM offers WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Create a new offer
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('offers', $data);
    }
    
    /**
     * Update an offer
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('offers', $data, 'id = ?', [$id]) > 0;
    }
    
    /**
     * Delete an offer
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('offers', 'id = ?', [$id]) > 0;
    }
}
