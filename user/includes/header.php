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
    <title><?php echo $pageTitle ?? 'User Panel - ' . APP_NAME; ?></title>
    
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>user/css/user.css">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo BASE_URL; ?>public/images/favicon.png">
</head>
<body class="user-body">
    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top user-navbar" 
         style="background: linear-gradient(135deg, #059669 0%, #2563eb 100%);">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo USER_URL; ?>">
                <i class="bi bi-speedometer2 fs-3 me-2"></i>
                <span class="fw-bold">ITSM - User Panel</span>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            
            <div class="collapse navbar-collapse" id="adminNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item dropdown">
                    <a class="nav-link position-relative text-light"
                    href="#"
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

                    <ul class="dropdown-menu dropdown-menu-end p-2"
                        style="width:320px">

                        <li class="dropdown-header">
                            Notifications
                        </li>

                        <?php if (empty($notifs)): ?>
                            <li class="text-center text-muted small py-2">
                                No new notifications
                            </li>
                        <?php else: foreach ($notifs as $n): ?>
                            <li>
                               <a class="dropdown-item small notif-item"
                                href="lihatRequest.php?id=<?= $n['ReqID'] ?>"
                                data-id="<?= $n['NotifID'] ?>">

                                    <strong><?= htmlspecialchars($n['Title']) ?></strong><br>
                                    <span class="text-muted">
                                        <?= htmlspecialchars($n['Message']) ?>
                                    </span>

                                    <div class="text-muted small">
                                        <?= date('d M Y H:i', strtotime($n['CreatedAt'])) ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; endif; ?>

                    </ul>
                </li>    
                    <!-- User Dropdown -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2"></i>
                            <span><?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Customer'); ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <span class="dropdown-item-text text-muted small">
                                    <?php echo htmlspecialchars($_SESSION['user_email'] ?? ''); ?>
                                </span>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="<?php echo BASE_URL; ?>logout.php">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                </ul>  
            </div>
        </div>
    </nav>

    <div class="user-wrapper">
        <!-- Sidebar -->
        <aside class="user-sidebar">
            <div class="sidebar-header p-3">
                <h5 class="mb-0">Menu</h5>
            </div>
            <nav class="sidebar-nav">
                <a href="<?php echo USER_URL; ?>" class="sidebar-link <?php echo ($currentPage ?? '') == 'dashboard' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>       
                <a href="<?= USER_URL ?>lihatRequest.php" class="sidebar-link <?php echo ($currentPage ?? '') == 'lihatReq' ? 'active' : ''; ?>">
                    <i class="bi bi-envelope-paper-fill"></i>
                    <span>Request List</span>
                </a> 
                <hr class="sidebar-divider">
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="user-content">

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadNotifications(){
    $.getJSON('ajax/get-notifications.php', function(data){
        var badge = $('#notif-badge');
        if(data.unreadCount > 0){
            badge.text(data.unreadCount).show();
        } else {
            badge.hide();
        }

        var list = $('#notif-list');
        list.find('li:not(.dropdown-header)').remove();

        if(data.notifs.length === 0){
            list.append('<li class="text-center text-muted small py-2">No new notifications</li>');
        } else {
            $.each(data.notifs, function(i, n){
                var bg = n.IsRead ? '' : 'bg-light';
                var style = n.IsRead ? '' : 'background-color:#e9ecef;';
                list.append(
                    `<li>
                        <a class="dropdown-item small notif-item ${bg}" 
                           href="request-detail.php?id=${n.ReqID}" 
                           data-id="${n.NotifID}" 
                           style="${style}">
                            <strong>${n.Title}</strong><br>
                            <span class="text-muted">${n.Message}</span>
                            <div class="text-muted small">${n.CreatedAt}</div>
                        </a>
                    </li>`
                );
            });
        }
    });
}

// Load notifications saat halaman siap
$(document).ready(function(){
    loadNotifications();
});

<script>
document.querySelectorAll('.notif-item').forEach(el => {
    el.addEventListener('click', () => {
        fetch('ajax/notif.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'id=' + el.dataset.id
        });
    });
});
</script>
