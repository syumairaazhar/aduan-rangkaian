<?php
// templates/sidebar.php
$current_page = basename($_SERVER['PHP_SELF']);
$role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>
<div class="sidebar">
    <div class="sidebar-brand">
        <img src="PKNS.png" alt="PKNS Logo" class="brand-logo">
        <span>Network Careline</span>
    </div>
    
    <ul class="sidebar-menu">
        <?php if ($role === 'staff' || $role === 'admin' || $role === 'support'): ?>
            <li class="menu-label">Main Menu</li>
            <li>
                <a href="staff.php" class="<?php echo ($current_page == 'staff.php') ? 'active' : ''; ?>">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li>
                <a href="manage_user.php" class="<?php echo ($current_page == 'manage_user.php' || $current_page == 'add_user.php' || $current_page == 'edit_user.php') ? 'active' : ''; ?>">
                    <i class="bi bi-people-fill"></i>
                    <span>Manage Users</span>
                </a>
            </li>
            <li>
                <a href="manage_category.php" class="<?php echo ($current_page == 'manage_category.php') ? 'active' : ''; ?>">
                    <i class="bi bi-tags-fill"></i>
                    <span>Add Category</span>
                </a>
            </li>
        <?php else: ?>
            <li class="menu-label">My Desk</li>
            <li>
                <a href="user.php" class="<?php echo ($current_page == 'user.php' || $current_page == 'create_ticket.php' || $current_page == 'ticket_details.php') ? 'active' : ''; ?>">
                    <i class="bi bi-ticket-detailed-fill"></i>
                    <span>My Tickets</span>
                </a>
            </li>
            <li>
                <a href="user_settings.php" class="<?php echo ($current_page == 'user_settings.php') ? 'active' : ''; ?>">
                    <i class="bi bi-gear-fill"></i>
                    <span>Settings</span>
                </a>
            </li>
        <?php endif; ?>
        
        <li class="menu-divider"></li>
        <li>
            <a href="logout.php" class="logout-link">
                <i class="bi bi-box-arrow-right"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
</div>
