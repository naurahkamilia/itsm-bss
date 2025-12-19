<?php
/**
 * Product Model
 * Handles all product-related database operations
 */

defined('APP_ACCESS') or die('Direct access not permitted');

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all products with optional filters
     */
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE 1=1";
        
        $params = [];
        
        // Filter by category
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        // Filter by search term
        if (!empty($filters['search'])) {
            $sql .= " AND p.name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        // Filter by active status
        if (isset($filters['is_active'])) {
            $sql .= " AND p.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }
        
        // Sorting
        $orderBy = 'p.created_at DESC';
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price-low':
                    $orderBy = 'p.price ASC';
                    break;
                case 'price-high':
                    $orderBy = 'p.price DESC';
                    break;
                case 'stock-high':
                    $orderBy = 'p.stock DESC';
                    break;
                case 'stock-low':
                    $orderBy = 'p.stock ASC';
                    break;
                case 'name-asc':
                    $orderBy = 'p.name ASC';
                    break;
                case 'name-desc':
                    $orderBy = 'p.name DESC';
                    break;
            }
        }
        $sql .= " ORDER BY " . $orderBy;
        
        // Pagination
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->db->prepare($sql);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        if (!empty($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)($filters['offset'] ?? 0), PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Count products with filters
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) as total FROM products WHERE 1=1";
        $params = [];
        
        if (!empty($filters['category_id'])) {
            $sql .= " AND category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }
        
        if (!empty($filters['search'])) {
            $sql .= " AND name LIKE :search";
            $params[':search'] = '%' . $filters['search'] . '%';
        }
        
        if (isset($filters['is_active'])) {
            $sql .= " AND is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch()['total'];
    }
    
    /**
     * Get product by ID
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.id = :id";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Get product by slug
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, c.name as category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.slug = :slug";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }
    
    /**
     * Create new product
     */
    public function create($data) {
        $sql = "INSERT INTO products (category_id, name, slug, description, price, 
                discount_price, stock, is_active, image) 
                VALUES (:category_id, :name, :slug, :description, :price, 
                :discount_price, :stock, :is_active, :image)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':slug' => $this->generateSlug($data['name']),
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':discount_price' => $data['discount_price'] ?? null,
            ':stock' => $data['stock'],
            ':is_active' => $data['is_active'] ?? 1,
            ':image' => $data['image'] ?? null
        ]);
    }
    
    /**
     * Update product
     */
    public function update($id, $data) {
        $sql = "UPDATE products SET 
                category_id = :category_id,
                name = :name,
                description = :description,
                price = :price,
                discount_price = :discount_price,
                stock = :stock,
                is_active = :is_active";
        
        if (!empty($data['image'])) {
            $sql .= ", image = :image";
        }
        
        $sql .= " WHERE id = :id";
        
        $params = [
            ':id' => $id,
            ':category_id' => $data['category_id'],
            ':name' => $data['name'],
            ':description' => $data['description'],
            ':price' => $data['price'],
            ':discount_price' => $data['discount_price'] ?? null,
            ':stock' => $data['stock'],
            ':is_active' => $data['is_active'] ?? 1
        ];
        
        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Delete product
     */
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Toggle product active status
     */
    public function toggleActive($id) {
        $sql = "UPDATE products SET is_active = NOT is_active WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }
    
    /**
     * Generate slug from name
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Check if slug exists
        $originalSlug = $slug;
        $counter = 1;
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug exists
     */
    private function slugExists($slug) {
        $sql = "SELECT COUNT(*) as count FROM products WHERE slug = :slug";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch()['count'] > 0;
    }
    
    /**
     * Get products by category
     */
    public function getByCategory($categoryId, $limit = null) {
        $sql = "SELECT * FROM products WHERE category_id = :category_id AND is_active = 1 
                ORDER BY created_at DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
        
        if ($limit) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
