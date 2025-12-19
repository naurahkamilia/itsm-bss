<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM users WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                name LIKE :s1 OR 
                role LIKE :s2 OR 
                email LIKE :s3 OR
                NIK LIKE :s4 OR
                status LIKE :s5
            )";

            $search = '%' . $filters['search'] . '%';
            $params[':s1'] = $search;
            $params[':s2'] = $search;
            $params[':s3'] = $search;
            $params[':s4'] = $search;
            $params[':s5'] = $search;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM users WHERE 1=1";
        $params = [];


        if (!empty($filters['search'])) {
        $sql .= " AND (
            name LIKE :s1 OR 
            role LIKE :s2 OR 
            email LIKE :s3 OR
            NIK LIKE :s4 OR
            status LIKE :s5
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
        $params[':s3'] = $search;
        $params[':s4'] = $search;
        $params[':s5'] = $search;
    }

        $sql .= " ORDER BY NIK DESC";

        if (isset($filters['limit']) && isset($filters['offset'])) {
            $sql .= " LIMIT :offset, :limit";
        }

        $stmt = $this->db->prepare($sql);

        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        if (isset($filters['limit']) && isset($filters['offset'])) {
            $stmt->bindValue(':offset', (int)$filters['offset'], PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$filters['limit'], PDO::PARAM_INT);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAdmins() {
        $stmt = $this->db->prepare("
            SELECT NIK, email
            FROM users
            WHERE role = 'admin'
            AND status = 'aktif'
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();
    }

    public function getByNik($nik) {
        $sql = "SELECT * FROM users WHERE NIK = :nik";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':nik' => $nik]);
        return $stmt->fetch();
    }

    public function create($data) {
        $sql = "INSERT INTO users 
                (NIK, name, email, password, role, status)
                VALUES 
                (:NIK, :name, :email, :password, :role, :status)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NIK'      => $data['NIK'],
            ':name'     => $data['name'],
            ':email'    => $data['email'],
            ':password' => Security::hashPassword($data['password']),
            ':role'     => $data['role'] ?? 'customer',
            ':status'   => $data['status'] ?? 'aktif'
        ]);
    }

    public function update($nik, $data) {
        $fields = [];
        $params = [':nik' => $nik];

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

        if (isset($data['role'])) {
            $fields[] = "role = :role";
            $params[':role'] = $data['role'];
        }

        if (isset($data['status'])) {
            $fields[] = "status = :status";
            $params[':status'] = $data['status'];
        }

        if (empty($fields)) return false;

        $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE NIK = :nik";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function delete($nik) {
        $sql = "DELETE FROM users WHERE NIK = :nik";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':nik' => $nik]);
    }

    public function verifyLogin($email, $password) {
        $user = $this->getByEmail($email);

        if (!$user) return false;
        if (!Security::verifyPassword($password, $user['password'])) return false;

        return $user;
    }


    public function generateResetToken($email) {
        $token = Security::generateRandomString(64);
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));

        $sql = "UPDATE users 
                SET reset_token = :token, reset_token_expires = :expires 
                WHERE email = :email";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':token'   => $token,
            ':expires' => $expires,
            ':email'   => $email
        ]);

        return $token;
    }

    public function verifyResetToken($token) {
        $sql = "SELECT * FROM users 
                WHERE reset_token = :token 
                AND reset_token_expires > NOW()";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch();
    }

    public function resetPassword($token, $newPassword) {
        $user = $this->verifyResetToken($token);
        if (!$user) return false;

        $sql = "UPDATE users SET 
                password = :password,
                reset_token = NULL,
                reset_token_expires = NULL
                WHERE NIK = :nik";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':password' => Security::hashPassword($newPassword),
            ':nik'      => $user['NIK']
        ]);
    }
}
