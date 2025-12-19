<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Review {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getById($revId) {
        $sql = "SELECT * FROM review WHERE ReviewID = :ReviewID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ReviewID', (int)$revId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByReqID($reqID) {
        $stmt = $this->db->prepare("
            SELECT * FROM review 
            WHERE ReqID = :ReqID 
            ORDER BY Tanggal DESC 
            LIMIT 1
        ");
        $stmt->execute([':ReqID' => $reqID]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $sql = "INSERT INTO review (ReqID, NIK, Komentar, Status,
            	Tanggal) VALUES (:ReqID, :NIK, :Komentar, :Status,
            	:Tanggal)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ReqID' => $data['ReqID'],
            ':NIK' => $data['NIK'],
            ':Komentar' => $data['Komentar'],
            ':Status' => $data['Status'],
            ':Tanggal' => $data['Tanggal']
        ]);
    }

    public function update($revId, $data) {
        $sql = "UPDATE review 
                SET Tanggal = :Tanggal,
                Komentar = :Komentar,
                Status = :Status
                WHERE ReviewID = :ReviewID";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':Komentar' => $data['Komentar'],
            ':Status' => $data['Status'],
            ':Tanggal' => $data['Tanggal'],
            ':ReviewID'   => (int)$revId
        ]);
    }

    public function delete($revId) {
        $sql = "DELETE FROM review WHERE ReviewID = :ReviewID";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ReviewID' => (int)$revId
        ]);
    }
}