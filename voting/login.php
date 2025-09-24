<?php
session_start();
require_once 'database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $input_username = trim($_POST['username']);
    $input_password = trim($_POST['password']);

    if (!empty($input_username) && !empty($input_password)) {
        $stmt = $pdo->prepare("SELECT id, username, password FROM users WHERE username = ?");
        $stmt->execute([$input_username]);
        $user = $stmt->fetch();

        if ($user && password_verify($input_password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['loggedin'] = true;

            header("Location: dashboard.php?success=Login successful!");
            exit();
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<?php include 'header.php'; ?>

<div class="auth-container">
    <div class="auth-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold">Login to Your Account</h2>
            <p class="text-muted">Enter your credentials to access the voting system</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required 
                       placeholder="Enter your username">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required 
                       placeholder="Enter your password">
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2">
                <i class="fas fa-sign-in-alt me-2"></i>Login
            </button>
        </form>

        <div class="text-center mt-3">
            <p class="mb-0">Don't have an account? 
                <a href="register.php" class="text-decoration-none">Register here</a>
            </p>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>