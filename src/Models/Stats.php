<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Stats Model
 * 
 * Handles all statistics-related database operations
 */
class Stats
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get dashboard stats for a date range
     */
    public function getDashboardStats(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    COALESCE(SUM(views), 0) as total_views,
                    COALESCE(SUM(unique_views), 0) as total_unique_views,
                    COALESCE(SUM(actions), 0) as total_actions,
                    COALESCE(SUM(conversions), 0) as total_conversions,
                    COALESCE(SUM(revenue), 0) as total_revenue,
                    COALESCE(SUM(cost), 0) as total_cost
                FROM stats_daily";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " WHERE date BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $result = $this->db->fetch($sql, $params);
        
        if ($result) {
            $result['total_profit'] = $result['total_revenue'] - $result['total_cost'];
            $result['roi'] = $result['total_cost'] > 0 
                ? (($result['total_revenue'] - $result['total_cost']) / $result['total_cost']) * 100 
                : 0;
            $result['epc'] = $result['total_views'] > 0 
                ? $result['total_revenue'] / $result['total_views'] 
                : 0;
            $result['cvr'] = $result['total_views'] > 0 
                ? ($result['total_conversions'] / $result['total_views']) * 100 
                : 0;
        }
        
        return $result ?: [
            'total_views' => 0, 'total_unique_views' => 0, 'total_actions' => 0,
            'total_conversions' => 0, 'total_revenue' => 0, 'total_cost' => 0,
            'total_profit' => 0, 'roi' => 0, 'epc' => 0, 'cvr' => 0
        ];
    }
    
    /**
     * Get stats trend by day
     */
    public function getTrend(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    date,
                    SUM(views) as views,
                    SUM(conversions) as conversions,
                    SUM(revenue) as revenue,
                    SUM(cost) as cost,
                    SUM(revenue - cost) as profit
                FROM stats_daily";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " WHERE date BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $sql .= " GROUP BY date ORDER BY date ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get top campaigns by a metric
     */
    public function getTopCampaigns(int $limit = 10, string $metric = 'profit', ?string $startDate = null, ?string $endDate = null): array
    {
        $validMetrics = ['views', 'conversions', 'revenue', 'cost', 'profit'];
        if (!in_array($metric, $validMetrics)) {
            $metric = 'profit';
        }
        
        $orderBy = $metric === 'profit' ? '(SUM(sd.revenue) - SUM(sd.cost))' : "SUM(sd.$metric)";
        
        $sql = "SELECT 
                    c.id,
                    c.name,
                    c.key_code,
                    COALESCE(SUM(sd.views), 0) as views,
                    COALESCE(SUM(sd.conversions), 0) as conversions,
                    COALESCE(SUM(sd.revenue), 0) as revenue,
                    COALESCE(SUM(sd.cost), 0) as cost,
                    COALESCE(SUM(sd.revenue) - SUM(sd.cost), 0) as profit
                FROM campaigns c
                LEFT JOIN stats_daily sd ON c.id = sd.campaign_id";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " AND sd.date BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $sql .= " WHERE c.is_active = 1
                  GROUP BY c.id
                  ORDER BY $orderBy DESC
                  LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get stats by traffic source
     */
    public function getByTrafficSource(?string $startDate = null, ?string $endDate = null): array
    {
        $sql = "SELECT 
                    ts.id,
                    ts.name,
                    COALESCE(SUM(sd.views), 0) as views,
                    COALESCE(SUM(sd.conversions), 0) as conversions,
                    COALESCE(SUM(sd.revenue), 0) as revenue,
                    COALESCE(SUM(sd.cost), 0) as cost,
                    COALESCE(SUM(sd.revenue) - SUM(sd.cost), 0) as profit
                FROM traffic_sources ts
                LEFT JOIN campaigns c ON ts.id = c.traffic_source_id
                LEFT JOIN stats_daily sd ON c.id = sd.campaign_id";
        
        $params = [];
        
        if ($startDate && $endDate) {
            $sql .= " AND sd.date BETWEEN ? AND ?";
            $params = [$startDate, $endDate];
        }
        
        $sql .= " GROUP BY ts.id ORDER BY profit DESC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update daily stats cache for a campaign
     */
    public function updateDailyStats(int $campaignId, string $date): void
    {
        // Calculate stats from clicks table
        $stats = $this->db->fetch(
            "SELECT 
                COUNT(*) as views,
                SUM(is_unique) as unique_views,
                SUM(CASE WHEN engage_time IS NOT NULL THEN 1 ELSE 0 END) as engagements,
                SUM(CASE WHEN action_time IS NOT NULL THEN 1 ELSE 0 END) as actions,
                SUM(CASE WHEN conversion_time IS NOT NULL THEN 1 ELSE 0 END) as conversions,
                COALESCE(SUM(revenue), 0) as revenue,
                COALESCE(SUM(cost), 0) as cost
             FROM clicks
             WHERE campaign_id = ? AND DATE(view_time) = ?",
            [$campaignId, $date]
        );
        
        if ($stats) {
            $this->db->query(
                "INSERT INTO stats_daily (campaign_id, date, views, unique_views, engagements, actions, conversions, revenue, cost)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE
                    views = VALUES(views),
                    unique_views = VALUES(unique_views),
                    engagements = VALUES(engagements),
                    actions = VALUES(actions),
                    conversions = VALUES(conversions),
                    revenue = VALUES(revenue),
                    cost = VALUES(cost)",
                [
                    $campaignId, $date,
                    $stats['views'] ?? 0,
                    $stats['unique_views'] ?? 0,
                    $stats['engagements'] ?? 0,
                    $stats['actions'] ?? 0,
                    $stats['conversions'] ?? 0,
                    $stats['revenue'] ?? 0,
                    $stats['cost'] ?? 0
                ]
            );
        }
    }
    
    /**
     * Get today's real-time stats (from clicks table directly)
     */
    public function getTodayStats(): array
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Get today's stats
        $todayResult = $this->db->fetch(
            "SELECT 
                COUNT(*) as views,
                SUM(CASE WHEN conversion_time IS NOT NULL THEN 1 ELSE 0 END) as conversions,
                COALESCE(SUM(revenue), 0) as revenue,
                COALESCE(SUM(cost), 0) as cost
             FROM clicks
             WHERE DATE(view_time) = ?",
            [$today]
        );
        
        // Get yesterday's stats for comparison
        $yesterdayResult = $this->db->fetch(
            "SELECT 
                COUNT(*) as views,
                SUM(CASE WHEN conversion_time IS NOT NULL THEN 1 ELSE 0 END) as conversions,
                COALESCE(SUM(revenue), 0) as revenue,
                COALESCE(SUM(cost), 0) as cost
             FROM clicks
             WHERE DATE(view_time) = ?",
            [$yesterday]
        );
        
        $views = $todayResult['views'] ?? 0;
        $conversions = $todayResult['conversions'] ?? 0;
        $revenue = $todayResult['revenue'] ?? 0;
        $cost = $todayResult['cost'] ?? 0;
        $profit = $revenue - $cost;
        
        $yesterdayViews = $yesterdayResult['views'] ?? 0;
        $yesterdayConversions = $yesterdayResult['conversions'] ?? 0;
        $yesterdayRevenue = $yesterdayResult['revenue'] ?? 0;
        $yesterdayProfit = $yesterdayRevenue - ($yesterdayResult['cost'] ?? 0);
        
        return [
            'views' => $views,
            'clicks' => $views, // Alias for compatibility
            'conversions' => $conversions,
            'revenue' => $revenue,
            'cost' => $cost,
            'profit' => $profit,
            'views_change' => $yesterdayViews > 0 ? (($views - $yesterdayViews) / $yesterdayViews) * 100 : 0,
            'conversions_change' => $yesterdayConversions > 0 ? (($conversions - $yesterdayConversions) / $yesterdayConversions) * 100 : 0,
            'revenue_change' => $yesterdayRevenue > 0 ? (($revenue - $yesterdayRevenue) / $yesterdayRevenue) * 100 : 0,
            'profit_change' => $yesterdayProfit != 0 ? (($profit - $yesterdayProfit) / abs($yesterdayProfit)) * 100 : 0,
        ];
    }
    
    /**
     * Get quick stats (counts of various entities)
     */
    public function getQuickStats(): array
    {
        $activeCampaigns = $this->db->fetchColumn("SELECT COUNT(*) FROM campaigns WHERE is_active = 1");
        $totalCampaigns = $this->db->fetchColumn("SELECT COUNT(*) FROM campaigns");
        $trafficSources = $this->db->fetchColumn("SELECT COUNT(*) FROM traffic_sources WHERE is_active = 1");
        $affiliateNetworks = $this->db->fetchColumn("SELECT COUNT(*) FROM affiliate_networks WHERE is_active = 1");
        $landingPages = $this->db->fetchColumn("SELECT COUNT(*) FROM landing_pages WHERE is_active = 1");
        
        return [
            'active_campaigns' => (int)$activeCampaigns,
            'total_campaigns' => (int)$totalCampaigns,
            'traffic_sources' => (int)$trafficSources,
            'affiliate_networks' => (int)$affiliateNetworks,
            'landing_pages' => (int)$landingPages,
        ];
    }
    
    /**
     * Get stats for a date range (for charts)
     */
    public function getDateRangeStats(string $startDate, string $endDate): array
    {
        // First try from cache
        $cached = $this->db->fetchAll(
            "SELECT 
                date,
                SUM(views) as views,
                SUM(conversions) as conversions,
                SUM(revenue) as revenue,
                SUM(cost) as cost,
                SUM(revenue - cost) as profit
             FROM stats_daily
             WHERE date BETWEEN ? AND ?
             GROUP BY date
             ORDER BY date ASC",
            [$startDate, $endDate]
        );
        
        if (!empty($cached)) {
            return $cached;
        }
        
        // Fall back to clicks table
        return $this->db->fetchAll(
            "SELECT 
                DATE(view_time) as date,
                COUNT(*) as views,
                SUM(CASE WHEN conversion_time IS NOT NULL THEN 1 ELSE 0 END) as conversions,
                COALESCE(SUM(revenue), 0) as revenue,
                COALESCE(SUM(cost), 0) as cost,
                COALESCE(SUM(revenue) - SUM(cost), 0) as profit
             FROM clicks
             WHERE DATE(view_time) BETWEEN ? AND ?
             GROUP BY DATE(view_time)
             ORDER BY date ASC",
            [$startDate, $endDate]
        );
    }
}
