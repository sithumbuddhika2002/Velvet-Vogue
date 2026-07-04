<?php
require_once 'db.php';
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue | E-Commerce Clothing Store</title>
    <!-- CSS and Fonts -->
    <link rel="stylesheet" href="css/style.css">
    <!-- FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme check script to prevent layout flashes -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        })();
    </script>
</head>
<body>

<!-- Mobile Nav Backdrop Overlay -->
<div class="nav-overlay" id="nav-overlay"></div>

<header class="site-header" id="site-header">
    <div class="container nav-container">
        <!-- Logo -->
        <a href="index.php" class="logo" aria-label="Velvet Vogue Home">
            <span>V</span>elvet <span>V</span>ogue
        </a>

        <!-- Primary Navigation Links -->
        <nav class="nav-menu" id="nav-menu">
            <a href="index.php" class="nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
                <span>Home</span>
            </a>
            <a href="shop.php" class="nav-link <?php echo $current_page == 'shop.php' ? 'active' : ''; ?>">
                <span>Shop</span>
            </a>
            <a href="contact.php" class="nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">
                <span>Contact</span>
            </a>
            
            <?php if (isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin'): ?>
                <a href="admin.php" class="nav-link <?php echo $current_page == 'admin.php' ? 'active' : ''; ?>">
                    <span>Admin Portal</span>
                </a>
            <?php endif; ?>
        </nav>

        <!-- Right Hand Actions: Cart & User Account -->
        <div class="nav-actions">
            <!-- Theme Toggle Button -->
            <button id="theme-toggle" class="nav-action-icon" title="Toggle Theme" aria-label="Toggle Theme">
                <i id="theme-toggle-icon" class="fas fa-moon"></i>
            </button>
            <script>
                (function() {
                    const icon = document.getElementById('theme-toggle-icon');
                    const savedTheme = localStorage.getItem('theme') || 'light';
                    if (savedTheme === 'dark') {
                        icon.className = 'fas fa-sun';
                    } else {
                        icon.className = 'fas fa-moon';
                    }
                })();
            </script>

            <!-- Account Icon/Link -->
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="profile.php" class="nav-action-icon" title="View Account Profile">
                    <i class="far fa-user"></i>
                    <span class="user-first-name">
                        <?php echo explode(' ', $_SESSION['user_name'])[0]; ?>
                    </span>
                </a>
                <a href="logout.php" class="nav-action-icon" title="Logout" style="font-size: 1rem;">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            <?php else: ?>
                <a href="login.php" class="nav-action-icon" title="Login / Register">
                    <i class="far fa-user"></i>
                </a>
            <?php endif; ?>

            <!-- Shopping Cart Icon with dynamic items badge -->
            <a href="cart.php" class="nav-action-icon" title="Shopping Cart">
                <i class="fas fa-shopping-bag"></i>
                <?php 
                $cart_count = get_cart_count();
                if ($cart_count > 0): 
                ?>
                    <span class="cart-badge"><?php echo $cart_count; ?></span>
                <?php endif; ?>
            </a>

            <!-- Mobile Hamburger Menu Button with morphing lines -->
            <button class="nav-toggle-btn" id="nav-toggle-btn" aria-label="Toggle Navigation Menu">
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
                <span class="hamburger-line"></span>
            </button>
        </div>
    </div>
</header>
<main>
