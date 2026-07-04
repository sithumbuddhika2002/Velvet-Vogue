<?php
require_once 'header.php';

// Handle Cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $cart_key = sanitize($_POST['cart_key']);
    
    if ($_POST['action'] === 'update_qty' && isset($_POST['quantity'])) {
        $new_qty = (int)$_POST['quantity'];
        if (isset($_SESSION['cart'][$cart_key])) {
            // Verify stock limit
            $product_id = $_SESSION['cart'][$cart_key]['id'];
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $stock = $stmt->fetchColumn();

            if ($new_qty < 1) {
                unset($_SESSION['cart'][$cart_key]);
            } else if ($new_qty > $stock) {
                $_SESSION['cart'][$cart_key]['quantity'] = $stock;
            } else {
                $_SESSION['cart'][$cart_key]['quantity'] = $new_qty;
            }
        }
    } else if ($_POST['action'] === 'remove_item') {
        if (isset($_SESSION['cart'][$cart_key])) {
            unset($_SESSION['cart'][$cart_key]);
        }
    }
}

$cart_items = $_SESSION['cart'];
$subtotal = 0;
?>

<section class="section-padding">
    <div class="container">
        <span class="subtitle">Your Bag</span>
        <h1 style="margin: 0.5rem 0 3rem; font-size: clamp(2rem, 4vw, 3rem);">Shopping Cart</h1>

        <?php if (empty($cart_items)): ?>
            <div class="empty-state animate-fade-in">
                <i class="fas fa-shopping-bag" style="font-size: 3.5rem; color: var(--accent-gold); margin-bottom: 1.5rem; display: inline-block;"></i>
                <h3>Your Bag is Empty</h3>
                <p>You haven't added any clothing items to your shopping bag yet. Explore our latest arrivals to get started.</p>
                <a href="shop.php" class="btn btn-primary">Browse Shop</a>
            </div>
        <?php else: ?>
            <div class="animate-fade-in">
                <!-- Cart Items Table -->
                <div class="cart-table-wrapper">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach ($cart_items as $key => $item): 
                                $item_total = $item['price'] * $item['quantity'];
                                $subtotal += $item_total;
                            ?>
                                <tr>
                                    <!-- Product Info Column -->
                                    <td>
                                        <div class="cart-product-cell">
                                            <div class="cart-product-img">
                                                <?php 
                                                $image_path = 'images/' . $item['image_url'];
                                                if (!empty($item['image_url']) && file_exists($image_path)): 
                                                ?>
                                                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                                <?php else: ?>
                                                    <div class="product-image-placeholder" style="height: 100%;">
                                                        <i class="fas fa-shirt" style="font-size: 1.5rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="cart-product-info">
                                                <h4><a href="product.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a></h4>
                                                <p style="margin-top: 0.25rem;">
                                                    <span class="item-spec">Size: <?php echo htmlspecialchars($item['size']); ?></span>
                                                    <span class="item-spec">Color: <?php echo htmlspecialchars($item['color']); ?></span>
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Price Column -->
                                    <td>$<?php echo number_format($item['price'], 2); ?></td>
                                    
                                    <!-- Quantity Adjustment Column -->
                                    <td>
                                        <form action="cart.php" method="POST" style="display: inline-flex; align-items: center;">
                                            <input type="hidden" name="action" value="update_qty">
                                            <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($key); ?>">
                                            
                                            <!-- Simple responsive adjustments -->
                                            <div class="qty-selector" style="height: 40px;">
                                                <button type="submit" name="quantity" value="<?php echo $item['quantity'] - 1; ?>" class="qty-btn" style="width: 35px;">-</button>
                                                <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" style="width: 35px;" readonly>
                                                <button type="submit" name="quantity" value="<?php echo $item['quantity'] + 1; ?>" class="qty-btn" style="width: 35px;">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    
                                    <!-- Item Total Column -->
                                    <td>$<?php echo number_format($item_total, 2); ?></td>
                                    
                                    <!-- Remove Item Action -->
                                    <td style="text-align: right;">
                                        <form action="cart.php" method="POST">
                                            <input type="hidden" name="action" value="remove_item">
                                            <input type="hidden" name="cart_key" value="<?php echo htmlspecialchars($key); ?>">
                                            <button type="submit" class="cart-remove-btn"><i class="far fa-trash-can" style="margin-right: 0.25rem;"></i> Remove</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Cart Totals & Checkout Redirection Panel -->
                <div class="cart-summary-panel">
                    <h3 style="font-family: var(--font-body); font-size: 1.15rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 1.5rem;">Cart Totals</h3>
                    
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span style="font-size: 0.9rem; color: var(--text-secondary);"><?php echo $subtotal >= 150 ? 'FREE' : '$15.00'; ?></span>
                    </div>
                    
                    <?php 
                    $shipping_cost = $subtotal >= 150 ? 0 : 15.00;
                    $grand_total = $subtotal + $shipping_cost;
                    ?>
                    <div class="summary-row total">
                        <span>Grand Total</span>
                        <span>$<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                    
                    <a href="checkout.php" class="btn btn-primary btn-full" style="height: 55px; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Proceed to Checkout</a>
                    <a href="shop.php" class="btn btn-secondary btn-full" style="margin-top: 1rem; height: 50px; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once 'footer.php'; ?>
