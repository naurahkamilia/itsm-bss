<?php
/**
 * User Model
 */

defined('APP_ACCESS') or die('Direct access not permitted');

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new user
     */
    public function create($data) {
        $sql = "INSERT INTO users (name, email, password, role) 
                VALUES (:name, :email, :password, :role)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':name' => $data['name'],
            ':email' => $data['email'],
            ':password' => Security::hashPassword($data['password']),
            ':role' => $data['role'] ?? 'customer'
        ]);
    }
    
    /**
     * Update user
     */
    public function update($id, $data) {
        $fields = [];
        $params = [':id' => $id];
        
        if (isset($data['name'])) {
            $fields[] = "name = :name";
            $params[':name'] = $data['name'];
        }
        
        if (isset($data['email'])) {
            $fields[] = "email = :email";
            $params[':email'] = $data['email'];
        }
        
        if (isset($data['password'])) {
            $fields[] = "password = :password";
            $params[':password'] = Security::hashPassword($data['password']);
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Verify login
     */
    public function verifyLogin($email, $password) {
        $user = $this->getByEmail($email);
        
        if (!$user) {
            return false;
        }
        
        if (!Security::verifyPassword($password, $user['password'])) {
            return false;
        }
        
        return $user;
    }
    
    /**
     * Generate reset token
     */
    public function generateResetToken($email) {
        $token = Security::generateRandomString(64);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        $sql = "UPDATE users SET reset_token = :token, reset_token_expires = :expires 
                WHERE email = :email";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':token' => $token,
            ':expires' => $expires,
            ':email' => $email
        ]);
        
        return $token;
    }
    
    /**
     * Verify reset token
     */
    public function verifyResetToken($token) {
        $sql = "SELECT * FROM users 
                WHERE reset_token = :token 
                AND reset_token_expires > NOW()";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }
    
    /**
     * Reset password
     */
    public function resetPassword($token, $newPassword) {
        $user = $this->verifyResetToken($token);
        
        if (!$user) {
            return false;
        }
        
        $sql = "UPDATE users SET 
                password = :password,
                reset_token = NULL,
                reset_token_expires = NULL
                WHERE id = :id";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => Security::hashPassword($newPassword),
            ':id' => $user['id']
        ]);
    }
}
