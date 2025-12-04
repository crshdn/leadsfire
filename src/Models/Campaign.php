<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Campaign Model
 * 
 * Handles all campaign-related database operations
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
                c.id,
                c.name,
                c.key_code,
                c.is_active,
                c.created_at,
                c.updated_at,
                c.traffic_source_id,
                c.cost_model,
                c.cost_value,
                c.rotation_type,
                c.engage_seconds,
                ts.name as traffic_source_name,
                COALESCE(sd.views, 0) as views,
                COALESCE(sd.unique_views, 0) as unique_views,
                COALESCE(sd.actions, 0) as actions,
                COALESCE(sd.conversions, 0) as conversions,
                COALESCE(sd.revenue, 0) as revenue,
                COALESCE(sd.cost, 0) as cost,
                COALESCE(sd.revenue - sd.cost, 0) as profit
            FROM campaigns c
            LEFT JOIN traffic_sources ts ON c.traffic_source_id = ts.id
            LEFT JOIN (
                SELECT campaign_id, 
                       SUM(views) as views,
                       SUM(unique_views) as unique_views,
                       SUM(actions) as actions,
                       SUM(conversions) as conversions,
                       SUM(revenue) as revenue,
                       SUM(cost) as cost
                FROM stats_daily
                GROUP BY campaign_id
            ) sd ON c.id = sd.campaign_id
            WHERE 1=1
        ";
        
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND c.is_active = ?";
            $params[] = $filters['status'] === 'active' ? 1 : 0;
        }
        
        if (!empty($filters['traffic_source_id'])) {
            $sql .= " AND c.traffic_source_id = ?";
            $params[] = $filters['traffic_source_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.name LIKE ? OR c.key_code LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        $sql .= " ORDER BY c.created_at DESC";
        
        if (!empty($filters['limit'])) {
            $offset = !empty($filters['offset']) ? (int)$filters['offset'] : 0;
            $sql .= " LIMIT " . (int)$filters['limit'] . " OFFSET " . $offset;
        }
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count all campaigns (for pagination)
     */
    public function countAll(array $filters = []): int
    {
        $sql = "SELECT COUNT(*) FROM campaigns c WHERE 1=1";
        $params = [];
        
        if (isset($filters['status'])) {
            $sql .= " AND c.is_active = ?";
            $params[] = $filters['status'] === 'active' ? 1 : 0;
        }
        
        if (!empty($filters['traffic_source_id'])) {
            $sql .= " AND c.traffic_source_id = ?";
            $params[] = $filters['traffic_source_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND (c.name LIKE ? OR c.key_code LIKE ?)";
            $params[] = '%' . $filters['search'] . '%';
            $params[] = '%' . $filters['search'] . '%';
        }
        
        return (int)$this->db->fetchColumn($sql, $params);
    }
    
    /**
     * Get campaign by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM campaigns WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Get campaign by key code
     */
    public function findByKey(string $key): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM campaigns WHERE key_code = ?",
            [$key]
        );
    }
    
    /**
     * Create a new campaign
     */
    public function create(array $data): int
    {
        $data['key_code'] = $this->generateKeyCode();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('campaigns', $data);
    }
    
    /**
     * Update a campaign
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('campaigns', $data, 'id = ?', [$id]) > 0;
    }
    
    /**
     * Delete a campaign
     */
    public function delete(int $id): bool
    {
        // Delete associated paths first
        $this->db->delete('campaign_paths', 'campaign_id = ?', [$id]);
        return $this->db->delete('campaigns', 'id = ?', [$id]) > 0;
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
        
        // Prepare new campaign data
        $newCampaign = [
            'name' => $campaign['name'] . ' (Copy)',
            'key_code' => $this->generateKeyCode(),
            'traffic_source_id' => $campaign['traffic_source_id'],
            'cost_model' => $campaign['cost_model'],
            'cost_value' => $campaign['cost_value'],
            'redirect_type' => $campaign['redirect_type'],
            'rotation_type' => $campaign['rotation_type'],
            'engage_seconds' => $campaign['engage_seconds'],
            'tracking_domain' => $campaign['tracking_domain'],
            'group_id' => $campaign['group_id'],
            'is_active' => 0, // Start as inactive
            'notes' => $campaign['notes'],
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
        $newId = $this->db->insert('campaigns', $newCampaign);
        
        // Clone paths
        $paths = $this->getPaths($id);
        foreach ($paths as $path) {
            unset($path['id']);
            $path['campaign_id'] = $newId;
            $path['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('campaign_paths', $path);
        }
        
        return $newId;
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
        
        return $this->update($id, ['is_active' => $campaign['is_active'] ? 0 : 1]);
    }
    
    /**
     * Generate unique key code
     */
    private function generateKeyCode(): string
    {
        do {
            // Generate 8-character alphanumeric key
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $key = '';
            for ($i = 0; $i < 8; $i++) {
                $key .= $chars[random_int(0, strlen($chars) - 1)];
            }
        } while ($this->findByKey($key) !== null);
        
        return $key;
    }
    
    /**
     * Get campaign paths (landing pages and offers)
     */
    public function getPaths(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT cp.*, 
                    lp.name as landing_page_name, lp.url as landing_page_url,
                    o.name as offer_name, o.url as offer_url, o.payout as offer_payout
             FROM campaign_paths cp
             LEFT JOIN landing_pages lp ON cp.landing_page_id = lp.id
             LEFT JOIN offers o ON cp.offer_id = o.id
             WHERE cp.campaign_id = ?
             ORDER BY cp.path_type, cp.position",
            [$campaignId]
        );
    }
    
    /**
     * Get landing page paths for a campaign
     */
    public function getLandingPaths(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT cp.*, lp.name, lp.url
             FROM campaign_paths cp
             LEFT JOIN landing_pages lp ON cp.landing_page_id = lp.id
             WHERE cp.campaign_id = ? AND cp.path_type = 'landing' AND cp.is_active = 1
             ORDER BY cp.position",
            [$campaignId]
        );
    }
    
    /**
     * Get offer paths for a campaign
     */
    public function getOfferPaths(int $campaignId): array
    {
        return $this->db->fetchAll(
            "SELECT cp.*, o.name, o.url, o.payout
             FROM campaign_paths cp
             LEFT JOIN offers o ON cp.offer_id = o.id
             WHERE cp.campaign_id = ? AND cp.path_type = 'offer' AND cp.is_active = 1
             ORDER BY cp.position",
            [$campaignId]
        );
    }
    
    /**
     * Save campaign paths
     */
    public function savePaths(int $campaignId, array $paths): void
    {
        // Delete existing paths
        $this->db->delete('campaign_paths', 'campaign_id = ?', [$campaignId]);
        
        // Insert new paths
        foreach ($paths as $path) {
            $path['campaign_id'] = $campaignId;
            $path['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert('campaign_paths', $path);
        }
    }
    
    /**
     * Get campaign stats for a date range
     */
    public function getStats(int $campaignId, ?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    date,
                    views,
                    unique_views,
                    engagements,
                    actions,
                    conversions,
                    revenue,
                    cost,
                    (revenue - cost) as profit
                FROM stats_daily
                WHERE campaign_id = ?";
        
        $params = [$campaignId];
        
        if ($startDate && $endDate) {
            $sql .= " AND date BETWEEN ? AND ?";
            $params[] = $startDate;
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get total stats for a campaign
     */
    public function getTotalStats(int $campaignId): array
    {
        $result = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(views), 0) as views,
                COALESCE(SUM(unique_views), 0) as unique_views,
                COALESCE(SUM(engagements), 0) as engagements,
                COALESCE(SUM(actions), 0) as actions,
                COALESCE(SUM(conversions), 0) as conversions,
                COALESCE(SUM(revenue), 0) as revenue,
                COALESCE(SUM(cost), 0) as cost
             FROM stats_daily
             WHERE campaign_id = ?",
            [$campaignId]
        );
        
        if ($result) {
            $result['profit'] = $result['revenue'] - $result['cost'];
            $result['roi'] = $result['cost'] > 0 ? (($result['revenue'] - $result['cost']) / $result['cost']) * 100 : 0;
            $result['epc'] = $result['views'] > 0 ? $result['revenue'] / $result['views'] : 0;
            $result['cvr'] = $result['views'] > 0 ? ($result['conversions'] / $result['views']) * 100 : 0;
        }
        
        return $result ?: [
            'views' => 0, 'unique_views' => 0, 'engagements' => 0, 'actions' => 0,
            'conversions' => 0, 'revenue' => 0, 'cost' => 0, 'profit' => 0,
            'roi' => 0, 'epc' => 0, 'cvr' => 0
        ];
    }
}
