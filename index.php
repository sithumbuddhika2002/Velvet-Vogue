<?php 
require_once 'header.php'; 

$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4");
$featured_products = $stmt->fetchAll();


$hero_video_layout = 'full'; 

$cloudinary_cloud_name = '';
$cloudinary_video_id   = ''; 


$fallback_video_webm   = 'videos/hero.webm'; 
$fallback_video_mp4    = ''; 

?>

<!-- Hero Banner Section -->
<section class="hero <?php echo $hero_video_layout === 'full' ? 'hero-video-full' : ''; ?> animate-fade-in">
    <?php if ($hero_video_layout === 'full'): ?>
        <!-- Full-bleed Background Video Container -->
        <div class="hero-full-video-container">
            <video autoplay loop muted playsinline class="hero-video-bg">
                <?php if (!empty($cloudinary_cloud_name) && !empty($cloudinary_video_id)): ?>
                    <source src="https://res.cloudinary.com/<?php echo htmlspecialchars($cloudinary_cloud_name); ?>/video/upload/f_webm,q_auto/<?php echo htmlspecialchars($cloudinary_video_id); ?>.webm" type="video/webm">
                    <source src="https://res.cloudinary.com/<?php echo htmlspecialchars($cloudinary_cloud_name); ?>/video/upload/f_mp4,q_auto/<?php echo htmlspecialchars($cloudinary_video_id); ?>.mp4" type="video/mp4">
                <?php endif; ?>
                
                <?php if (!empty($fallback_video_webm)): ?>
                    <source src="<?php echo htmlspecialchars($fallback_video_webm); ?>" type="video/webm">
                <?php endif; ?>
                <?php if (!empty($fallback_video_mp4)): ?>
                    <source src="<?php echo htmlspecialchars($fallback_video_mp4); ?>" type="video/mp4">
                <?php endif; ?>
                
                <!-- Ultimate Local Fallback -->
                <source src="images/hero_video.mp4" type="video/mp4">
            </video>
            <div class="hero-full-video-overlay"></div>
        </div>
    <?php endif; ?>

    <div class="container hero-container-flex">
        <div class="hero-content">
            <span class="hero-tagline hero-anim-1">Velvet Vogue / New Season</span>
            <h1 class="hero-anim-2">Express Your <em>True Identity</em> Through Style</h1>
            <p class="hero-desc hero-anim-3">Discover our curated collection of premium casualwear and tailored formal wear designed for young adults who seek quality, sustainability, and absolute confidence.</p>
            <div class="hero-actions hero-anim-4">
                <a href="shop.php" class="btn btn-primary">Explore Collection</a>
                <a href="shop.php?category=formal" class="btn btn-secondary">Shop Formal</a>
            </div>
        </div>
        
        <?php if ($hero_video_layout !== 'full'): ?>
            <!-- Custom Graphic Box for premium handcrafted feel with background video -->
            <div class="hero-bg-accent">
                <div class="hero-canvas-art">
                    <video autoplay loop muted playsinline class="hero-video-bg">
                        <?php if (!empty($cloudinary_cloud_name) && !empty($cloudinary_video_id)): ?>
                            <source src="https://res.cloudinary.com/<?php echo htmlspecialchars($cloudinary_cloud_name); ?>/video/upload/f_webm,q_auto/<?php echo htmlspecialchars($cloudinary_video_id); ?>.webm" type="video/webm">
                            <source src="https://res.cloudinary.com/<?php echo htmlspecialchars($cloudinary_cloud_name); ?>/video/upload/f_mp4,q_auto/<?php echo htmlspecialchars($cloudinary_video_id); ?>.mp4" type="video/mp4">
                        <?php endif; ?>
                        
                        <?php if (!empty($fallback_video_webm)): ?>
                            <source src="<?php echo htmlspecialchars($fallback_video_webm); ?>" type="video/webm">
                        <?php endif; ?>
                        <?php if (!empty($fallback_video_mp4)): ?>
                            <source src="<?php echo htmlspecialchars($fallback_video_mp4); ?>" type="video/mp4">
                        <?php endif; ?>
                        
                        <!-- Ultimate Local Fallback -->
                        <source src="images/hero_video.mp4" type="video/mp4">
                    </video>
                    <div class="hero-video-overlay"></div>
                    <div class="hero-canvas-content">
                        <h3>Autumn/Winter Collection</h3>
                        <span>Premium Solid Designs</span>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($hero_video_layout === 'full'): ?>
        <!-- Scroll Down Indicator -->
        <div class="hero-scroll-indicator">
            <span class="scroll-text">Explore</span>
            <div class="scroll-mouse">
                <div class="scroll-wheel"></div>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Brand Values -->
<section class="section-padding" style="background-color: var(--bg-secondary); border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 3.5rem;">
            <div class="animate-on-scroll" style="opacity: 0;">
                <div style="font-size: 1.5rem; color: var(--accent-gold); margin-bottom: 1rem;"><i class="fas fa-signature"></i></div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Curated Quality</h4>
                <p style="font-size: 0.95rem; line-height: 1.7;">Every piece in our catalog is handpicked for its premium materials, comfort, and long-lasting durability.</p>
            </div>
            <div class="animate-on-scroll" style="opacity: 0;">
                <div style="font-size: 1.5rem; color: var(--accent-gold); margin-bottom: 1rem;"><i class="fas fa-leaf"></i></div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Organic & Sustainable</h4>
                <p style="font-size: 0.95rem; line-height: 1.7;">We prioritize organic cotton, pure silk, and ethically sourced cashmere to protect both you and our planet.</p>
            </div>
            <div class="animate-on-scroll" style="opacity: 0;">
                <div style="font-size: 1.5rem; color: var(--accent-gold); margin-bottom: 1rem;"><i class="fas fa-shipping-fast"></i></div>
                <h4 style="font-size: 1.15rem; margin-bottom: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">Free Express Shipping</h4>
                <p style="font-size: 0.95rem; line-height: 1.7;">Enjoy free express delivery on all orders over $150. Tracked shipping directly from our store to your door.</p>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Categories Section (Restructured as Modern 3D Coverflow Slider) -->
<section class="section-padding curated-looks-section animate-on-scroll" style="opacity: 0;">
    <div class="container">
        <div class="section-header">
            <div class="title-area">
                <span class="subtitle">Collections</span>
                <h2>Browse Curated Lines</h2>
            </div>
            <a href="shop.php" class="btn btn-secondary btn-sm" style="padding: 0.75rem 1.5rem; font-size: 0.75rem;">View All Catalog</a>
        </div>
        
        <!-- 3D Coverflow Viewport -->
        <div class="coverflow-viewport">
            <div class="coverflow-container">
                <!-- Look 01 -->
                <div class="coverflow-card" data-url="shop.php?gender=men">
                    <img src="images/wool_blazer.jpg" alt="Sartorial Edge" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 01 / Men's Wardrobe</span>
                        <h3>Sartorial Edge</h3>
                        <p>Sharp lines and refined modern silhouettes.</p>
                        <a href="shop.php?gender=men" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>

                <!-- Look 02 -->
                <div class="coverflow-card" data-url="shop.php?gender=women">
                    <img src="images/silk_slip.jpg" alt="Fluid Silhouettes" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 02 / Women's Line</span>
                        <h3>Fluid Silhouettes</h3>
                        <p>Graceful drapes and organic flows for effortless elegance.</p>
                        <a href="shop.php?gender=women" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>

                <!-- Look 03 -->
                <div class="coverflow-card" data-url="shop.php?category=casual">
                    <img src="images/linen_shirt.jpg" alt="Coastal Breeze" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 03 / Summer Casuals</span>
                        <h3>Coastal Breeze</h3>
                        <p>Organic linen, effortlessly styled for the warm coast.</p>
                        <a href="shop.php?category=casual" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>

                <!-- Look 04 -->
                <div class="coverflow-card" data-url="shop.php">
                    <img src="images/cotton_tee.jpg" alt="Urban Luxe" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 04 / Streetwear</span>
                        <h3>Urban Luxe</h3>
                        <p>Bold silhouettes where modern streetwear meets premium fabrics.</p>
                        <a href="shop.php" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>

                <!-- Look 05 -->
                <div class="coverflow-card" data-url="shop.php">
                    <img src="images/trench_coat.jpg" alt="Autumn Tailoring" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 05 / Winter Outerwear</span>
                        <h3>Autumn Tailoring</h3>
                        <p>Heavy wool coats paired with soft, luxury cashmere knits.</p>
                        <a href="shop.php" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>

                <!-- Look 06 -->
                <div class="coverflow-card" data-url="shop.php?category=accessories">
                    <img src="images/leather_bag.jpg" alt="Essential Details" class="coverflow-card-img">
                    <div class="coverflow-card-overlay"></div>
                    <div class="coverflow-card-content">
                        <span class="look-tag">LOOK 06 / Accessories</span>
                        <h3>Essential Details</h3>
                        <p>The perfect final touch with handcrafted premium detailing.</p>
                        <a href="shop.php?category=accessories" class="shop-link">Shop This Look <svg width="16" height="8" viewBox="0 0 20 10" fill="none"><path d="M1 5H19M19 5L15 1M19 5L15 9" stroke="#ffffff" stroke-width="2"/></svg></a>
                    </div>
                </div>
            </div>
            
            <!-- Navigation controls -->
            <button class="coverflow-btn coverflow-btn-prev" aria-label="Previous Slide">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="coverflow-btn coverflow-btn-next" aria-label="Next Slide">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Pagination dots -->
        <div class="coverflow-dots"></div>
    </div>
</section>

<!-- Featured / New Arrivals Section -->
<section class="section-padding" style="background-color: var(--bg-secondary); border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color);">
    <div class="container">
        <div class="section-header">
            <div class="title-area">
                <span class="subtitle">New Arrivals</span>
                <h2>Fresh Off The Loom</h2>
            </div>
        </div>

        <div class="product-grid">
            <?php foreach ($featured_products as $product): ?>
                <div class="product-card">
                    <div class="product-image-wrapper">
                        <!-- Image placeholder validation -->
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
    </div>
</section>

<!-- Testimonials / Quote Section -->
<section class="section-padding">
    <div class="container" style="text-align: center; max-width: 800px;">
        <span class="subtitle" style="color: var(--accent-gold); text-transform: uppercase; letter-spacing: 0.2em; font-size: 0.85rem; font-weight: 600;">Testimonial</span>
        <blockquote style="font-family: var(--font-heading); font-size: clamp(1.5rem, 3.5vw, 2.5rem); line-height: 1.4; color: var(--text-primary); margin: 2rem 0; font-style: italic;">
            "Velvet Vogue is redefining the way I build my daily wardrobe. Their items fit perfectly, feel incredibly soft, and survive countless washes. True quality."
        </blockquote>
        <cite style="font-style: normal; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 0.15em; color: var(--text-secondary); font-weight: 600;">
            &mdash; Marcus Vance, London
        </cite>
    </div>
</section>

<!-- Newsletter Section -->
<section class="section-padding" style="background-color: var(--bg-tertiary); border-top: 1px solid var(--border-color);">
    <div class="container" style="text-align: center; max-width: 650px;">
        <span class="subtitle" style="color: var(--accent-gold); text-transform: uppercase; letter-spacing: 0.2em; font-size: 0.85rem; font-weight: 600; display: block; margin-bottom: 1rem;">Join the Inner Circle</span>
        <h2 style="margin-bottom: 1.5rem; font-size: 2.25rem;">Stay Informed on Private Drops</h2>
        <p style="margin-bottom: 2.5rem; color: var(--text-secondary);">Sign up to receive early access to new seasonal collections, limited-run catalog collaborations, and private boutique sales events.</p>
        
        <form style="display: flex; gap: 0.5rem; width: 100%; flex-wrap: wrap;" onsubmit="event.preventDefault(); alert('Thank you for subscribing to Velvet Vogue.');">
            <input type="email" placeholder="Enter your email address" required style="flex-grow: 1; padding: 1.15rem 1.5rem; border: 1px solid var(--border-color); background-color: var(--bg-primary); color: #fff; outline: none; transition: var(--transition-fast);" onfocus="this.style.borderColor='var(--accent-gold)';" onblur="this.style.borderColor='var(--border-color)';">
            <button type="submit" class="btn btn-primary" style="padding: 1.15rem 2rem;">Subscribe</button>
        </form>
    </div>
</section>

<?php require_once 'footer.php'; ?>
