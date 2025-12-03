<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Landing Page Model (predeflps table)
 */
class LandingPage
{
    private Database $db;
    
    public function __construct()
    {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all landing pages
     */
    public function getAll(): array
    {
        return $this->db->fetchAll(
            "SELECT * FROM predeflps ORDER BY PredefLPName ASC"
        );
    }
    
    /**
     * Get landing page by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM predeflps WHERE PredefLPID = ?",
            [$id]
        );
    }
    
    /**
     * Create a new landing page
     */
    public function create(array $data): int
    {
        $data['DateAdded'] = date('Y-m-d H:i:s');
        return $this->db->insert('predeflps', $data);
    }
    
    /**
     * Update a landing page
     */
    public function update(int $id, array $data): bool
    {
        return $this->db->update('predeflps', $data, 'PredefLPID = ?', [$id]) > 0;
    }
    
    /**
     * Delete a landing page
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('predeflps', 'PredefLPID = ?', [$id]) > 0;
    }
    
    /**
     * Get for dropdown select
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT PredefLPID as id, PredefLPName as name, PredefLPURL as url 
             FROM predeflps ORDER BY PredefLPName ASC"
        );
    }
}

