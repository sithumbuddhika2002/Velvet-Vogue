<?php
require_once 'header.php';

// Capture and sanitize filter parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$genders_filter = isset($_GET['gender']) ? (array)$_GET['gender'] : [];
$categories_filter = isset($_GET['category']) ? (array)$_GET['category'] : [];
$sizes_filter = isset($_GET['size']) ? (array)$_GET['size'] : [];
$max_price_filter = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 300.00;

// Base query construction
$sql = "SELECT * FROM products WHERE 1=1";
$params = [];

if ($search !== '') {
    $sql .= " AND (name LIKE :search OR description LIKE :search)";
    $params['search'] = '%' . $search . '%';
}

if (!empty($genders_filter)) {
    $gender_placeholders = [];
    foreach ($genders_filter as $idx => $g) {
        $key = ":gender_" . $idx;
        $gender_placeholders[] = $key;
        $params[$key] = $g;
    }
    $sql .= " AND gender IN (" . implode(',', $gender_placeholders) . ")";
}

if (!empty($categories_filter)) {
    $category_placeholders = [];
    foreach ($categories_filter as $idx => $c) {
        $key = ":category_" . $idx;
        $category_placeholders[] = $key;
        $params[$key] = $c;
    }
    $sql .= " AND category IN (" . implode(',', $category_placeholders) . ")";
}

if (!empty($sizes_filter)) {
    $size_clauses = [];
    foreach ($sizes_filter as $idx => $s) {
        $key = ":size_" . $idx;
        // Search comma separated sizes column using LIKE or FIND_IN_SET
        $size_clauses[] = "FIND_IN_SET($key, REPLACE(sizes, ' ', '')) > 0";
        $params[$key] = $s;
    }
    $sql .= " AND (" . implode(" OR ", $size_clauses) . ")";
}

if ($max_price_filter > 0) {
    $sql .= " AND price <= :max_price";
    $params['max_price'] = $max_price_filter;
}

$sql .= " ORDER BY id DESC";

// Execute SQL
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<section class="section-padding">
    <div class="container">
        
        <!-- Page Title & Search Bar -->
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem; margin-bottom: 4rem;">
            <div>
                <span class="subtitle">Velvet Vogue Catalog</span>
                <h1 style="font-size: clamp(2rem, 4vw, 3rem);">Browse Collection</h1>
            </div>
            
            <div style="width: 100%; max-width: 400px;">
                <form action="shop.php" method="GET" class="search-input-group">
                    <!-- Retain other filter URL parameters when searching -->
                    <?php foreach ($genders_filter as $g): ?>
                        <input type="hidden" name="gender[]" value="<?php echo htmlspecialchars($g); ?>">
                    <?php endforeach; ?>
                    <?php foreach ($categories_filter as $c): ?>
                        <input type="hidden" name="category[]" value="<?php echo htmlspecialchars($c); ?>">
                    <?php endforeach; ?>
                    <?php foreach ($sizes_filter as $s): ?>
                        <input type="hidden" name="size[]" value="<?php echo htmlspecialchars($s); ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="max_price" value="<?php echo htmlspecialchars($max_price_filter); ?>">
                    
                    <input type="text" name="search" placeholder="Search product name..." value="<?php echo htmlspecialchars($search); ?>">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>

        <div class="shop-layout">
            <!-- Sidebar Filters Panel -->
            <aside class="filter-sidebar">
                <form action="shop.php" method="GET">
                    <!-- Maintain search value -->
                    <?php if ($search !== ''): ?>
                        <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
                    <?php endif; ?>

                    <!-- Gender Filter Group -->
                    <div class="filter-section">
                        <h4>Gender</h4>
                        <div class="filter-list">
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="gender[]" value="men" <?php echo in_array('men', $genders_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Men
                            </label>
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="gender[]" value="women" <?php echo in_array('women', $genders_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Women
                            </label>
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="gender[]" value="unisex" <?php echo in_array('unisex', $genders_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Unisex
                            </label>
                        </div>
                    </div>

                    <!-- Category Filter Group -->
                    <div class="filter-section">
                        <h4>Category</h4>
                        <div class="filter-list">
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="category[]" value="casual" <?php echo in_array('casual', $categories_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Casualwear
                            </label>
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="category[]" value="formal" <?php echo in_array('formal', $categories_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Formalwear
                            </label>
                            <label class="filter-checkbox-label">
                                <input type="checkbox" name="category[]" value="accessories" <?php echo in_array('accessories', $categories_filter) ? 'checked' : ''; ?> onchange="this.form.submit();">
                                Accessories
                            </label>
                        </div>
                    </div>

                    <!-- Size Filter Group -->
                    <div class="filter-section">
                        <h4>Sizes</h4>
                        <div class="size-pill-group">
                            <?php 
                            $sizes_list = ['XS', 'S', 'M', 'L', 'XL', 'XXL', 'One Size'];
                            foreach ($sizes_list as $size): 
                                $checked = in_array($size, $sizes_filter) ? 'checked' : '';
                            ?>
                                <input type="checkbox" id="size_<?php echo $size; ?>" name="size[]" value="<?php echo $size; ?>" <?php echo $checked; ?> onchange="this.form.submit();">
                                <label for="size_<?php echo $size; ?>" class="size-pill-label"><?php echo $size; ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Price Slider Filter Group -->
                    <div class="filter-section">
                        <h4>Max Price: <span style="color: var(--accent-gold); font-weight: 600;">$<?php echo number_format($max_price_filter, 2); ?></span></h4>
                        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                            <input type="range" name="max_price" min="20" max="300" step="5" value="<?php echo $max_price_filter; ?>" style="width: 100%; accent-color: var(--accent-gold);" onchange="this.form.submit();">
                            <div style="display: flex; justify-content: space-between; font-size: 0.75rem; color: var(--text-muted);">
                                <span>$20</span>
                                <span>$300</span>
                            </div>
                        </div>
                    </div>

                    <a href="shop.php" class="btn btn-secondary btn-full" style="padding: 0.75rem; font-size: 0.75rem; text-align: center; margin-top: 1rem;">Reset Filters</a>
                </form>
            </aside>

            <!-- Products List Area -->
            <div>
                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <h3>No Products Found</h3>
                        <p>We couldn't find any products matching your specific search queries and filters. Try adjusting your selections.</p>
                        <a href="shop.php" class="btn btn-primary">View All Products</a>
                    </div>
                <?php else: ?>
                    <div class="product-grid animate-fade-in">
                        <?php foreach ($products as $product): ?>
                            <div class="product-card">
                                <div class="product-image-wrapper">
                                    <?php 
                                    $image_path = 'images/' . $product['image_url'];
                                    if (!empty($product['image_url']) && file_exists($image_path)): 
                                    ?>
                                        <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                                    <?php else: ?>
                                        <div class="product-image-placeholder">
                                            <i class="fas fa-shirt"></i>
                                            <span><?php echo htmlspecialchars($product['name']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="product-details">
                                    <div class="product-meta">
                                        <span class="product-cat"><?php echo htmlspecialchars($product['category']); ?></span>
                                        <span class="product-gender"><?php echo htmlspecialchars(ucfirst($product['gender'])); ?></span>
                                    </div>
                                    <h3 class="product-title">
                                        <a href="product.php?id=<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></a>
                                    </h3>
                                    <div class="product-price">$<?php echo number_format($product['price'], 2); ?></div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
