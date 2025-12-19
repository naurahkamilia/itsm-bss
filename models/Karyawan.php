<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Karyawan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAll($filters = []) {
        $sql = "SELECT * FROM karyawan WHERE 1=1";
        $params = [];


        if (!empty($filters['search'])) {
        $sql .= " AND (
            Nama LIKE :s1 OR 
            Jabatan LIKE :s2 OR 
            Departemen LIKE :s3 OR
            NIK LIKE :s4 OR
            Tgl_masuk_kerja LIKE :s5 OR
            Plant LIKE :s6
        )";

        $search = '%' . $filters['search'] . '%';
        $params[':s1'] = $search;
        $params[':s2'] = $search;
        $params[':s3'] = $search;
        $params[':s4'] = $search;
        $params[':s5'] = $search;
        $params[':s6'] = $search;
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

    public function getById($nik) {
        $sql = "SELECT * FROM karyawan WHERE NIK = :NIK";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':NIK' => $nik]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function count($filters = []) {
        $sql = "SELECT COUNT(*) FROM karyawan WHERE 1=1";
        
        $params = [];

        if (!empty($filters['search'])) {
            $sql .= " AND (
                Nama LIKE :s1 OR 
                Jabatan LIKE :s2 OR 
                Departemen LIKE :s3 OR
                NIK LIKE :s4 OR
                Tgl_masuk_kerja LIKE :s5 OR
                Plant LIKE :s6
            )";

            $search = '%' . $filters['search'] . '%';
            $params[':s1'] = $search;
            $params[':s2'] = $search;
            $params[':s3'] = $search;
            $params[':s4'] = $search;
            $params[':s5'] = $search;
            $params[':s6'] = $search;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data) {
        $sql = "INSERT INTO karyawan 
        (NIK, Nama, Jabatan, Departemen, TTL, Alamat, Tgl_masuk_kerja, Plant)
        VALUES 
        (:NIK, :Nama, :Jabatan, :Departemen, :TTL, :Alamat, :Tgl_masuk_kerja, :Plant)";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NIK' => $data['NIK'],
            ':Nama' => $data['Nama'],
            ':Jabatan' => $data['Jabatan'],
            ':Departemen' => $data['Departemen'],
            ':TTL' => $data['TTL'] ?? null,
            ':Alamat' => $data['Alamat'],
            ':Tgl_masuk_kerja' => $data['Tgl_masuk_kerja'],
            ':Plant' => $data['Plant']
        ]);
    }

    public function update($nik, $data) {
        $sql = "UPDATE karyawan SET 
            Nama = :Nama,
            Jabatan = :Jabatan,
            Departemen = :Departemen,
            TTL = :TTL,
            Alamat = :Alamat,
            Tgl_masuk_kerja = :Tgl_masuk_kerja,
            Plant = :Plant
        WHERE NIK = :NIK";

        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':NIK' => $nik,
            ':Nama' => $data['Nama'],
            ':Jabatan' => $data['Jabatan'],
            ':Departemen' => $data['Departemen'],
            ':TTL' => $data['TTL'] ?? null,
            ':Alamat' => $data['Alamat'],
            ':Tgl_masuk_kerja' => $data['Tgl_masuk_kerja'],
            ':Plant' => $data['Plant']
        ]);
    }

    public function delete($nik) {
        $sql = "DELETE FROM karyawan WHERE NIK = :NIK";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':NIK' => $nik]);
    }
}
