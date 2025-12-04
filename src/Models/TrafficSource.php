<?php

namespace LeadsFire\Models;

use LeadsFire\Services\Database;

/**
 * Traffic Source Model
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
            "SELECT * FROM traffic_sources ORDER BY name ASC"
        );
    }
    
    /**
     * Get active traffic sources for select dropdown
     */
    public function getForSelect(): array
    {
        return $this->db->fetchAll(
            "SELECT id, name FROM traffic_sources WHERE is_active = 1 ORDER BY name ASC"
        );
    }
    
    /**
     * Get traffic source by ID
     */
    public function find(int $id): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM traffic_sources WHERE id = ?",
            [$id]
        );
    }
    
    /**
     * Get traffic source by slug
     */
    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            "SELECT * FROM traffic_sources WHERE slug = ?",
            [$slug]
        );
    }
    
    /**
     * Create a new traffic source
     */
    public function create(array $data): int
    {
        $data['slug'] = $this->generateSlug($data['name']);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('traffic_sources', $data);
    }
    
    /**
     * Update a traffic source
     */
    public function update(int $id, array $data): bool
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('traffic_sources', $data, 'id = ?', [$id]) > 0;
    }
    
    /**
     * Delete a traffic source
     */
    public function delete(int $id): bool
    {
        return $this->db->delete('traffic_sources', 'id = ?', [$id]) > 0;
    }
    
    /**
     * Generate URL-safe slug from name
     */
    private function generateSlug(string $name): string
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Ensure uniqueness
        $baseSlug = $slug;
        $counter = 1;
        while ($this->findBySlug($slug) !== null) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
}
