<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Landing Page Model
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
            "SELECT lp.*, g.name as group_name
             FROM landing_pages lp
             LEFT JOIN `groups` g ON lp.group_id = g.id AND g.type = 'landing_page'
             ORDER BY lp.name ASC"
        );
    }
    
    /**
     * Get active landing pages for select dropdown
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name, url FROM landing_pages WHERE is_active = 1 ORDER BY name ASC"
        );
    }
    
    /**
     * Get landing page by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM landing_pages WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Create a new landing page
     */
    public function create(array $data): int
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('landing_pages', $data);
    }
    
    /**
     * Update a landing page
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('landing_pages', $data, 'id = ?', [$id]) > 0;
    }
    
    /**
     * Delete a landing page
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('landing_pages', 'id = ?', [$id]) > 0;
    }
}
