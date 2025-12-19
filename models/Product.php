<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * GET ALL PRODUCTS (WITH FILTER)
     */
    public function getAll($filters = []) {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";

        $params = [];

        // CATEGORY FILTER
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        // SEARCH FILTER (FIXED)
        if (!empty($filters['search'])) {
            $sql .= " AND LOWER(p.name) LIKE :search";
            $params[':search'] = "%" . strtolower($filters['search']) . "%";
        }

        // STATUS FILTER
        if (isset($filters['is_active'])) {
            $sql .= " AND p.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }

        // SORTING
        $orderBy = "p.id DESC";
        if (!empty($filters['sort'])) {
            switch ($filters['sort']) {
                case 'price-low':  $orderBy = "p.price ASC"; break;
                case 'price-high': $orderBy = "p.price DESC"; break;
                case 'stock-low':  $orderBy = "p.stock ASC"; break;
                case 'stock-high': $orderBy = "p.stock DESC"; break;
                case 'name-asc':   $orderBy = "p.name ASC"; break;
                case 'name-desc':  $orderBy = "p.name DESC"; break;
            }
        }

        $sql .= " ORDER BY $orderBy";

        // PAGINATION
        if (!empty($filters['limit'])) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }

        $stmt = $this->db->prepare($sql);

        // BIND NORMAL PARAMETERS
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        // BIND PAGINATION
        if (!empty($filters['limit'])) {
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * COUNT PRODUCTS (FIXED)
     */
    public function count($filters = []) {
        $sql = "SELECT COUNT(*) AS total
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE 1=1";

        $params = [];

        // CATEGORY FILTER
        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = :category_id";
            $params[':category_id'] = $filters['category_id'];
        }

        // SEARCH FILTER (FIXED)
        if (!empty($filters['search'])) {
            $sql .= " AND LOWER(p.name) LIKE :search";
            $params[':search'] = "%" . strtolower($filters['search']) . "%";
        }

        // STATUS FILTER
        if (isset($filters['is_active'])) {
            $sql .= " AND p.is_active = :is_active";
            $params[':is_active'] = $filters['is_active'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetch()['total'];
    }

    /**
     * GET BY ID
     */
    public function getById($id) {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.id = :id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    /**
     * GET BY SLUG
     */
    public function getBySlug($slug) {
        $sql = "SELECT p.*, c.name AS category_name
                FROM products p
                LEFT JOIN categories c ON p.category_id = c.id
                WHERE p.slug = :slug";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch();
    }

    /**
     * CREATE PRODUCT
     */
    public function create($data) {
        $sql = "INSERT INTO products 
                (category_id, name, slug, description, price, discount_price, stock, is_active, image)
                VALUES 
                (:category_id, :name, :slug, :description, :price, :discount_price, :stock, :is_active, :image)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':category_id'    => $data['category_id'],
            ':name'           => $data['name'],
            ':slug'           => $this->generateSlug($data['name']),
            ':description'    => $data['description'],
            ':price'          => $data['price'],
            ':discount_price' => $data['discount_price'] ?? null,
            ':stock'          => $data['stock'],
            ':is_active'      => $data['is_active'] ?? 1,
            ':image'          => $data['image']
        ]);
    }

    /**
     * UPDATE PRODUCT
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

        // If new image uploaded
        if (!empty($data['image'])) {
            $sql .= ", image = :image";
        }

        $sql .= " WHERE id = :id";

        $params = [
            ':id'            => $id,
            ':category_id'   => $data['category_id'],
            ':name'          => $data['name'],
            ':description'   => $data['description'],
            ':price'         => $data['price'],
            ':discount_price'=> $data['discount_price'],
            ':stock'         => $data['stock'],
            ':is_active'     => $data['is_active']
        ];

        if (!empty($data['image'])) {
            $params[':image'] = $data['image'];
        }

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    /**
     * DELETE
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * TOGGLE ACTIVE
     */
    public function toggleActive($id) {
        $stmt = $this->db->prepare("UPDATE products SET is_active = NOT is_active WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    /**
     * GENERATE UNIQUE SLUG
     */
    private function generateSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');

        // CHECK DUPLICATE
        $original = $slug;
        $i = 1;

        while ($this->slugExists($slug)) {
            $slug = $original . '-' . $i;
            $i++;
        }

        return $slug;
    }

    private function slugExists($slug) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE slug = :slug");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetchColumn() > 0;
    }
}
