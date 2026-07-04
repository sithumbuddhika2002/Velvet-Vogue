<?php
require_once 'header.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch product details
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    echo '<section class="section-padding"><div class="container"><div class="empty-state"><h3>Product Not Found</h3><p>The product you are looking for does not exist or has been removed.</p><a href="shop.php" class="btn btn-primary">Back to Shop</a></div></div></section>';
    require_once 'footer.php';
    exit;
}

$success_message = '';

// Handle Add To Cart POST action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_to_cart') {
    $size = isset($_POST['size']) ? sanitize($_POST['size']) : 'One Size';
    $color = isset($_POST['color']) ? sanitize($_POST['color']) : 'Default';
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

    // Check quantity bounds
    if ($quantity < 1) $quantity = 1;
    if ($quantity > $product['stock']) $quantity = $product['stock'];

    // Unique cart key to differentiate identical items with different sizes/colors
    $cart_key = $product['id'] . '_' . strtolower(str_replace(' ', '', $size)) . '_' . strtolower(str_replace(' ', '', $color));

    if (isset($_SESSION['cart'][$cart_key])) {
        // Adjust existing quantity (respecting max stock limit)
        $new_qty = $_SESSION['cart'][$cart_key]['quantity'] + $quantity;
        if ($new_qty > $product['stock']) {
            $_SESSION['cart'][$cart_key]['quantity'] = $product['stock'];
        } else {
            $_SESSION['cart'][$cart_key]['quantity'] = $new_qty;
        }
    } else {
        $_SESSION['cart'][$cart_key] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $product['price'],
            'image_url' => $product['image_url'],
            'size' => $size,
            'color' => $color,
            'quantity' => $quantity
        ];
    }
    $success_message = "Successfully added to your shopping bag!";
}

// Explode sizes and colors
$sizes_array = !empty($product['sizes']) ? array_map('trim', explode(',', $product['sizes'])) : [];
$colors_array = !empty($product['colors']) ? array_map('trim', explode(',', $product['colors'])) : [];
?>

<section class="section-padding">
    <div class="container">
        
        <?php if ($success_message): ?>
            <div class="alert alert-success animate-fade-in" style="display: flex; justify-content: space-between; align-items: center;">
                <span><i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $success_message; ?></span>
                <div style="display: flex; gap: 1rem;">
                    <a href="cart.php" style="text-decoration: underline; font-weight: 600;">View Bag</a>
                    <a href="shop.php" style="text-decoration: underline;">Continue Shopping</a>
                </div>
            </div>
        <?php endif; ?>

        <div class="product-detail-layout">
            <!-- Product Gallery/Image -->
            <div class="product-gallery animate-fade-in">
                <?php 
                $image_path = 'images/' . $product['image_url'];
                if (!empty($product['image_url']) && file_exists($image_path)): 
                ?>
                    <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                <?php else: ?>
                    <div class="product-image-placeholder" style="height: 100%;">
                        <i class="fas fa-shirt" style="font-size: 5rem; margin-bottom: 2rem;"></i>
                        <span style="font-size: 1.1rem; letter-spacing: 0.15em; font-family: var(--font-heading);"><?php echo htmlspecialchars($product['name']); ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Product Purchase details panel -->
            <div class="product-info-panel animate-slide-up">
                <span class="product-cat"><?php echo htmlspecialchars(ucfirst($product['category'])); ?> / <?php echo htmlspecialchars(ucfirst($product['gender'])); ?></span>
                <h1 style="margin: 0.5rem 0 1.5rem;"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <div class="detail-meta">
                    <span class="detail-price">$<?php echo number_format($product['price'], 2); ?></span>
                    
                    <?php if ($product['stock'] > 0): ?>
                        <span class="badge-status" style="background-color: rgba(16, 185, 129, 0.15); color: var(--success); border: 1px solid var(--success); font-size: 0.75rem;">In Stock (<?php echo $product['stock']; ?> left)</span>
                    <?php else: ?>
                        <span class="badge-status" style="background-color: rgba(239, 68, 68, 0.15); color: var(--danger); border: 1px solid var(--danger); font-size: 0.75rem;">Out of Stock</span>
                    <?php endif; ?>
                </div>

                <p class="detail-desc"><?php echo htmlspecialchars($product['description']); ?></p>

                <?php if ($product['stock'] > 0): ?>
                    <form id="add-to-cart-form" action="product.php?id=<?php echo $product['id']; ?>" method="POST">
                        <input type="hidden" name="action" value="add_to_cart">
                        
                        <!-- Size Selector -->
                        <?php if (!empty($sizes_array) && $sizes_array[0] !== 'One Size'): ?>
                            <div class="detail-selection-block">
                                <h4>Select Size</h4>
                                <div class="size-pill-group">
                                    <?php foreach ($sizes_array as $idx => $size): ?>
                                        <input type="radio" id="detail_size_<?php echo $size; ?>" name="size" value="<?php echo $size; ?>" <?php echo $idx === 0 ? 'required' : ''; ?>>
                                        <label for="detail_size_<?php echo $size; ?>" class="size-pill-label"><?php echo $size; ?></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Color Selector -->
                        <?php if (!empty($colors_array) && $colors_array[0] !== 'Default'): ?>
                            <div class="detail-selection-block">
                                <h4>Select Color</h4>
                                <div class="color-circle-group">
                                    <?php 
                                    foreach ($colors_array as $idx => $color): 
                                        // Standard color associations to show solid background blocks (No Gradients)
                                        $bg_style = 'background-color: #333;';
                                        $lower_color = strtolower($color);
                                        if ($lower_color === 'black' || $lower_color === 'onyx') $bg_style = 'background-color: #121212; border: 1px solid #333;';
                                        else if ($lower_color === 'white' || $lower_color === 'ivory') $bg_style = 'background-color: #f7f7f7; border: 1px solid #ccc;';
                                        else if ($lower_color === 'sand' || $lower_color === 'oatmeal') $bg_style = 'background-color: #e5d3b3;';
                                        else if ($lower_color === 'navy') $bg_style = 'background-color: #0b1d3a;';
                                        else if ($lower_color === 'olive' || $lower_color === 'sage') $bg_style = 'background-color: #4a5d4e;';
                                        else if ($lower_color === 'emerald') $bg_style = 'background-color: #046340;';
                                        else if ($lower_color === 'champagne') $bg_style = 'background-color: #eeddb7;';
                                        else if ($lower_color === 'tan' || $lower_color === 'chestnut') $bg_style = 'background-color: #a0522d;';
                                        else if ($lower_color === 'burgundy') $bg_style = 'background-color: #6e1c24;';
                                        else if ($lower_color === 'terracotta') $bg_style = 'background-color: #c36241;';
                                    ?>
                                        <input type="radio" id="detail_color_<?php echo $color; ?>" name="color" value="<?php echo $color; ?>" <?php echo $idx === 0 ? 'required' : ''; ?>>
                                        <label for="detail_color_<?php echo $color; ?>" class="color-circle-label" style="<?php echo $bg_style; ?>" title="<?php echo htmlspecialchars($color); ?>"></label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Quantity Selector and CTA Button -->
                        <div class="detail-selection-block" style="display: flex; flex-direction: column; gap: 1.5rem;">
                            <div>
                                <h4>Quantity</h4>
                                <div class="qty-selector">
                                    <button type="button" class="qty-btn qty-minus">-</button>
                                    <input type="number" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" class="qty-input" readonly>
                                    <button type="button" class="qty-btn qty-plus">+</button>
                                </div>
                            </div>
                            
                            <button type="submit" class="btn btn-primary" style="height: 55px; width: 100%; max-width: 350px;">Add to Bag</button>
                        </div>
                    </form>
                <?php else: ?>
                    <button class="btn btn-secondary btn-full" disabled style="cursor: not-allowed; opacity: 0.5; max-width: 350px;">Out of Stock</button>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
