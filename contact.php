<?php
require_once 'header.php';

$success_message = '';
$error_message = '';

// Handle Inquiry Form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_inquiry') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $subject = sanitize($_POST['subject']);
    $message = sanitize($_POST['message']);

    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required. Please fill out the form completely.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO inquiries (name, email, subject, message) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $subject, $message]);
            $success_message = "Thank you for reaching out! Your inquiry has been received. Our customer service team will contact you within 24 hours.";
        } catch (Exception $e) {
            $error_message = "Failed to submit inquiry. Please try again later. Error: " . $e->getMessage();
        }
    }
}
?>

<section class="section-padding">
    <div class="container">
        
        <span class="subtitle">Get in Touch</span>
        <h1 style="margin: 0.5rem 0 3rem; font-size: clamp(2rem, 4vw, 3rem);">Contact & Inquiries</h1>

        <?php if ($success_message): ?>
            <div class="alert alert-success animate-fade-in">
                <i class="fas fa-check-circle" style="margin-right: 0.5rem;"></i> <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger animate-fade-in">
                <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="contact-layout">
            <!-- Inquiry Form -->
            <div class="animate-fade-in">
                <div class="form-card" style="margin: 0; max-width: 100%;">
                    <h3 style="font-family: var(--font-body); font-size: 1.25rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 2rem; border-bottom: 1px solid var(--border-color); padding-bottom: 0.5rem;">Send a Message</h3>
                    
                    <form id="contact-form" action="contact.php" method="POST">
                        <input type="hidden" name="action" value="submit_inquiry">

                        <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div>
                                <label for="name">Your Name *</label>
                                <input type="text" id="name" name="name" class="form-control" required value="<?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : ''; ?>">
                            </div>
                            <div>
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" class="form-control" required value="<?php echo isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : ''; ?>">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">Subject *</label>
                            <input type="text" id="subject" name="subject" class="form-control" required placeholder="What is your inquiry about?">
                        </div>

                        <div class="form-group">
                            <label for="message">Message *</label>
                            <textarea id="message" name="message" class="form-control" required placeholder="Type your message details here..."></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem;">Submit Message</button>
                    </form>
                </div>
            </div>

            <!-- Store Details Panels -->
            <div class="info-cards animate-slide-up">
                <div class="info-card">
                    <h4>Velvet Vogue Boutique</h4>
                    <p style="margin-top: 0.5rem;"><i class="fas fa-location-dot" style="margin-right: 0.5rem; color: var(--accent-gold);"></i> 45 Fashion Boulevard, Suite A<br>SoHo, New York, NY 10012</p>
                    <p style="margin-top: 0.5rem;"><i class="fas fa-phone" style="margin-right: 0.5rem; color: var(--accent-gold);"></i> +1 (212) 555-8965</p>
                    <p style="margin-top: 0.5rem;"><i class="fas fa-envelope" style="margin-right: 0.5rem; color: var(--accent-gold);"></i> support@velvetvogue.com</p>
                </div>

                <div class="info-card">
                    <h4>Opening Hours</h4>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.75rem; font-size: 0.95rem;">
                        <span style="color: var(--text-secondary);">Monday - Friday</span>
                        <span>10:00 AM - 8:00 PM</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.95rem;">
                        <span style="color: var(--text-secondary);">Saturday</span>
                        <span>10:00 AM - 6:00 PM</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-top: 0.5rem; font-size: 0.95rem; border-top: 1px solid var(--border-color); padding-top: 0.5rem;">
                        <span style="color: var(--text-secondary);">Sunday</span>
                        <span style="color: var(--accent-gold); font-weight: 500;">Closed</span>
                    </div>
                </div>

                <div class="info-card" style="border-color: var(--accent-gold);">
                    <h4>Customer Support Portal</h4>
                    <p style="font-size: 0.85rem; line-height: 1.6; color: var(--text-secondary);">For order cancellations, bulk custom tailored fitting bookings, or delivery issues, please attach your Order Reference ID in the subject line.</p>
                </div>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
