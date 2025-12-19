<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Request {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
    $sql = "SELECT r.*, 
                   k.Nama,
                   a.NamaApk,
                   h.NamaHw
            FROM request r
            LEFT JOIN karyawan k ON r.NIK = k.NIK
            LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
            LEFT JOIN hardware h ON r.HwID = h.HwID
            WHERE 1=1";
    
    $params = [];

    // Filter search
    if (!empty($filters['search'])) {
        $sql .= " AND (
            r.Departemen LIKE :s1 OR 
            r.NIK LIKE :s2 OR 
            a.NamaApk LIKE :s3 OR
            h.NamaHw LIKE :s4 OR
            r.Request LIKE :s5 OR
            r.Tgl_request LIKE :s6 OR
            r.Prioritas LIKE :s7 OR
            k.Nama LIKE :s8
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
        $params[':s3'] = $search;
        $params[':s4'] = $search;
        $params[':s5'] = $search;
        $params[':s6'] = $search;
        $params[':s7'] = $search;
        $params[':s8'] = $search;
    }

    if (!empty($filters['StatusReq'])) {
        $sql .= " AND r.StatusReq = :status";
        $params[':status'] = $filters['StatusReq'];
    }

    if (!empty($filters['Prioritas'])) {
        $sql .= " AND r.Prioritas = :prio";
        $params[':prio'] = $filters['Prioritas'];
    }

    if (!empty($filters['tanggal_dari'])) {
        $sql .= " AND DATE(r.Tgl_request) >= :tgl_dari";
        $params[':tgl_dari'] = $filters['tanggal_dari'];
    }

    if (!empty($filters['tanggal_sampai'])) {
        $sql .= " AND DATE(r.Tgl_request) <= :tgl_sampai";
        $params[':tgl_sampai'] = $filters['tanggal_sampai'];
    }

    $sql .= " ORDER BY r.ReqID DESC";

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

    public function getByUser($nik, $filters = [])
    {
        $sql = "
            SELECT r.*, a.NamaApk, h.NamaHw
            FROM request r
            LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
            LEFT JOIN hardware h ON r.HwID = h.HwID
            WHERE r.NIK = :nik
        ";

        $params = [
            'nik' => $nik
        ];

         if (!empty($filters['search'])) {
        $sql .= " AND (
           a.NamaApk LIKE :s1 OR
            h.NamaHw LIKE :s2
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
    }

    if (!empty($filters['StatusReq'])) {
        $sql .= " AND r.StatusReq = :status";
        $params[':status'] = $filters['StatusReq'];
    }

    $sql .= " ORDER BY r.ReqID DESC";

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

    public function countByUser($nik, $filters = [])
    {
        $sql = "
            SELECT COUNT(*) 
            FROM request r
            LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
            LEFT JOIN hardware h ON r.HwID = h.HwID
            WHERE r.NIK = :nik
        ";

        $params = [
            ':nik' => $nik
        ];

        if (!empty($filters['search'])) {
        $sql .= " AND (
            a.NamaApk LIKE :s1 OR
            h.NamaHw LIKE :s2
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
    }

    // FILTER STATUS
    if (!empty($filters['StatusReq'])) {
        $sql .= " AND r.StatusReq = :status";
        $params[':status'] = $filters['StatusReq'];
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function getById($reqID) {
        $sql = "SELECT r.*,
                       u.name,
                       a.NamaApk,
                       h.NamaHw
                FROM request r
                LEFT JOIN users u ON r.NIK = u.NIK
                LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
                LEFT JOIN hardware h ON r.HwID = h.HwID
                WHERE r.ReqID = :ReqID";

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':ReqID', (int)$reqID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listReq($status, $limit = 5, $offset = 0){
        $stmt = $this->db->prepare("
            SELECT r.*, a.NamaApk, h.NamaHw FROM request r 
            LEFT JOIN aplikasi a ON r.ApkID = a.ApkID 
            LEFT JOIN hardware h ON r.HwID = h.HwID
            WHERE StatusReq = :status
            ORDER BY Tgl_request DESC
            LIMIT :limit OFFSET :offset
        ");

        $stmt->bindValue(':status', $status, PDO::PARAM_STR);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

public function count($filters = []) {
    $sql = "SELECT COUNT(*) AS total
            FROM request r
            LEFT JOIN karyawan k ON r.NIK = k.NIK
            LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
            LEFT JOIN hardware h ON r.HwID = h.HwID
            WHERE 1=1";

    $params = [];

    // FILTER SEARCH
    if (!empty($filters['search'])) {
        $sql .= " AND (
            r.Departemen LIKE :s1 OR 
            r.NIK LIKE :s2 OR 
            a.NamaApk LIKE :s3 OR
            h.NamaHw LIKE :s4 OR
            r.Request LIKE :s5 OR
            r.Tgl_request LIKE :s6 OR
            r.Prioritas LIKE :s7 OR 
            k.Nama LIKE :s8
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
        $params[':s3'] = $search;
        $params[':s4'] = $search;
        $params[':s5'] = $search;
        $params[':s6'] = $search;
        $params[':s7'] = $search;
        $params[':s8'] = $search;
    }

    // FILTER STATUS
    if (!empty($filters['StatusReq'])) {
        $sql .= " AND r.StatusReq = :status";
        $params[':status'] = $filters['StatusReq'];
    }

    // FILTER PRIORITAS
    if (!empty($filters['Prioritas'])) {
        $sql .= " AND r.Prioritas = :prio";
        $params[':prio'] = $filters['Prioritas'];
    }

    $stmt = $this->db->prepare($sql);
    $stmt->execute($params);

    return $stmt->fetchColumn();
}

    public function countByStatus($status){
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM request WHERE StatusReq = ?");
        $stmt->execute([$status]);
        return (int) $stmt->fetchColumn();
    }

    public function countToday(){
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM request WHERE DATE(Tgl_request) = CURDATE()");
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countByID($nik) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM request WHERE NIK = :NIK");
        $stmt->bindParam(':NIK', $nik);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    public function countByStatusnNik($status, $nik){
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM request WHERE StatusReq = ? AND NIK =?");
        $stmt->execute([$status, $nik]);
        return (int) $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO request 
                (NIK, ApkID, HwID, Departemen, Tgl_request, Prioritas, Request, Dokumentasi, StatusReq)
                VALUES 
                (:NIK, :ApkID, :HwID, :Departemen, :TglReq, :Prioritas, :Request, :Dokumentasi, :StatusReq)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NIK'        => $data['NIK'],
            ':ApkID'      => !empty($data['ApkID']) ? (int)$data['ApkID'] : null,
            ':HwID'       => !empty($data['HwID']) ? (int)$data['HwID'] : null,
            ':Departemen'=> $data['Departemen'],
            ':TglReq'    => $data['Tgl_request'],
            ':Prioritas' => $data['Prioritas'],
            ':Request'   => $data['Request'],
            ':Dokumentasi' => $data['Dokumentasi'],
            ':StatusReq' => $data['StatusReq']
        ]);
    }

    public function update($reqID, $data) {
        $sql = "UPDATE request SET 
                    ApkID = :ApkID,
                    HwID = :HwID,
                    Prioritas = :Prioritas,
                    Request = :Request,
                    Dokumentasi = :Dokumentasi,
                    StatusReq = :StatusReq
                WHERE ReqID = :ReqID";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ApkID'     => $data['ApkID'],
            ':HwID'      => $data['HwID'],
            ':Prioritas'=> $data['Prioritas'],
            ':Request'  => $data['Request'],
            ':Dokumentasi' => $data['Dokumentasi'],
            ':StatusReq'=> $data['StatusReq'],
            ':ReqID'    => (int)$reqID
        ]);
    }

    public function delete($reqID) {
        $sql = "DELETE FROM request WHERE ReqID = :ReqID";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':ReqID' => (int)$reqID
        ]);
    }

    public function existsByApk($apkID, $excludeNik = null) {
        $sql = "SELECT COUNT(*) FROM request WHERE ApkID = :ApkID";
        $params = [
            ':ApkID' => $apkID
        ];

        if (!empty($excludeNik)) {
            $sql .= " AND NIK != :NIK";
            $params[':NIK'] = $excludeNik;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    public function approve($id){
        $stmt = $this->db->prepare("UPDATE request SET StatusReq='Disetujui' WHERE ReqID = ?");
        return $stmt->execute([$id]);
    }

    public function reject($id){
        $stmt = $this->db->prepare("UPDATE request SET StatusReq='Ditolak' WHERE ReqID = ?");
        return $stmt->execute([$id]);
    }

    public function getStatusById($id) {
    $stmt = $this->db->prepare("SELECT StatusReq FROM request WHERE ReqID = ?");
    $stmt->execute([$id]);
    return $stmt->fetchColumn();
    }

    public function cancelled($id) {
        return $this->db->prepare(
            "UPDATE request SET StatusReq='Dibatalkan' WHERE ReqID=?"
        )->execute([$id]);
    }

    public function setAntrian($id) {
    $stmt = $this->db->prepare("UPDATE request SET StatusReq='Antrian' WHERE ReqID=?");
    $result = $stmt->execute([$id]);
    var_dump($result, $id); // debug
    return $result;
}

    public function setMenungguReview($id) {
    return $this->db->prepare(
        "UPDATE request 
         SET StatusReq = 'Menunggu Review',
             waiting_review_at = NOW()
         WHERE ReqID = ?"
    )->execute([$id]);
}

    public function revisi($id) {
    return $this->db->prepare(
        "UPDATE request 
         SET StatusReq='Revisi',
             waiting_review_at = NULL
         WHERE ReqID=?"
    )->execute([$id]);
}

    public function selesai($reqID){
    $sql = "UPDATE request 
            SET StatusReq = 'Selesai'
            WHERE ReqID = :ReqID";
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['ReqID' => $reqID]);

    $sql2 = "UPDATE review
             SET Status = 'Approved'
             WHERE ReqID = :ReqID";
    $stmt2 = $this->db->prepare($sql2);
    $stmt2->execute(['ReqID' => $reqID]);

    return true;
}

    public function getRequestDetails($reqID) {
        $sql = "SELECT 
                    r.*,
                    u.name AS user_name,
                    u.Departemen AS user_department,
                    u.email AS user_email,
                    a.NamaApk AS apk_name,
                    h.NamaHw AS hw_name
                FROM request r
                LEFT JOIN users u ON r.NIK = u.NIK
                LEFT JOIN aplikasi a ON r.ApkID = a.ApkID
                LEFT JOIN hardware h ON r.HwID = h.HwID
                WHERE r.ReqID = :reqID
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bindParam(':reqID', $reqID, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function autoCancelExpired() {
    $sql = "SELECT r.ReqID
            FROM request r
            JOIN request_completion c ON r.ReqID = c.ReqID
            WHERE r.StatusReq = 'Antrian'
              AND c.Deadline IS NOT NULL
              AND c.Deadline < NOW()
              AND c.MulaiKerja IS NULL";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

public function autoFinishWaitingReview() {
    $sql = "UPDATE request
            SET StatusReq = 'Selesai',
                waiting_review_at = NULL
            WHERE StatusReq = 'Menunggu Review'
              AND waiting_review_at IS NOT NULL
              AND waiting_review_at <= NOW() - INTERVAL 1 HOUR";

    $this->db->query($sql);
}

}
