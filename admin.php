<?php
require_once 'db.php';

// Authorization Gate (Only admins can enter)
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$tab = isset($_GET['tab']) ? sanitize($_GET['tab']) : 'catalog';
$success_msg = '';
$error_msg = '';

// Handle Order Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order_status') {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['status']);
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $success_msg = "Order #VV-{$order_id} updated to '{$new_status}' successfully.";
    } catch (Exception $e) {
        $error_msg = "Failed to update order status. Error: " . $e->getMessage();
    }
}

// Handle Add Product Catalog item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_product') {
    $name = sanitize($_POST['name']);
    $description = sanitize($_POST['description']);
    $price = (float)$_POST['price'];
    $category = sanitize($_POST['category']);
    $gender = sanitize($_POST['gender']);
    $sizes = sanitize($_POST['sizes']);
    $colors = sanitize($_POST['colors']);
    $stock = (int)$_POST['stock'];

    // Local WAMP sandbox image upload
    $image_url = 'placeholder.jpg';
    if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $target_dir = "images/";
        // Create directory if not exists
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $filename = time() . '_' . basename($_FILES['product_image']['name']);
        $target_file = $target_dir . $filename;
        $image_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        
        // Validate image type
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($image_type, $allowed)) {
            if (move_uploaded_file($_FILES['product_image']['tmp_name'], $target_file)) {
                $image_url = $filename;
            } else {
                $error_msg = "Failed to move uploaded product image.";
            }
        } else {
            $error_msg = "Invalid image file format. Only JPG, PNG, and WebP are allowed.";
        }
    }

    if ($error_msg === '') {
        try {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, category, gender, image_url, sizes, colors, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $category, $gender, $image_url, $sizes, $colors, $stock]);
            $success_msg = "Product '{$name}' added to catalog successfully.";
        } catch (Exception $e) {
            $error_msg = "Database insert error: " . $e->getMessage();
        }
    }
}

// Handle Delete Product Catalog item
if (isset($_GET['delete_product_id'])) {
    $del_id = (int)$_GET['delete_product_id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        $stmt->execute([$del_id]);
        $success_msg = "Product successfully removed from catalog.";
    } catch (Exception $e) {
        $error_msg = "Failed to delete product: " . $e->getMessage();
    }
}

// Fetch lists based on active tab
if ($tab === 'orders') {
    $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
    $orders = $stmt->fetchAll();
} else if ($tab === 'inquiries') {
    $stmt = $pdo->query("SELECT * FROM inquiries ORDER BY id DESC");
    $inquiries = $stmt->fetchAll();
} else {
    // default: catalog
    $stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
    $products = $stmt->fetchAll();
}

// Calculate Statistics Metrics
$revenue_stmt = $pdo->query("SELECT SUM(total_amount) FROM orders");
$total_revenue = (float)$revenue_stmt->fetchColumn();

$orders_stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = (int)$orders_stmt->fetchColumn();

$products_stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = (int)$products_stmt->fetchColumn();

$inquiries_stmt = $pdo->query("SELECT COUNT(*) FROM inquiries");
$total_inquiries = (int)$inquiries_stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Velvet Vogue | Admin Dashboard</title>
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

<div class="admin-dashboard">
    <!-- Persistent Left Sidebar Navigation -->
    <aside class="admin-sidebar-nav">
        <div>
            <div class="admin-logo">
                <span>V</span>elvet <span>V</span>ogue
            </div>
            
            <nav class="admin-sidebar-menu">
                <a href="admin.php?tab=catalog" class="admin-menu-link <?php echo $tab === 'catalog' ? 'active' : ''; ?>">
                    <i class="fas fa-shirt"></i> Product Catalog
                </a>
                <a href="admin.php?tab=orders" class="admin-menu-link <?php echo $tab === 'orders' ? 'active' : ''; ?>">
                    <i class="fas fa-receipt"></i> Customer Orders
                </a>
                <a href="admin.php?tab=inquiries" class="admin-menu-link <?php echo $tab === 'inquiries' ? 'active' : ''; ?>">
                    <i class="fas fa-envelope-open-text"></i> Contact Messages
                </a>
            </nav>
        </div>
        
        <div class="admin-sidebar-footer">
            <a href="index.php" class="admin-menu-link" style="background-color: rgba(197, 168, 128, 0.1);">
                <i class="fas fa-store"></i> Back to Store
            </a>
            <a href="logout.php" class="admin-menu-link" style="color: var(--danger);">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </aside>

    <!-- Main Workspace Area -->
    <main class="admin-main">
        <header class="admin-header">
            <div class="admin-header-title">
                <?php 
                if ($tab === 'orders') echo 'Customer Transactions';
                else if ($tab === 'inquiries') echo 'Customer Messages';
                else echo 'Boutique Inventory';
                ?>
            </div>
            
            <div class="admin-header-actions">
                <!-- Theme Toggle Button -->
                <button id="theme-toggle" class="nav-action-icon" title="Toggle Theme" aria-label="Toggle Theme">
                    <i id="theme-toggle-icon" class="fas fa-moon"></i>
                </button>
                
                <!-- Admin Info -->
                <div style="display: flex; align-items: center; gap: 0.5rem; font-weight: 500; font-size: 0.9rem;">
                    <i class="far fa-user-circle" style="font-size: 1.25rem; color: var(--accent-gold);"></i>
                    <span>Admin</span>
                </div>
            </div>
        </header>

        <div class="admin-body">
            <!-- Alert Messages -->
            <?php if ($success_msg): ?>
                <div class="alert alert-success animate-fade-in" style="margin-bottom: 2rem;">
                    <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $success_msg; ?>
                </div>
            <?php endif; ?>

            <?php if ($error_msg): ?>
                <div class="alert alert-danger animate-fade-in" style="margin-bottom: 2rem;">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>

            <!-- Metrics Statistics Grid -->
            <div class="admin-metrics-grid">
                <div class="metric-card">
                    <div class="metric-icon green">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                    <div class="metric-info">
                        <h4>Total Revenue</h4>
                        <span>$<?php echo number_format($total_revenue, 2); ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon blue">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div class="metric-info">
                        <h4>Total Orders</h4>
                        <span><?php echo $total_orders; ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon">
                        <i class="fas fa-shirt"></i>
                    </div>
                    <div class="metric-info">
                        <h4>Catalog Items</h4>
                        <span><?php echo $total_products; ?></span>
                    </div>
                </div>
                <div class="metric-card">
                    <div class="metric-icon blue" style="background-color: rgba(139, 92, 246, 0.1); color: #8b5cf6;">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="metric-info">
                        <h4>Inquiries</h4>
                        <span><?php echo $total_inquiries; ?></span>
                    </div>
                </div>
            </div>

            <!-- Tab Content -->
            <div class="admin-panel animate-fade-in" style="border-radius: 8px;">
                <!-- TAB 1: PRODUCT CATALOG -->
                <?php if ($tab === 'catalog'): ?>
                    <h3 style="margin-bottom: 1.5rem;">Catalog Management</h3>
                    
                    <details style="border: 1px solid var(--border-color); background-color: var(--bg-primary); padding: 1.5rem; margin-bottom: 2rem; border-radius: 6px;">
                        <summary style="font-weight: 600; cursor: pointer; text-transform: uppercase; letter-spacing: 0.05em; color: var(--accent-gold);">+ Add New Catalog Product</summary>
                        
                        <form action="admin.php?tab=catalog" method="POST" enctype="multipart/form-data" style="margin-top: 1.5rem;">
                            <input type="hidden" name="action" value="add_product">
                            
                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div>
                                    <label for="name">Product Name *</label>
                                    <input type="text" id="name" name="name" class="form-control" required placeholder="e.g. Linen Blended Suit">
                                </div>
                                <div>
                                    <label for="price">Price ($) *</label>
                                    <input type="number" id="price" name="price" step="0.01" min="1" class="form-control" required placeholder="e.g. 150.00">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Detailed Description *</label>
                                <textarea id="description" name="description" class="form-control" required placeholder="Describe materials, fit details, styles..." style="height: 100px; resize: vertical;"></textarea>
                            </div>

                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                                <div>
                                    <label for="category">Category *</label>
                                    <select id="category" name="category" class="form-control" style="background-color: var(--bg-primary);">
                                        <option value="casual">Casualwear</option>
                                        <option value="formal">Formalwear</option>
                                        <option value="accessories">Accessories</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="gender">Gender Target *</label>
                                    <select id="gender" name="gender" class="form-control" style="background-color: var(--bg-primary);">
                                        <option value="men">Men</option>
                                        <option value="women">Women</option>
                                        <option value="unisex">Unisex</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="stock">Initial Stock *</label>
                                    <input type="number" id="stock" name="stock" value="20" min="0" class="form-control" required>
                                </div>
                            </div>

                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                                <div>
                                    <label for="sizes">Available Sizes (Comma separated) *</label>
                                    <input type="text" id="sizes" name="sizes" class="form-control" value="S,M,L,XL" placeholder="e.g. S,M,L,XL or One Size">
                                </div>
                                <div>
                                    <label for="colors">Available Colors (Comma separated) *</label>
                                    <input type="text" id="colors" name="colors" class="form-control" value="Black,Ivory" placeholder="e.g. Black,Ivory,Navy">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="product_image">Product Image File *</label>
                                <input type="file" id="product_image" name="product_image" required style="display: block; margin-top: 0.5rem; font-size: 0.9rem;">
                            </div>

                            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; border-radius: 4px;">Save Product</button>
                        </form>
                    </details>

                    <div class="admin-table-wrapper">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Category</th>
                                    <th>Price</th>
                                    <th>Stock</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($products as $p): ?>
                                    <tr>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                                <div style="width: 35px; height: 45px; background-color: var(--bg-primary); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden; border-radius: 4px;">
                                                    <?php if (file_exists('images/' . $p['image_url'])): ?>
                                                        <img src="images/<?php echo htmlspecialchars($p['image_url']); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                                    <?php else: ?>
                                                        <i class="fas fa-shirt" style="font-size: 0.7rem; color: var(--text-muted);"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <span style="font-weight: 500;"><?php echo htmlspecialchars($p['name']); ?></span>
                                            </div>
                                        </td>
                                        <td style="text-transform: uppercase; font-size: 0.75rem; color: var(--accent-gold); font-weight: 600;"><?php echo $p['category']; ?> / <?php echo $p['gender']; ?></td>
                                        <td>$<?php echo number_format($p['price'], 2); ?></td>
                                        <td><?php echo $p['stock']; ?></td>
                                        <td>
                                            <a href="admin.php?tab=catalog&delete_product_id=<?php echo $p['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?');" style="color: var(--danger); font-size: 0.8rem;" title="Delete Product"><i class="far fa-trash-can"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>

                <!-- TAB 2: CUSTOMER ORDERS -->
                <?php elseif ($tab === 'orders'): ?>
                    <h3 style="margin-bottom: 1.5rem;">Transaction & Order Management</h3>

                    <?php if (empty($orders)): ?>
                        <div class="empty-state">
                            <h3>No Orders Placed Yet</h3>
                            <p>Customers haven't made any purchases yet. New order orders will pop up in this screen.</p>
                        </div>
                    <?php else: ?>
                        <div class="admin-table-wrapper">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Ref ID</th>
                                        <th>Customer</th>
                                        <th>Delivery Address</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                        <th>Update Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($orders as $order): ?>
                                        <tr>
                                            <td style="font-weight: 600;">#VV-<?php echo sprintf('%05d', $order['id']); ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($order['shipping_name']); ?></strong><br>
                                                <span style="font-size: 0.75rem; color: var(--text-muted);"><?php echo htmlspecialchars($order['shipping_email']); ?></span>
                                            </td>
                                            <td style="font-size: 0.85rem; max-width: 250px; line-height: 1.4;"><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                            <td style="font-weight: 600; color: var(--accent-gold);">$<?php echo number_format($order['total_amount'], 2); ?></td>
                                            <td>
                                                <?php 
                                                $badge_class = 'badge-pending';
                                                if ($order['status'] === 'Processing') $badge_class = 'badge-processing';
                                                else if ($order['status'] === 'Shipped') $badge_class = 'badge-shipped';
                                                else if ($order['status'] === 'Delivered') $badge_class = 'badge-delivered';
                                                ?>
                                                <span class="badge-status <?php echo $badge_class; ?>"><?php echo $order['status']; ?></span>
                                            </td>
                                            <td>
                                                <form action="admin.php?tab=orders" method="POST" style="display: flex; gap: 0.25rem;">
                                                    <input type="hidden" name="action" value="update_order_status">
                                                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                    <select name="status" class="form-control" style="padding: 0.35rem 0.5rem; font-size: 0.8rem; background-color: var(--bg-primary);" onchange="this.form.submit();">
                                                        <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>Pending</option>
                                                        <option value="Processing" <?php echo $order['status'] === 'Processing' ? 'selected' : ''; ?>>Processing</option>
                                                        <option value="Shipped" <?php echo $order['status'] === 'Shipped' ? 'selected' : ''; ?>>Shipped</option>
                                                        <option value="Delivered" <?php echo $order['status'] === 'Delivered' ? 'selected' : ''; ?>>Delivered</option>
                                                    </select>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                <!-- TAB 3: CONTACT MESSAGES / INQUIRIES -->
                <?php elseif ($tab === 'inquiries'): ?>
                    <h3 style="margin-bottom: 1.5rem;">Customer Messages & Inquiries</h3>

                    <?php if (empty($inquiries)): ?>
                        <div class="empty-state">
                            <h3>No Messages Received</h3>
                            <p>You haven't received any customer inquiry submissions yet.</p>
                        </div>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-top: 1.5rem;">
                            <?php foreach ($inquiries as $inq): ?>
                                <div style="border: 1px solid var(--border-color); background-color: var(--bg-primary); padding: 1.5rem; border-radius: 6px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--border-color); padding-bottom: 0.75rem; margin-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                                        <div>
                                            <strong>From: <?php echo htmlspecialchars($inq['name']); ?></strong> 
                                            <span style="font-size: 0.8rem; color: var(--text-muted); margin-left: 0.5rem;">< &nbsp;<?php echo htmlspecialchars($inq['email']); ?> &nbsp;></span>
                                        </div>
                                        <div style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('M d, Y H:i', strtotime($inq['created_at'])); ?></div>
                                    </div>
                                    <div style="font-weight: 500; font-size: 0.95rem; margin-bottom: 0.5rem; color: var(--accent-gold);">Subject: <?php echo htmlspecialchars($inq['subject']); ?></div>
                                    <p style="font-size: 0.95rem; line-height: 1.6; color: var(--text-secondary); white-space: pre-wrap;"><?php echo htmlspecialchars($inq['message']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
    </main>
</div>

<!-- Theme toggle logic implementation -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const themeToggleIcon = document.getElementById('theme-toggle-icon');
        
        if (themeToggleBtn && themeToggleIcon) {
            // Set initial icon based on theme
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            if (currentTheme === 'dark') {
                themeToggleIcon.className = 'fas fa-sun';
            } else {
                themeToggleIcon.className = 'fas fa-moon';
            }
            
            themeToggleBtn.addEventListener('click', () => {
                const activeTheme = document.documentElement.getAttribute('data-theme') || 'light';
                let newTheme = 'light';
                
                if (activeTheme === 'light') {
                    newTheme = 'dark';
                    themeToggleIcon.className = 'fas fa-sun';
                } else {
                    newTheme = 'light';
                    themeToggleIcon.className = 'fas fa-moon';
                }
                
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            });
        }
    });
</script>

</body>
</html>
