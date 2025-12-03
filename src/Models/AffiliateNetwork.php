<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Affiliate Network Model (affiliatesources table)
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
            "SELECT * FROM affiliatesources ORDER BY Affiliate ASC"
        );
    }
    
    /**
     * Get affiliate network by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM affiliatesources WHERE AffiliateSourceID = ?",
            [$id]
        );
    }
    
    /**
     * Create a new affiliate network
     */
    public function create(array $data): int
    {
        return $this->db->insert('affiliatesources', $data);
    }
    
    /**
     * Update an affiliate network
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update('affiliatesources', $data, 'AffiliateSourceID = ?', [$id]) > 0;
    }
    
    /**
     * Delete an affiliate network
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('affiliatesources', 'AffiliateSourceID = ?', [$id]) > 0;
    }
    
    /**
     * Get for dropdown select
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT AffiliateSourceID as id, Affiliate as name FROM affiliatesources ORDER BY Affiliate ASC"
        );
    }
}

