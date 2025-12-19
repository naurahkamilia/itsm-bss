<?php
defined('APP_ACCESS') or die('Direct access not permitted');

class Notification {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function countUnread($receiverNik) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) 
            FROM notifications
            WHERE ReceiverNIK = ? AND IsRead = 0
        ");
        $stmt->execute([$receiverNik]);
        return (int) $stmt->fetchColumn();
    }

    public function getUnreadByReceiver($receiverNik, $limit = 5) {
        $stmt = $this->db->prepare("
            SELECT *
            FROM notifications
            WHERE ReceiverNIK = ? AND IsRead = 0
            ORDER BY createdAt DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $receiverNik);
        $stmt->bindValue(2, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

public function create($receiver, $sender, $reqID, $title, $message) {

    if (!is_numeric($receiver)) {
        throw new InvalidArgumentException(
            "ReceiverNIK harus berupa NIK (integer), bukan '{$receiver}'"
        );
    }

    $sql = "INSERT INTO notifications 
            (ReceiverNIK, SenderNIK, ReqID, Title, Message)
            VALUES (?, ?, ?, ?, ?)";

    $stmt = $this->db->prepare($sql);
    return $stmt->execute([
        (int)$receiver,
        (int)$sender,
        $reqID,
        $title,
        $message
    ]);
}

    public function getUnreadCount($nik) {
        $sql = "SELECT COUNT(*) FROM notifications 
                WHERE ReceiverNIK = ? AND IsRead = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$nik]);
        return $stmt->fetchColumn();
    }

    public function getLatest($nik, $limit = 5) {
    $sql = "SELECT * FROM notifications 
            WHERE ReceiverNIK = ?
            ORDER BY CreatedAt DESC
            LIMIT ?";
    $stmt = $this->db->prepare($sql);
    $stmt->bindValue(1, $nik, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function markAsRead($notifID){
    $stmt = $this->db->prepare("UPDATE notifications SET IsRead = 1 WHERE NotifID = ?");
    return $stmt->execute([$notifID]);
}

public function markAllAsRead($receiver) {
    $stmt = $this->db->prepare("
        UPDATE notifications 
        SET IsRead = 1 
        WHERE ReceiverNIK = :receiver 
        AND IsRead = 0
    ");
    return $stmt->execute(['receiver' => $receiver]);
}


    public function getUnsentNotifications() {
        $sql = "
            SELECT n.*, u.Email
            FROM notifications n
            JOIN users u ON u.NIK = n.ReceiverNIK
            WHERE n.IsSent = 0
            ORDER BY n.CreatedAt ASC
        ";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    // Tandai email sudah terkirim
    public function markAsSent($notifID) {
        $stmt = $this->db->prepare(
            "UPDATE notifications SET IsSent = 1 WHERE NotifID = ?"
        );
        return $stmt->execute([$notifID]);
    }


    }
