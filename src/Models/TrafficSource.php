<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Traffic Source Model (cpvsources table)
 */
class TrafficSource
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all traffic sources
     */
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM cpvsources ORDER BY CPVSource ASC"
        );
    }
    
    /**
     * Get traffic source by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM cpvsources WHERE CPVSourceID = ?",
            [$id]
        );
    }
    
    /**
     * Create a new traffic source
     */
    public function create(array $data): int
    {
        $data['DateAdded'] = date('Y-m-d H:i:s');
        return $this->db->insert('cpvsources', $data);
    }
    
    /**
     * Update a traffic source
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update('cpvsources', $data, 'CPVSourceID = ?', [$id]) > 0;
    }
    
    /**
     * Delete a traffic source
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('cpvsources', 'CPVSourceID = ?', [$id]) > 0;
    }
    
    /**
     * Get for dropdown select
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT CPVSourceID as id, CPVSource as name FROM cpvsources ORDER BY CPVSource ASC"
        );
    }
}

