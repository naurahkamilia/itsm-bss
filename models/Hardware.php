<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Hardware {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM hardware WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (NamaHw LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $sql .= " ORDER BY HwID DESC";

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

    public function getById($hwId) {
        $sql = "SELECT * FROM hardware WHERE HwID = :HwID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':HwID', (int)$hwId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM hardware WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (NamaHw LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO hardware (NamaHw)
                VALUES (:NamaHw)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NamaHw' => $data['NamaHw']
        ]);
    }

    public function update($hwId, $data) {
        $sql = "UPDATE hardware 
                SET NamaHw = :NamaHw
                WHERE HwID = :HwID";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NamaHw' => $data['NamaHw'],
            ':HwID'   => (int)$hwId
        ]);
    }

    public function delete($hwId) {
        $sql = "DELETE FROM hardware WHERE HwID = :HwID";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':HwID' => (int)$hwId
        ]);
    }

    public function existsByName($namaHw, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM hardware WHERE NamaHw = :NamaHw";
        $params = [
            ':NamaHw' => $namaHw
        ];

        if (!empty($excludeId)) {
            $sql .= " AND HwID != :HwID";
            $params[':HwID'] = (int)$excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
