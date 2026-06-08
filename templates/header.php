<?php
// templates/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$user_name = isset($_SESSION['name']) ? $_SESSION['name'] : 'Guest';
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . " - Network Careline" : "Network Careline"; ?></title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Custom Style -->
    <link rel="stylesheet" href="style.css">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="app-layout">
        <?php include('sidebar.php'); ?>
        
        <div class="main-content">
            <header class="top-bar">
                <div class="top-bar-left">
                    <button class="mobile-nav-toggle" id="mobileNavToggle">
                        <i class="bi bi-list"></i>
                    </button>
                    <div class="page-title-container">
                        <h2><?php echo isset($page_title) ? htmlspecialchars($page_title) : "Dashboard"; ?></h2>
                    </div>
                </div>
                
                <div class="top-bar-right">
                    <div class="user-profile-badge">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user_name, 0, 1)); ?>
                        </div>
                        <div class="user-profile-info">
                            <span class="user-profile-name"><?php echo htmlspecialchars($user_name); ?></span>
                            <span class="user-profile-role"><?php echo ucfirst(htmlspecialchars($role)); ?></span>
                        </div>
                    </div>
                </div>
            </header>
            
            <div class="content-body">
