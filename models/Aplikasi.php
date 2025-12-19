<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Aplikasi {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM aplikasi WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (NamaApk LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $sql .= " ORDER BY ApkID DESC";

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

    public function getById($apkID) {
        $sql = "SELECT * FROM aplikasi WHERE ApkID = :ApkID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ApkID', (int)$apkID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM aplikasi WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (NamaApk LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO aplikasi (NamaApk)
                VALUES (:NamaApk)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NamaApk' => $data['NamaApk']
        ]);
    }

    public function update($apkID, $data) {
        $sql = "UPDATE aplikasi 
                SET NamaApk = :NamaApk
                WHERE ApkID = :ApkID";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NamaApk' => $data['NamaApk'],
            ':ApkID'   => (int)$apkID
        ]);
    }

    public function delete($apkID) {
        $sql = "DELETE FROM aplikasi WHERE ApkID = :ApkID";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ApkID' => (int)$apkID
        ]);
    }

    public function existsByName($namaApk, $excludeId = null) {
        $sql = "SELECT COUNT(*) FROM aplikasi WHERE NamaApk = :NamaApk";
        $params = [
            ':NamaApk' => $namaApk
        ];

        if (!empty($excludeId)) {
            $sql .= " AND ApkID != :ApkID";
            $params[':ApkID'] = (int)$excludeId;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }
}
