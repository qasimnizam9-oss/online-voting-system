<?php
session_start();
require_once 'database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Username or email already exists.";
        } else {
            // Hash password and insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            
            if ($stmt->execute([$username, $email, $hashed_password])) {
                $success = "Registration successful! You can now login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}
?>

<?php include 'header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Create Account</h2>
            <p class="text-muted">Join our secure voting platform</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required 
                       placeholder="Choose a username">
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" required 
                       placeholder="Enter your email">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required 
                       placeholder="Minimum 6 characters">
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required 
                       placeholder="Confirm your password">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-user-plus me-2"></i>Register
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-0">Already have an account? 
                <a href="login.php" class="text-decoration-none">Login here</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>