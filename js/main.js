// Velvet Vogue Global Interactivity Script
document.addEventListener('DOMContentLoaded', () => {
    
    // 1. Mobile Menu Drawer Toggle
    const navToggleBtn = document.getElementById('nav-toggle-btn');
    const navMenu = document.getElementById('nav-menu');
    const navOverlay = document.getElementById('nav-overlay');
    
    function toggleMobileMenu() {
        if (navToggleBtn && navMenu && navOverlay) {
            const isOpen = navMenu.classList.toggle('active');
            navToggleBtn.classList.toggle('active', isOpen);
            navOverlay.classList.toggle('active', isOpen);
            
            // Prevent body scroll when menu drawer is open (UX best practice)
            if (isOpen) {
                document.body.style.overflow = 'hidden';
            } else {
                document.body.style.overflow = '';
            }
        }
    }

    if (navToggleBtn) {
        navToggleBtn.addEventListener('click', toggleMobileMenu);
    }
    
    if (navOverlay) {
        navOverlay.addEventListener('click', toggleMobileMenu);
    }

    // Close mobile menu when clicking outside the drawer
    document.addEventListener('click', (e) => {
        if (navMenu && navMenu.classList.contains('active') && 
            navToggleBtn && !navToggleBtn.contains(e.target) && 
            !navMenu.contains(e.target) && 
            (!navOverlay || !navOverlay.contains(e.target))) {
            toggleMobileMenu();
        }
    });

    // 2. Sticky Header Scroll Interaction
    const header = document.getElementById('site-header');
    const hero = document.querySelector('.hero');
    if (header) {
        const hasHero = !!hero;
        
        const handleScroll = () => {
            if (window.scrollY > 50) {
                header.classList.add('header-scrolled');
                header.classList.remove('has-hero-top');
            } else {
                header.classList.remove('header-scrolled');
                if (hasHero) {
                    header.classList.add('has-hero-top');
                }
            }
        };
        
        // Run once on load
        handleScroll();
        window.addEventListener('scroll', handleScroll);
    }

    // 3. Product Details Page: Quantity Selector
    const qtyMinus = document.querySelector('.qty-minus');
    const qtyPlus = document.querySelector('.qty-plus');
    const qtyInput = document.querySelector('.qty-input');
    
    if (qtyMinus && qtyPlus && qtyInput) {
        qtyMinus.addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 1;
            if (val > 1) {
                qtyInput.value = val - 1;
            }
        });
        
        qtyPlus.addEventListener('click', () => {
            let val = parseInt(qtyInput.value) || 1;
            let max = parseInt(qtyInput.getAttribute('max')) || 99;
            if (val < max) {
                qtyInput.value = val + 1;
            }
        });

        qtyInput.addEventListener('change', () => {
            let val = parseInt(qtyInput.value);
            let max = parseInt(qtyInput.getAttribute('max')) || 99;
            if (isNaN(val) || val < 1) qtyInput.value = 1;
            else if (val > max) qtyInput.value = max;
        });
    }

    // 4. Product Details Page: Add to Cart Selection Validation
    const cartForm = document.getElementById('add-to-cart-form');
    if (cartForm) {
        cartForm.addEventListener('submit', (e) => {
            const sizes = document.getElementsByName('size');
            const colors = document.getElementsByName('color');
            
            let sizeSelected = false;
            let colorSelected = false;
            
            // Check if sizes are present on page and if one is selected
            if (sizes.length > 0) {
                for (let s of sizes) {
                    if (s.checked) {
                        sizeSelected = true;
                        break;
                    }
                }
            } else {
                sizeSelected = true; // No size options required (e.g. accessories)
            }
            
            // Check if colors are present on page and if one is selected
            if (colors.length > 0) {
                for (let c of colors) {
                    if (c.checked) {
                        colorSelected = true;
                        break;
                    }
                }
            } else {
                colorSelected = true; // No color options required
            }
            
            if (!sizeSelected || !colorSelected) {
                e.preventDefault();
                alert('Please select both a Size and Color before adding to cart.');
            }
        });
    }

    // 5. Contact Form Client-side Validation
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            const emailField = document.getElementById('email');
            const nameField = document.getElementById('name');
            const messageField = document.getElementById('message');
            
            if (!nameField.value.trim() || !emailField.value.trim() || !messageField.value.trim()) {
                e.preventDefault();
                alert('Please fill out all required fields.');
                return;
            }
            
            // Basic email validation regex
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(emailField.value.trim())) {
                e.preventDefault();
                alert('Please enter a valid email address.');
            }
        });
    }

    // 6. Checkout Form Client-side Validation
    const checkoutForm = document.getElementById('checkout-form');
    if (checkoutForm) {
        checkoutForm.addEventListener('submit', (e) => {
            const fields = ['shipping_name', 'shipping_email', 'shipping_phone', 'shipping_address', 'cc_number', 'cc_expiry', 'cc_cvv'];
            let allFilled = true;
            
            for (let f of fields) {
                const el = document.getElementById(f);
                if (el && !el.value.trim()) {
                    allFilled = false;
                    el.style.borderColor = 'var(--danger)';
                } else if (el) {
                    el.style.borderColor = 'var(--border-color)';
                }
            }
            
            if (!allFilled) {
                e.preventDefault();
                alert('Please fill out all required billing and payment fields.');
            }
        });
    }

    // 7. Micro-animation scroll reveals (Sleek fade-up effect)
    const fadeEls = document.querySelectorAll('.animate-on-scroll');
    if (fadeEls.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-slide-up');
                    observer.unobserve(entry.target);
                }
            });
        }, {
            threshold: 0.1
        });
        
        fadeEls.forEach(el => observer.observe(el));
    }

    // 8. Dynamic Theme Switcher Logic
    const themeToggleBtn = document.getElementById('theme-toggle');
    const themeToggleIcon = document.getElementById('theme-toggle-icon');
    
    if (themeToggleBtn && themeToggleIcon) {
        themeToggleBtn.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme') || 'light';
            let newTheme = 'light';
            
            if (currentTheme === 'light') {
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

    // 9. Modern 3D Coverflow Slider Logic (Concave Editorial track variant)
    const initCoverflow = () => {
        const coverflow = document.querySelector('.coverflow-container');
        if (!coverflow) return;
        
        const cards = Array.from(coverflow.querySelectorAll('.coverflow-card'));
        const dotsContainer = document.querySelector('.coverflow-dots');
        const prevBtn = document.querySelector('.coverflow-btn-prev');
        const nextBtn = document.querySelector('.coverflow-btn-next');
        
        if (cards.length === 0) return;
        
        // Start in the middle
        let activeIndex = Math.floor(cards.length / 2);
        const totalLooks = cards.length;
        
        const getRelativeIndex = (idx, activeIdx, total) => {
            const diff = idx - activeIdx;
            let rel = ((diff % total) + total) % total;
            if (rel > total / 2) {
                rel -= total;
            }
            return rel;
        };
        
        const getVariantClass = (rel) => {
            if (rel === 0) return 'center';
            if (rel === -1) return 'left';
            if (rel === 1) return 'right';
            if (rel === -2) return 'far-left';
            if (rel === 2) return 'far-right';
            return 'hidden';
        };
        
        // Generate dots
        if (dotsContainer) {
            dotsContainer.innerHTML = '';
            cards.forEach((_, i) => {
                const dot = document.createElement('div');
                dot.className = `coverflow-dot ${i === activeIndex ? 'active' : ''}`;
                dot.dataset.index = i;
                dotsContainer.appendChild(dot);
                
                dot.addEventListener('click', () => {
                    goToSlide(i);
                });
            });
        }
        
        const updateSlider = () => {
            const activeWrapped = ((activeIndex % totalLooks) + totalLooks) % totalLooks;
            const dots = dotsContainer ? dotsContainer.querySelectorAll('.coverflow-dot') : [];
            
            cards.forEach((card, index) => {
                // Reset card classes
                card.classList.remove('center', 'left', 'right', 'far-left', 'far-right', 'hidden');
                
                const rel = getRelativeIndex(index, activeIndex, totalLooks);
                const className = getVariantClass(rel);
                
                card.classList.add(className);
            });
            
            dots.forEach((dot, index) => {
                if (index === activeWrapped) {
                    dot.classList.add('active');
                } else {
                    dot.classList.remove('active');
                }
            });
        };
        
        const goToSlide = (index) => {
            // Determine shortest path to clicked index to prevent wild scrolling
            const currentWrapped = ((activeIndex % totalLooks) + totalLooks) % totalLooks;
            let diff = index - currentWrapped;
            if (diff > totalLooks / 2) diff -= totalLooks;
            if (diff < -totalLooks / 2) diff += totalLooks;
            
            activeIndex += diff;
            updateSlider();
        };
        
        if (prevBtn) {
            prevBtn.addEventListener('click', () => {
                activeIndex -= 1;
                updateSlider();
            });
        }
        
        if (nextBtn) {
            nextBtn.addEventListener('click', () => {
                activeIndex += 1;
                updateSlider();
            });
        }
        
        // Handle click on non-active cards to slide to them, or navigate if active
        cards.forEach((card, index) => {
            card.addEventListener('click', (e) => {
                const total = cards.length;
                const rel = getRelativeIndex(index, activeIndex, total);
                
                if (rel !== 0) {
                    // Prevent normal redirection/navigation since it is not active
                    e.preventDefault();
                    e.stopPropagation();
                    
                    // Determine shortest path to slide
                    const currentWrapped = ((activeIndex % total) + total) % total;
                    let diff = index - currentWrapped;
                    if (diff > total / 2) diff -= total;
                    if (diff < -total / 2) diff += total;
                    
                    activeIndex += diff;
                    updateSlider();
                } else {
                    // It is active! If clicking directly on card or wrapper, let's navigate to target url
                    // If they clicked the <a> link inside, let browser handle the link naturally
                    const targetUrl = card.getAttribute('data-url');
                    if (targetUrl && !e.target.closest('a')) {
                        window.location.href = targetUrl;
                    }
                }
            });
        });
        
        // Swipe gestures (Touch and Mouse drag support)
        let startX = 0;
        let isDragging = false;
        let dragThreshold = 50;
        
        const handleStart = (e) => {
            startX = e.touches ? e.touches[0].clientX : e.clientX;
            isDragging = true;
        };
        
        const handleMove = (e) => {
            if (!isDragging) return;
            const currentX = e.touches ? e.touches[0].clientX : e.clientX;
            const diff = startX - currentX;
            
            if (Math.abs(diff) > dragThreshold) {
                if (diff > 0) {
                    activeIndex += 1;
                } else {
                    activeIndex -= 1;
                }
                updateSlider();
                isDragging = false;
            }
        };
        
        const handleEnd = () => {
            isDragging = false;
        };
        
        const viewport = document.querySelector('.coverflow-viewport');
        if (viewport) {
            viewport.addEventListener('touchstart', handleStart, { passive: true });
            viewport.addEventListener('touchmove', handleMove, { passive: true });
            viewport.addEventListener('touchend', handleEnd);
            
            viewport.addEventListener('mousedown', handleStart);
            viewport.addEventListener('mousemove', handleMove);
            window.addEventListener('mouseup', handleEnd);
        }
        
        updateSlider();
    };
    
    initCoverflow();
});
