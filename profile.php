<?php
require_once 'header.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_role'];

// Fetch user orders
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();
?>

<section class="section-padding">
    <div class="container">
        
        <div class="profile-card animate-fade-in">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-info">
                    <h3><?php echo htmlspecialchars($user_name); ?></h3>
                    <p><i class="far fa-envelope" style="margin-right: 0.25rem;"></i> <?php echo htmlspecialchars($user_email); ?></p>
                </div>
                <div>
                    <span class="badge-status" style="background-color: var(--bg-primary); border: 1px solid var(--border-color); color: var(--accent-gold); font-size: 0.8rem; padding: 0.4rem 1rem;">
                        <i class="fas fa-crown" style="margin-right: 0.25rem; font-size: 0.75rem;"></i> <?php echo ucfirst($user_role); ?> Account
                    </span>
                </div>
            </div>

            <!-- Order History Section -->
            <h4 style="font-family: var(--font-body); font-size: 1.1rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2rem;">Order History</h4>
            
            <?php if (empty($orders)): ?>
                <div class="empty-state" style="padding: 3rem 1.5rem;">
                    <h3>No Orders Yet</h3>
                    <p>You haven't placed any transactions with Velvet Vogue yet. Once you make a purchase, it will appear here.</p>
                    <a href="shop.php" class="btn btn-primary btn-sm" style="padding: 0.75rem 1.5rem; font-size: 0.75rem; margin-top: 1rem;">Shop Our Catalog</a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 2rem;">
                    <?php 
                    foreach ($orders as $order): 
                        // Fetch order items for this specific order
                        $item_stmt = $pdo->prepare("
                            SELECT oi.*, p.name as product_name, p.image_url 
                            FROM order_items oi 
                            LEFT JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?
                        ");
                        $item_stmt->execute([$order['id']]);
                        $order_items = $item_stmt->fetchAll();
                        
                        // Parse status badge
                        $badge_class = 'badge-pending';
                        if ($order['status'] === 'Processing') $badge_class = 'badge-processing';
                        else if ($order['status'] === 'Shipped') $badge_class = 'badge-shipped';
                        else if ($order['status'] === 'Delivered') $badge_class = 'badge-delivered';
                    ?>
                        <div style="border: 1px solid var(--border-color); background-color: var(--bg-primary); padding: 1.5rem;">
                            <!-- Individual Order Metadata bar -->
                            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                                <div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Order Reference</span>
                                    <h5 style="font-family: var(--font-body); font-size: 1rem; font-weight: 600; margin-top: 0.25rem;">#VV-<?php echo sprintf('%05d', $order['id']); ?></h5>
                                </div>
                                <div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Placed On</span>
                                    <div style="font-size: 0.95rem; font-weight: 500; margin-top: 0.25rem;"><?php echo date('F d, Y', strtotime($order['created_at'])); ?></div>
                                </div>
                                <div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Status</span>
                                    <div style="margin-top: 0.25rem;"><span class="badge-status <?php echo $badge_class; ?>"><?php echo $order['status']; ?></span></div>
                                </div>
                                <div>
                                    <span style="font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">Total Amount</span>
                                    <div style="font-size: 1.05rem; font-weight: 700; color: var(--accent-gold); margin-top: 0.25rem;">$<?php echo number_format($order['total_amount'], 2); ?></div>
                                </div>
                            </div>

                            <!-- List of items inside this Order -->
                            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                                <?php foreach ($order_items as $item): ?>
                                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.9rem;">
                                        <div style="display: flex; align-items: center; gap: 1rem;">
                                            <div style="width: 45px; height: 60px; background-color: var(--bg-secondary); border: 1px solid var(--border-color); display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                                <?php 
                                                $img_path = 'images/' . $item['image_url'];
                                                if (!empty($item['image_url']) && file_exists($img_path)): 
                                                ?>
                                                    <img src="<?php echo htmlspecialchars($img_path); ?>" alt="" style="width: 100%; height: 100%; object-fit: cover;">
                                                <?php else: ?>
                                                    <i class="fas fa-shirt" style="font-size: 0.9rem; color: var(--text-muted);"></i>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div style="font-weight: 500;"><?php echo htmlspecialchars($item['product_name'] ?? 'Product Removed'); ?></div>
                                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                                                    Size: <?php echo htmlspecialchars($item['size']); ?> &nbsp;|&nbsp; Color: <?php echo htmlspecialchars($item['color']); ?> &nbsp;|&nbsp; Qty: <?php echo $item['quantity']; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <span style="font-weight: 600;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
