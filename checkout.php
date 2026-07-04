<?php
require_once 'header.php';

$cart_items = $_SESSION['cart'];
if (empty($cart_items) && !isset($_GET['success'])) {
    header("Location: cart.php");
    exit;
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping_cost = $subtotal >= 150 ? 0 : 15.00;
$grand_total = $subtotal + $shipping_cost;

$order_success = false;
$saved_order_id = 0;
$error_message = '';

// Handle Order Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $shipping_name = sanitize($_POST['shipping_name']);
    $shipping_email = sanitize($_POST['shipping_email']);
    $shipping_phone = sanitize($_POST['shipping_phone']);
    $shipping_address = sanitize($_POST['shipping_address']);
    $payment_method = sanitize($_POST['payment_method']);
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

    try {
        $pdo->beginTransaction();

        // 1. Create order record
        $stmt = $pdo->prepare("INSERT INTO orders (user_id, total_amount, shipping_name, shipping_email, shipping_phone, shipping_address, payment_method, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$user_id, $grand_total, $shipping_name, $shipping_email, $shipping_phone, $shipping_address, $payment_method]);
        $order_id = $pdo->lastInsertId();

        // 2. Insert order items & verify/update catalog stock levels
        foreach ($cart_items as $key => $item) {
            // Verify stock first
            $stmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stmt->execute([$item['id']]);
            $current_stock = $stmt->fetchColumn();

            if ($current_stock < $item['quantity']) {
                throw new Exception("Insufficent stock available for " . $item['name'] . ". Only " . $current_stock . " remaining.");
            }

            // Create item record
            $stmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, quantity, price, size, color) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['id'], $item['quantity'], $item['price'], $item['size'], $item['color']]);

            // Deduct stock level
            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$item['quantity'], $item['id']]);
        }

        $pdo->commit();
        
        // Reset Cart Session on success
        $_SESSION['cart'] = [];
        
        // Redirect to success state
        header("Location: checkout.php?success=1&id=" . $order_id);
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error_message = $e->getMessage();
    }
}

if (isset($_GET['success']) && isset($_GET['id'])) {
    $order_success = true;
    $saved_order_id = (int)$_GET['id'];
}
?>

<section class="section-padding">
    <div class="container">
        
        <?php if ($order_success): ?>
            <!-- Success Confirmation State -->
            <div class="empty-state animate-fade-in" style="max-width: 650px; margin: 0 auto; border-style: solid;">
                <i class="fas fa-check-circle" style="font-size: 4rem; color: var(--success); margin-bottom: 1.5rem;"></i>
                <span class="subtitle">Thank You</span>
                <h2 style="margin: 0.5rem 0 1.5rem;">Order Placed Successfully</h2>
                <p>Your order <strong>#VV-<?php echo sprintf('%05d', $saved_order_id); ?></strong> has been received. We will send shipping confirmations and delivery tracking details to your registered email shortly.</p>
                <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 2.5rem;">
                    <a href="shop.php" class="btn btn-primary">Continue Shopping</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="btn btn-secondary">View Order History</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php else: ?>
            
            <span class="subtitle">Secure checkout</span>
            <h1 style="margin: 0.5rem 0 3rem; font-size: clamp(2rem, 4vw, 3rem);">Billing & Delivery</h1>

            <?php if ($error_message): ?>
                <div class="alert alert-danger animate-fade-in">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <div class="checkout-layout">
                <!-- Shipping Details Form -->
                <div class="animate-fade-in">
                    <form id="checkout-form" action="checkout.php" method="POST">
                        <input type="hidden" name="action" value="place_order">

                        <h3 style="font-family: var(--font-body); font-size: 1.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Shipping Address</h3>
                        
                        <div class="form-group">
                            <label for="shipping_name">Full Name *</label>
                            <input type="text" id="shipping_name" name="shipping_name" class="form-control" required value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                        </div>

                        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label for="shipping_email">Email Address *</label>
                                <input type="email" id="shipping_email" name="shipping_email" class="form-control" required value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                            </div>
                            <div>
                                <label for="shipping_phone">Phone Number *</label>
                                <input type="tel" id="shipping_phone" name="shipping_phone" class="form-control" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="shipping_address">Delivery Address *</label>
                            <textarea id="shipping_address" name="shipping_address" class="form-control" required placeholder="Street address, apartment, city, zip-code"></textarea>
                        </div>

                        <!-- Mock Payment Block (Elegant outline styling, no gradients) -->
                        <h3 style="font-family: var(--font-body); font-size: 1.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 3.5rem; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Payment Method</h3>
                        
                        <div class="form-group" style="display: flex; gap: 1.5rem; margin-bottom: 2rem;">
                            <label class="filter-checkbox-label" style="font-weight: 600;">
                                <input type="radio" name="payment_method" value="Credit Card" checked style="accent-color: var(--accent-gold);">
                                Credit / Debit Card
                            </label>
                            <label class="filter-checkbox-label">
                                <input type="radio" name="payment_method" value="Cash On Delivery" style="accent-color: var(--accent-gold);">
                                Cash On Delivery
                            </label>
                        </div>

                        <div id="card-details-fields" style="background-color: var(--bg-secondary); border: 1px solid var(--border-color); padding: 2rem; margin-bottom: 2.5rem;">
                            <div class="form-group">
                                <label for="cc_number">Card Number *</label>
                                <input type="text" id="cc_number" name="cc_number" class="form-control" placeholder="0000 0000 0000 0000" maxlength="19">
                            </div>
                            <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 0;">
                                <div>
                                    <label for="cc_expiry">Expiration Date *</label>
                                    <input type="text" id="cc_expiry" name="cc_expiry" class="form-control" placeholder="MM/YY" maxlength="5">
                                </div>
                                <div>
                                    <label for="cc_cvv">Security Code (CVV) *</label>
                                    <input type="password" id="cc_cvv" name="cc_cvv" class="form-control" placeholder="***" maxlength="4">
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary btn-full" style="height: 55px;">Place Order ($<?php echo number_format($grand_total, 2); ?>)</button>
                    </form>
                </div>

                <!-- Order items summary review -->
                <div class="order-review-card animate-slide-up">
                    <h3 style="font-family: var(--font-body); font-size: 1.15rem; text-transform: uppercase; letter-spacing: 0.1em; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Review Order</h3>
                    
                    <div style="max-height: 300px; overflow-y: auto; margin-bottom: 2rem; padding-right: 0.5rem;">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="order-review-item">
                                <div>
                                    <h4 style="font-family: var(--font-body); font-size: 0.95rem; font-weight: 500;"><?php echo htmlspecialchars($item['name']); ?></h4>
                                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.25rem;">Size: <?php echo htmlspecialchars($item['size']); ?> / Color: <?php echo htmlspecialchars($item['color']); ?> x<?php echo $item['quantity']; ?></p>
                                </div>
                                <span style="font-weight: 600;">$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row" style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <span>Subtotal</span>
                        <span>$<?php echo number_format($subtotal, 2); ?></span>
                    </div>
                    <div class="summary-row" style="margin-bottom: 1rem; border-bottom: 1px solid var(--border-color); padding-bottom: 1rem;">
                        <span>Shipping</span>
                        <span><?php echo $shipping_cost == 0 ? 'FREE' : '$15.00'; ?></span>
                    </div>
                    <div class="summary-row total" style="margin-bottom: 0;">
                        <span>Total Due</span>
                        <span>$<?php echo number_format($grand_total, 2); ?></span>
                    </div>
                </div>
            </div>

            <!-- Interactivity script for checkout payment toggle -->
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const paymentRadios = document.getElementsByName('payment_method');
                    const cardDetails = document.getElementById('card-details-fields');
                    const ccNum = document.getElementById('cc_number');
                    const ccExp = document.getElementById('cc_expiry');
                    const ccCvv = document.getElementById('cc_cvv');

                    function togglePaymentFields() {
                        let isCard = true;
                        for (let radio of paymentRadios) {
                            if (radio.checked && radio.value === 'Cash On Delivery') {
                                isCard = false;
                            }
                        }

                        if (isCard) {
                            cardDetails.style.display = 'block';
                            ccNum.required = true;
                            ccExp.required = true;
                            ccCvv.required = true;
                        } else {
                            cardDetails.style.display = 'none';
                            ccNum.required = false;
                            ccExp.required = false;
                            ccCvv.required = false;
                            ccNum.value = '';
                            ccExp.value = '';
                            ccCvv.value = '';
                        }
                    }

                    for (let radio of paymentRadios) {
                        radio.addEventListener('change', togglePaymentFields);
                    }

                    // Run initially
                    togglePaymentFields();
                });
            </script>
        <?php endif; ?>

    </div>
</section>

<?php require_once 'footer.php'; ?>
