<?php
session_start();
?>
<?php include 'header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="hero-content text-center">
                    <h1 class="display-4 fw-bold mb-4">Welcome to Online Voting System</h1>
                    <p class="lead mb-4">Secure, transparent, and efficient digital voting platform for modern elections</p>
                    <div class="hero-buttons">
                        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                            <a href="dashboard.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-tachometer-alt me-2"></i>Go to Dashboard
                            </a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary btn-lg me-3">
                                <i class="fas fa-sign-in-alt me-2"></i>Login to Vote
                            </a>
                            <a href="register.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-user-plus me-2"></i>Register
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="display-5 fw-bold text-white">Why Choose Our System?</h2>
            <p class="lead text-white">Experience the future of democratic voting</p>
        </div>
        
        <div class="row g-4">
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h4>Military-Grade Security</h4>
                    <p>Advanced encryption and security protocols ensure your vote remains confidential and tamper-proof</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h4>Real-time Results</h4>
                    <p>Watch live results as they come in with our interactive dashboard and analytics</p>
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="feature-card">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h4>Mobile Friendly</h4>
                    <p>Vote from anywhere using any device - desktop, tablet, or smartphone</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>