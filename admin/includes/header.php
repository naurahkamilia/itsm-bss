<?php
defined('APP_ACCESS') or die('Direct access not permitted');

require_once '../config/config.php';
require_once '../config/database.php';
require_once '../models/Notification.php';

$db = Database::getInstance()->getConnection();
$notifModel = new Notification($db);

$adminNik = $_SESSION['user_id'] ?? null;
$unreadCount = 0;
$notifs = [];

if (is_numeric($adminNik)) {
    $unreadCount = $notifModel->countUnread($adminNik);
    $notifs = $notifModel->getUnreadByReceiver($adminNik, 5);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Admin Panel - ' . APP_NAME; ?></title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= BASE_URL ?>public/css/style.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>admin/css/admin.css">

    <link rel="icon" type="image/png" href="<?= BASE_URL ?>public/images/favicon.png">
</head>

<body class="admin-body">

<nav class="navbar admin-navbar sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="<?= ADMIN_URL ?>">
            <i class="bi bi-speedometer2 me-2"></i>
            <span class="fw-semibold">Admin Panel</span>
        </a>

        <div class="d-flex align-items-center gap-3">

           <div class="dropdown">
            <a class="nav-link position-relative p-2"
            id="notifDropdown"
            role="button"
            data-bs-toggle="dropdown"
            aria-expanded="false">

                <i class="bi bi-bell fs-5"></i>

                <?php if ($unreadCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle
                                badge rounded-pill bg-danger">
                        <?= $unreadCount ?>
                    </span>
                <?php endif; ?>
            </a>

            <ul class="dropdown-menu dropdown-menu-end notif-dropdown">
                <li class="dropdown-header">Notifications</li>

                <?php if (empty($notifs)): ?>
                    <li class="text-center small text-muted py-2">
                        No new notifications
                    </li>
                <?php else: foreach ($notifs as $n): ?>
                    <li>
                        <a class="dropdown-item notif-item"
                        href="request-list.php?id=<?= $n['ReqID'] ?>">
                            <strong><?= htmlspecialchars($n['Title']) ?></strong>
                            <p class="mb-1 small"><?= htmlspecialchars($n['Message']) ?></p>
                            <small class="text-muted">
                                <?= date('d M Y H:i', strtotime($n['CreatedAt'])) ?>
                            </small>
                        </a>
                    </li>
                <?php endforeach; endif; ?>
            </ul>
        </div>

            <!-- User -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center p-2"
                role="button"
                data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle fs-5 me-2"></i>
                    <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li class="dropdown-item-text small text-muted">
                        <?= htmlspecialchars($_SESSION['user_email'] ?? '') ?>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item" href="<?= BASE_URL ?>logout.php">
                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                        </a>
                    </li>
                </ul>
            </div>

        </div>
    </div>
</nav>

<div class="admin-wrapper">

    <!-- SIDEBAR -->
    <aside class="admin-sidebar">
        <div class="sidebar-header">Menu</div>
        <nav class="sidebar-nav">
            <a href="<?= ADMIN_URL ?>" class="sidebar-link <?= ($currentPage=='dashboard')?'active':'' ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="<?= ADMIN_URL ?>request-list.php" class="sidebar-link <?= ($currentPage=='request')?'active':'' ?>">
                <i class="bi bi-box-seam"></i> Requests
            </a>
            <a href="<?= ADMIN_URL ?>karyawan-list.php" class="sidebar-link <?= ($currentPage=='karyawans')?'active':'' ?>">
                <i class="bi bi-person-vcard"></i> Employees
            </a>
            <a href="<?= ADMIN_URL ?>user-list.php" class="sidebar-link <?= ($currentPage=='user-acc')?'active':'' ?>">
                <i class="bi bi-person-lines-fill"></i> User Account
            </a>
            <a href="<?= ADMIN_URL ?>aplikasi.php" class="sidebar-link <?= ($currentPage=='aplikasi')?'active':'' ?>">
                <i class="bi bi-phone"></i> Apps
            </a>
            <a href="<?= ADMIN_URL ?>hardware-list.php" class="sidebar-link <?= ($currentPage=='hardware')?'active':'' ?>">
                <i class="bi bi-motherboard"></i> Infrastructure
            </a>
        </nav>
    </aside>

    <!-- CONTENT START -->
    <main class="admin-content">
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdown = document.getElementById('notifDropdown');
    dropdown.addEventListener('shown.bs.dropdown', function () {
        fetch('<?php echo BASE_URL; ?>ajax/notif-markasRead.php', {
            method: 'POST'
        })
        .then(res => res.json())
        .then(data => {
            if(data.status === "success"){
                const badge = dropdown.querySelector('.badge');
                if(badge) badge.remove();
            }
        });
    });
});

</script>
