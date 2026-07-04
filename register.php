<?php
require_once 'header.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        $error_message = "All fields are required.";
    } else if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else if (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetchColumn() > 0) {
                $error_message = "An account with this email address already exists.";
            } else {
                // Hash Password using bcrypt
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert user
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'customer')");
                $stmt->execute([$name, $email, $hashed_password]);
                
                $user_id = $pdo->lastInsertId();

                // Auto Login
                $_SESSION['user_id'] = $user_id;
                $_SESSION['user_name'] = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['user_role'] = 'customer';

                header("Location: profile.php");
                exit;
            }
        } catch (Exception $e) {
            $error_message = "Registration failed. Error: " . $e->getMessage();
        }
    }
}
?>

<section class="section-padding">
    <div class="container">
        
        <div class="form-card animate-fade-in">
            <div style="text-align: center; margin-bottom: 2.5rem;">
                <span class="subtitle">Join the label</span>
                <h2 style="margin-top: 0.5rem; font-size: 2rem;">Create Account</h2>
            </div>

            <?php if ($error_message): ?>
                <div class="alert alert-danger animate-fade-in">
                    <i class="fas fa-exclamation-circle" style="margin-right: 0.5rem;"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>

            <form action="register.php" method="POST">
                <input type="hidden" name="action" value="register">

                <div class="form-group">
                    <label for="name">Your Name *</label>
                    <input type="text" id="name" name="name" class="form-control" required placeholder="Enter your full name">
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" class="form-control" required placeholder="name@example.com">
                </div>

                <div class="form-group" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" class="form-control" required placeholder="Min 6 characters">
                    </div>
                    <div>
                        <label for="confirm_password">Confirm *</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required placeholder="Re-type password">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-full" style="height: 50px; margin-top: 1rem;">Create Account</button>
            </form>

            <div class="form-footer-link">
                Already have an account? <a href="login.php">Sign In</a>
            </div>
        </div>

    </div>
</section>

<?php require_once 'footer.php'; ?>
