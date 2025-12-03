<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Offer Model (predefoffers table)
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
            "SELECT o.*, a.Affiliate 
             FROM predefoffers o
             LEFT JOIN affiliatesources a ON o.AffiliateSourceID = a.AffiliateSourceID
             ORDER BY o.PredefOfferName ASC"
        );
    }
    
    /**
     * Get offer by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM predefoffers WHERE PredefOfferID = ?",
            [$id]
        );
    }
    
    /**
     * Create a new offer
     */
    public function create(array $data): int
    {
        $data['DateAdded'] = date('Y-m-d H:i:s');
        return $this->db->insert('predefoffers', $data);
    }
    
    /**
     * Update an offer
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update('predefoffers', $data, 'PredefOfferID = ?', [$id]) > 0;
    }
    
    /**
     * Delete an offer
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('predefoffers', 'PredefOfferID = ?', [$id]) > 0;
    }
    
    /**
     * Get for dropdown select
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT PredefOfferID as id, PredefOfferName as name, PredefOfferURL as url, Payout as payout
             FROM predefoffers ORDER BY PredefOfferName ASC"
        );
    }
}

