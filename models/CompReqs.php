<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Comp {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM request_completion WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (TglSelesai LIKE :search OR Catatan LIKE :search OR ComptID LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $sql .= " ORDER BY ComptID DESC";

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

    public function getById($CID) {
        $sql = "SELECT * FROM request_completion WHERE ComptID = :ComptID";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ComptID', (int)$CID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByReqID($reqID) {
    $sql = "SELECT * FROM request_completion WHERE ReqID = :ReqID";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':ReqID', (int)$reqID, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateByReqID($reqID, $data) {
    $sql = "UPDATE request_completion SET 
                Catatan = :Catatan, 
                EstWaktu = :EstWaktu, 
                TglSelesai = :TglSelesai 
            WHERE ReqID = :ReqID";
    $stmt = $this->db->prepare($sql);

    // pastikan semua key ada
    $params = [
        ':Catatan'    => $data['Catatan'] ?? null,
        ':EstWaktu'   => $data['EstWaktu'] ?? null,
        ':TglSelesai' => $data['TglSelesai'] ?? date('Y-m-d H:i:s'),
        ':ReqID'      => $reqID
    ];

    return $stmt->execute($params);
}

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM request_completion WHERE 1=1";
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (TglSelesai LIKE :search OR Catatan LIKE :search OR ComptID LIKE :search)";
            $params[':search'] = "%" . $filters['search'] . "%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO request_completion (ReqID, NIK, TglSelesai, Catatan,
            	EstWaktu) VALUES (:ReqID, :NIK, :TglSelesai, :Catatan,
                :EstWaktu)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ReqID' => $data['ReqID'],
            ':NIK' => $data['NIK'],
            ':TglSelesai' => $data['TglSelesai'],
            ':Catatan' => $data['Catatan'],
            ':EstWaktu' => $data['EstWaktu']
        ]);
    }

    public function update($CID, $data)
    {
        $sql = "UPDATE request_completion 
                SET TglSelesai = :TglSelesai,
                    Catatan = :Catatan,
                    EstWaktu = :EstWaktu
                WHERE ComptID = :ComptID";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':TglSelesai' => $data['TglSelesai'],
            ':Catatan'    => $data['Catatan'],
            ':EstWaktu'   => $data['EstWaktu'],
            ':ComptID'    => (int)$CID
        ]);
    }

    public function delete($CID) {
        $sql = "DELETE FROM request_completion WHERE ComptID = :ComptID";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ComptID' => (int)$CID
        ]);
    }

    public function startWork($reqID, $NIK){
        $sql = "INSERT INTO request_completion (ReqID, NIK, MulaiKerja)
                VALUES (:ReqID, :NIK, NOW())
                ON DUPLICATE KEY UPDATE MulaiKerja = NOW()";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ReqID' => $reqID,
            ':NIK'   => $NIK
        ]);
    }

    public function setEstimasi($reqID, $estJam){
    if (!$estJam || $estJam <= 0) {
        return false; 
    }

    $sql = "
        UPDATE request_completion
        SET EstWaktu = :EstWaktu,
            Deadline = DATE_ADD(MulaiKerja, INTERVAL :estJam HOUR)
        WHERE ReqID = :ReqID
    ";

    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(':EstWaktu', (int)$estJam, PDO::PARAM_INT);
    $stmt->bindValue(':estJam', (int)$estJam, PDO::PARAM_INT); // pakai placeholder
    $stmt->bindValue(':ReqID', (int)$reqID, PDO::PARAM_INT);

    return $stmt->execute();
}

   public function startRevision($reqID, $estJam){
    if ($estJam <= 0) return false;

    $sql = "
        UPDATE request_completion
        SET 
            EstWaktu   = :est,
            MulaiKerja = NOW(),
            Deadline   = DATE_ADD(NOW(), INTERVAL :est HOUR),
            TglSelesai = NULL,
            Status     = 'Revision'
        WHERE ReqID = :reqID
    ";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        'est'   => (int)$estJam,
        'reqID' => (int)$reqID
    ]);
}

    public function getExpiredJobs(){
        $sql = "
            SELECT r.ReqID
            FROM requests r
            JOIN request_completion c ON r.ReqID = c.ReqID
            WHERE r.StatusReq = 'Antrian'
            AND c.Deadline IS NOT NULL
            AND c.Deadline < NOW()
        ";

        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }
}