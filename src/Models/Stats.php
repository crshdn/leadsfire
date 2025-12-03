<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Stats Model - Dashboard and reporting statistics
 */
class Stats
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get today's summary stats
     */
    public function getTodayStats(): array
    {
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        
        // Today's stats
        $todayStats = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(Views), 0) as views,
                COALESCE(SUM(Clicks), 0) as clicks,
                COALESCE(SUM(Conversion), 0) as conversions,
                COALESCE(SUM(Revenue), 0) as revenue,
                COALESCE(SUM(Cost), 0) as cost
             FROM cachecampaign
             WHERE DateInterval = ?",
            [$today]
        ) ?: ['views' => 0, 'clicks' => 0, 'conversions' => 0, 'revenue' => 0, 'cost' => 0];
        
        // Yesterday's stats for comparison
        $yesterdayStats = $this->db->fetch(
            "SELECT 
                COALESCE(SUM(Views), 0) as views,
                COALESCE(SUM(Clicks), 0) as clicks,
                COALESCE(SUM(Conversion), 0) as conversions,
                COALESCE(SUM(Revenue), 0) as revenue,
                COALESCE(SUM(Cost), 0) as cost
             FROM cachecampaign
             WHERE DateInterval = ?",
            [$yesterday]
        ) ?: ['views' => 0, 'clicks' => 0, 'conversions' => 0, 'revenue' => 0, 'cost' => 0];
        
        // Calculate changes
        $todayStats['profit'] = $todayStats['revenue'] - $todayStats['cost'];
        $yesterdayStats['profit'] = $yesterdayStats['revenue'] - $yesterdayStats['cost'];
        
        $todayStats['views_change'] = $this->calculateChange($todayStats['views'], $yesterdayStats['views']);
        $todayStats['clicks_change'] = $this->calculateChange($todayStats['clicks'], $yesterdayStats['clicks']);
        $todayStats['conversions_change'] = $this->calculateChange($todayStats['conversions'], $yesterdayStats['conversions']);
        $todayStats['revenue_change'] = $todayStats['revenue'] - $yesterdayStats['revenue'];
        $todayStats['profit_change'] = $todayStats['profit'] - $yesterdayStats['profit'];
        
        return $todayStats;
    }
    
    /**
     * Get stats for date range
     */
    public function getDateRangeStats(string $startDate, string $endDate): array
    {
        return $this->db->fetchAll(
            "SELECT 
                DateInterval as date,
                COALESCE(SUM(Views), 0) as views,
                COALESCE(SUM(Clicks), 0) as clicks,
                COALESCE(SUM(Conversion), 0) as conversions,
                COALESCE(SUM(Revenue), 0) as revenue,
                COALESCE(SUM(Cost), 0) as cost,
                COALESCE(SUM(Revenue) - SUM(Cost), 0) as profit
             FROM cachecampaign
             WHERE DateInterval BETWEEN ? AND ?
             GROUP BY DateInterval
             ORDER BY DateInterval ASC",
            [$startDate, $endDate]
        );
    }
    
    /**
     * Get quick stats (counts)
     */
    public function getQuickStats(): array
    {
        $activeCampaigns = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM campaigns WHERE Active = 1"
        ) ?: 0;
        
        $totalCampaigns = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM campaigns"
        ) ?: 0;
        
        $trafficSources = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM cpvsources"
        ) ?: 0;
        
        $affiliateNetworks = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM affiliatesources"
        ) ?: 0;
        
        $landingPages = $this->db->fetchColumn(
            "SELECT COUNT(*) FROM predeflps"
        ) ?: 0;
        
        return [
            'active_campaigns' => (int)$activeCampaigns,
            'total_campaigns' => (int)$totalCampaigns,
            'traffic_sources' => (int)$trafficSources,
            'affiliate_networks' => (int)$affiliateNetworks,
            'landing_pages' => (int)$landingPages,
        ];
    }
    
    /**
     * Get top campaigns by profit
     */
    public function getTopCampaigns(int $limit = 10, string $startDate = null, string $endDate = null): array
    {
        $startDate = $startDate ?? date('Y-m-d', strtotime('-30 days'));
        $endDate = $endDate ?? date('Y-m-d');
        
        return $this->db->fetchAll(
            "SELECT 
                c.CampaignID,
                c.CampaignName,
                c.CampaignKey,
                c.Active,
                s.CPVSource as TrafficSource,
                COALESCE(SUM(cc.Views), 0) as Views,
                COALESCE(SUM(cc.Clicks), 0) as Clicks,
                COALESCE(SUM(cc.Conversion), 0) as Conversions,
                COALESCE(SUM(cc.Revenue), 0) as Revenue,
                COALESCE(SUM(cc.Cost), 0) as Cost,
                COALESCE(SUM(cc.Revenue) - SUM(cc.Cost), 0) as Profit,
                CASE WHEN SUM(cc.Cost) > 0 
                     THEN ((SUM(cc.Revenue) - SUM(cc.Cost)) / SUM(cc.Cost)) * 100 
                     ELSE 0 END as ROI
             FROM campaigns c
             LEFT JOIN cpvsources s ON c.CPVSourceID = s.CPVSourceID
             LEFT JOIN cachecampaign cc ON c.CampaignID = cc.CampaignID 
                AND cc.DateInterval BETWEEN ? AND ?
             GROUP BY c.CampaignID
             ORDER BY Profit DESC
             LIMIT ?",
            [$startDate, $endDate, $limit]
        );
    }
    
    /**
     * Get hourly stats for today
     */
    public function getHourlyStats(): array
    {
        $today = date('Y-m-d');
        
        // This would require a more granular cache table
        // For now, return empty array - can be implemented later
        return [];
    }
    
    /**
     * Calculate percentage change
     */
    private function calculateChange($current, $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        return round((($current - $previous) / $previous) * 100, 1);
    }
}

