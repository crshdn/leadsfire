<?php

namespace LeadsFire\Services\ClickTracker;

use LeadsFire\Services\Database;
use LeadsFire\Services\Logger;

/**
 * Conversion Handler
 * 
 * Processes postback conversions from affiliate networks.
 */
class ConversionHandler
{
    private Database $db;
    private Logger $logger;
    private ClickTracker $clickTracker;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->logger = Logger::getInstance();
        $this->clickTracker = new ClickTracker();
    }
    
    /**
     * Process a conversion postback
     */
    public function processPostback(array $params): array
    {
        $this->logger->debug('Processing postback', $params);
        
        // Get subid (click ID)
        $subId = $params['subid'] ?? $params['clickid'] ?? $params['aff_sub'] ?? '';
        
        if (empty($subId)) {
            $this->logger->warning('Postback missing subid', $params);
            return ['success' => false, 'error' => 'Missing subid parameter'];
        }
        
        // Decode subid to get click ID
        $clickId = $this->clickTracker->decodeSubId($subId);
        
        if ($clickId <= 0) {
            $this->logger->warning('Invalid subid', ['subid' => $subId]);
            return ['success' => false, 'error' => 'Invalid subid'];
        }
        
        // Get the original click
        $click = $this->db->fetch(
            "SELECT * FROM clicks WHERE ClickID = ?",
            [$clickId]
        );
        
        if (!$click) {
            $this->logger->warning('Click not found', ['click_id' => $clickId]);
            return ['success' => false, 'error' => 'Click not found'];
        }
        
        // Check attribution window
        $attributionDays = config('app.tracking.attribution_days', 30);
        $clickDate = strtotime($click['ViewDate']);
        $maxDate = $clickDate + ($attributionDays * 86400);
        
        if (time() > $maxDate) {
            $this->logger->warning('Click outside attribution window', [
                'click_id' => $clickId,
                'click_date' => $click['ViewDate'],
                'attribution_days' => $attributionDays
            ]);
            return ['success' => false, 'error' => 'Click outside attribution window'];
        }
        
        // Get revenue/payout
        $revenue = $this->parseRevenue($params);
        
        // Get status
        $status = $this->parseStatus($params);
        
        // Get transaction ID
        $transactionId = $params['txid'] ?? $params['transaction_id'] ?? $params['tid'] ?? '';
        
        // Check for duplicate conversion
        if (!empty($transactionId)) {
            $existing = $this->db->fetch(
                "SELECT * FROM conversions WHERE TransactionID = ?",
                [$transactionId]
            );
            
            if ($existing) {
                $this->logger->info('Duplicate conversion ignored', [
                    'transaction_id' => $transactionId,
                    'click_id' => $clickId
                ]);
                return ['success' => true, 'message' => 'Duplicate conversion'];
            }
        }
        
        // Record the conversion
        $conversionData = [
            'ClickID' => $clickId,
            'CampaignID' => $click['CampaignID'],
            'SubIdID' => $click['SubIdID'],
            'DestinationID' => $click['DestinationID'],
            'OfferID' => $click['OfferID'],
            'ConversionDate' => date('Y-m-d H:i:s'),
            'Revenue' => $revenue,
            'Status' => $status,
            'TransactionID' => $transactionId ?: null,
            'Custom1' => $params['custom1'] ?? $params['aff_sub2'] ?? null,
            'Custom2' => $params['custom2'] ?? $params['aff_sub3'] ?? null,
            'Custom3' => $params['custom3'] ?? $params['aff_sub4'] ?? null,
            'Custom4' => $params['custom4'] ?? $params['aff_sub5'] ?? null,
            'Custom5' => $params['custom5'] ?? null,
            'IPAddress' => get_client_ip(),
        ];
        
        try {
            $this->db->insert('conversions', $conversionData);
        } catch (\Exception $e) {
            $this->logger->error('Failed to insert conversion', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => 'Database error'];
        }
        
        // Update cache tables
        $this->updateConversionCache($click, $revenue);
        
        // Also insert into cacheconversion for reporting
        $this->insertCacheConversion($click, $revenue);
        
        $this->logger->info('Conversion recorded', [
            'click_id' => $clickId,
            'campaign_id' => $click['CampaignID'],
            'revenue' => $revenue,
            'status' => $status,
            'transaction_id' => $transactionId
        ]);
        
        return [
            'success' => true,
            'click_id' => $clickId,
            'revenue' => $revenue,
            'status' => $status
        ];
    }
    
    /**
     * Parse revenue from various parameter formats
     */
    private function parseRevenue(array $params): float
    {
        // Try different parameter names
        $revenueKeys = ['revenue', 'payout', 'amount', 'commission', 'sum'];
        
        foreach ($revenueKeys as $key) {
            if (isset($params[$key])) {
                // Remove currency symbols and parse
                $value = preg_replace('/[^0-9.-]/', '', $params[$key]);
                return (float)$value;
            }
        }
        
        return 0.0;
    }
    
    /**
     * Parse status from parameters
     */
    private function parseStatus(array $params): string
    {
        $status = $params['status'] ?? $params['goal'] ?? $params['event'] ?? 'approved';
        
        // Normalize status
        $status = strtolower($status);
        
        // Map common status values
        $statusMap = [
            'approved' => 'approved',
            'pending' => 'pending',
            'rejected' => 'rejected',
            'reversed' => 'rejected',
            'chargeback' => 'rejected',
            'lead' => 'approved',
            'sale' => 'approved',
            '1' => 'approved',
            '0' => 'rejected',
        ];
        
        return $statusMap[$status] ?? 'approved';
    }
    
    /**
     * Update conversion cache tables
     */
    private function updateConversionCache(array $click, float $revenue): void
    {
        $today = date('Y-m-d');
        $campaignId = $click['CampaignID'];
        
        // Update campaign cache
        $this->db->query(
            "INSERT INTO cachecampaign (CampaignID, DateInterval, Conversion, Revenue) 
             VALUES (?, ?, 1, ?)
             ON DUPLICATE KEY UPDATE Conversion = Conversion + 1, Revenue = Revenue + ?",
            [$campaignId, $today, $revenue, $revenue]
        );
        
        // Update campaign totals
        $this->db->query(
            "UPDATE cachetotals SET 
                Conversion = Conversion + 1,
                Revenue = Revenue + ?,
                Profit = Revenue - Cost,
                ROI = CASE WHEN Cost > 0 THEN ((Revenue - Cost) / Cost) * 100 ELSE 0 END
             WHERE CampaignID = ?",
            [$revenue, $campaignId]
        );
    }
    
    /**
     * Insert into cacheconversion for detailed reporting
     */
    private function insertCacheConversion(array $click, float $revenue): void
    {
        try {
            $this->db->insert('cacheconversion', [
                'ClickID' => $click['ClickID'],
                'CampaignID' => $click['CampaignID'],
                'SubIdID' => $click['SubIdID'],
                'DestinationID' => $click['DestinationID'],
                'OfferID' => $click['OfferID'],
                'ReferrerID' => $click['ReferrerID'] ?? 1,
                'Extra1ID' => $click['Extra1ID'] ?? null,
                'Extra2ID' => $click['Extra2ID'] ?? null,
                'Extra3ID' => $click['Extra3ID'] ?? null,
                'Extra4ID' => $click['Extra4ID'] ?? null,
                'Extra5ID' => $click['Extra5ID'] ?? null,
                'Extra6ID' => $click['Extra6ID'] ?? null,
                'Extra7ID' => $click['Extra7ID'] ?? null,
                'Extra8ID' => $click['Extra8ID'] ?? null,
                'Extra9ID' => $click['Extra9ID'] ?? null,
                'Extra10ID' => $click['Extra10ID'] ?? null,
                'AdValueID' => $click['AdValueID'] ?? null,
                'DeviceID' => $click['DeviceID'] ?? 1,
                'IspID' => $click['IspID'] ?? 1,
                'ViewDate' => $click['ViewDate'],
                'ConversionDate' => date('Y-m-d H:i:s'),
                'Revenue' => $revenue,
                'IPBinary' => $click['IPBinary'] ?? null,
                'UserAgentID' => $click['UserAgentID'] ?? 1,
            ]);
        } catch (\Exception $e) {
            // Ignore duplicate key errors
        }
    }
}

