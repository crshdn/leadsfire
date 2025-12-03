<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Campaign Model
 */
class Campaign
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all campaigns with stats
     */
    public function getAll(array $filters = []): array
    {
        $sql = "
            SELECT 
                c.CampaignID,
                c.CampaignName,
                c.CampaignKey,
                c.Active,
                c.DateAdded,
                c.ModifyDate,
                c.CPVSourceID,
                c.CostModel,
                c.CostValue,
                s.CPVSource as TrafficSource,
                COALESCE(ct.Views, 0) as Views,
                COALESCE(ct.Clicks, 0) as Clicks,
                COALESCE(ct.Conversion, 0) as Conversions,
                COALESCE(ct.Revenue, 0) as Revenue,
                COALESCE(ct.Cost, 0) as Cost,
                COALESCE(ct.Profit, 0) as Profit,
                COALESCE(ct.ROI, 0) as ROI
            FROM campaigns c
            LEFT JOIN cpvsources s ON c.CPVSourceID = s.CPVSourceID
            LEFT JOIN cachetotals ct ON c.CampaignID = ct.CampaignID
            WHERE 1=1
        ";
        
        $params = [];
        
        if (!empty($filters['status'])) {
            $sql .= " AND c.Active = ?";
            $params[] = $filters['status'] === 'active' ? 1 : 0;
        }
        
        if (!empty($filters['source_id'])) {
            $sql .= " AND c.CPVSourceID = ?";
            $params[] = $filters['source_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.CampaignName LIKE ? OR c.CampaignKey LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY c.DateAdded DESC";
        
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT " . (int)$filters['limit'];
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get campaign by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM campaigns WHERE CampaignID = ?",
            [$id]
        );
    }
    
    /**
     * Get campaign by key
     */
    public function findByKey(string $key): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM campaigns WHERE CampaignKey = ?",
            [$key]
        );
    }
    
    /**
     * Create a new campaign
     */
    public function create(array $data): int
    {
        // Generate unique campaign key
        $data['CampaignKey'] = $this->generateCampaignKey();
        $data['DateAdded'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('campaigns', $data);
    }
    
    /**
     * Update a campaign
     */
    public function update(int $id, array $data): bool
    {
        $data['ModifyDate'] = date('Y-m-d H:i:s');
        return $this->db->update('campaigns', $data, 'CampaignID = ?', [$id]) > 0;
    }
    
    /**
     * Delete a campaign
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('campaigns', 'CampaignID = ?', [$id]) > 0;
    }
    
    /**
     * Clone a campaign
     */
    public function clone(int $id): ?int
    {
        $campaign = $this->find($id);
        if (!$campaign) {
            return null;
        }
        
        // Remove ID and stats-related fields
        unset($campaign['CampaignID']);
        
        // Update name and key
        $campaign['CampaignName'] = $campaign['CampaignName'] . ' (Copy)';
        $campaign['CampaignKey'] = $this->generateCampaignKey();
        $campaign['DateAdded'] = date('Y-m-d H:i:s');
        $campaign['ModifyDate'] = null;
        
        return $this->db->insert('campaigns', $campaign);
    }
    
    /**
     * Toggle campaign active status
     */
    public function toggleActive(int $id): bool
    {
        $campaign = $this->find($id);
        if (!$campaign) {
            return false;
        }
        
        return $this->update($id, ['Active' => $campaign['Active'] ? 0 : 1]);
    }
    
    /**
     * Generate unique campaign key
     */
    private function generateCampaignKey(): string
    {
        do {
            // Generate 8-character alphanumeric key
            $key = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        } while ($this->findByKey($key) !== null);
        
        return $key;
    }
    
    /**
     * Get campaign destinations (landing pages)
     */
    public function getDestinations(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT d.*, p.PredefLPName, p.PredefLPURL 
             FROM destinations d
             LEFT JOIN predeflps p ON d.PredefLPID = p.PredefLPID
             WHERE d.CampaignID = ?
             ORDER BY d.Weight DESC",
            [$campaignId]
        );
    }
    
    /**
     * Get campaign offers
     */
    public function getOffers(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT o.*, po.PredefOfferName, po.PredefOfferURL, a.Affiliate
             FROM offers o
             LEFT JOIN predefoffers po ON o.PredefOfferID = po.PredefOfferID
             LEFT JOIN affiliatesources a ON o.AffiliateSourceID = a.AffiliateSourceID
             WHERE o.CampaignID = ?
             ORDER BY o.Weight DESC",
            [$campaignId]
        );
    }
    
    /**
     * Save campaign destinations
     */
    public function saveDestinations(int $campaignId, array $destinations): void
    {
        // Delete existing
        $this->db->delete('destinations', 'CampaignID = ?', [$campaignId]);
        
        // Insert new
        foreach ($destinations as $dest) {
            $dest['CampaignID'] = $campaignId;
            $this->db->insert('destinations', $dest);
        }
    }
    
    /**
     * Save campaign offers
     */
    public function saveOffers(int $campaignId, array $offers): void
    {
        // Delete existing
        $this->db->delete('offers', 'CampaignID = ?', [$campaignId]);
        
        // Insert new
        foreach ($offers as $offer) {
            $offer['CampaignID'] = $campaignId;
            $this->db->insert('offers', $offer);
        }
    }
    
    /**
     * Get campaign stats for a date range
     */
    public function getStats(int $campaignId, string $startDate, string $endDate): array
    {
        return $this->db->fetchAll(
            "SELECT 
                DateInterval as date,
                Views as views,
                Clicks as clicks,
                Conversion as conversions,
                Revenue as revenue,
                Cost as cost,
                (Revenue - Cost) as profit
             FROM cachecampaign
             WHERE CampaignID = ? AND DateInterval BETWEEN ? AND ?
             ORDER BY DateInterval ASC",
            [$campaignId, $startDate, $endDate]
        );
    }
}

