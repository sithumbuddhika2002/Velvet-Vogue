<?php
require_once 'header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = sanitize($_POST['email']);
    $password = $_POST['password']; // Don't sanitize password to preserve characters

    if (empty($email) || empty($password)) {
        $error_message = "Please enter both email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Set Session Variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: admin.php");
                } else {
                    header("Location: profile.php");
                }
                exit;
            } else {
                $error_message = "Invalid email address or password.";
            }
        } catch (Exception $e) {
            $error_message = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<section class="section-padding">
    <div class="container">
        
        <div class="form-card animate-fade-in">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <span class="subtitle">Welcome back</span>
                <h2 style="margin-top: 0.5rem; font-size: 2rem;">Sign In</h2>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger animate-fade-in">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <input type="hidden" name="action" value="login">

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" class="form-control" required placeholder="Enter your password">
                </div>

                <button type="submit" class="btn btn-primary btn-full" style="height: 50px; margin-top: 1rem;">Sign In</button>
            </form>

            <div class="form-footer-link">
                Don't have an account? <a href="register.php">Create Account</a>
            </div>
            
            <div style="margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid var(--border-color); text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                <p><strong>Mock Credentials for Testing:</strong></p>
                <p style="margin-top: 0.25rem;">Admin: <code>admin@velvetvogue.com</code> / <code>admin123</code></p>
                <p>Customer: <code>customer@velvetvogue.com</code> / <code>customer123</code></p>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
