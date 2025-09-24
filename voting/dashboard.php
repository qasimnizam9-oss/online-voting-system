<?php
require_once 'database.php';
require_once 'auth.php';

// Get user's voting status
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT has_voted FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get available positions
$positions_stmt = $pdo->query("SELECT * FROM positions");
$positions = $positions_stmt->fetchAll();

// Get total votes cast
$votes_stmt = $pdo->query("SELECT COUNT(*) as total_votes FROM votes");
$total_votes = $votes_stmt->fetch()['total_votes'];

// Get total users
$users_stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
$total_users = $users_stmt->fetch()['total_users'];
?>

<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Welcome Card -->
            <div class="dashboard-card text-center mb-4">
                <h2 class="fw-bold mb-3">Welcome, <?php echo $_SESSION['username']; ?>! 👋</h2>
                
                <?php if ($user['has_voted']): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle me-2"></i>
                        <strong>You have already voted!</strong>
                        <p class="mb-0">Thank you for participating in the election.</p>
                    </div>
                    <a href="results.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-chart-bar me-2"></i>View Election Results
                    </a>
                <?php else: ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>You are eligible to vote</strong>
                        <p class="mb-0">Please cast your vote before the election ends.</p>
                    </div>
                    <a href="vote.php" class="btn btn-success btn-lg">
                        <i class="fas fa-vote-yea me-2"></i>Cast Your Vote Now
                    </a>
                <?php endif; ?>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-users fa-3x text-primary mb-3"></i>
                        <h3><?php echo $total_users; ?></h3>
                        <p class="text-muted">Total Voters</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-vote-yea fa-3x text-success mb-3"></i>
                        <h3><?php echo $total_votes; ?></h3>
                        <p class="text-muted">Votes Cast</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="dashboard-card text-center">
                        <i class="fas fa-bullhorn fa-3x text-warning mb-3"></i>
                        <h3><?php echo count($positions); ?></h3>
                        <p class="text-muted">Active Positions</p>
                    </div>
                </div>
            </div>

            <!-- Current Elections -->
            <div class="dashboard-card">
                <h3 class="mb-4">Current Elections</h3>
                <div class="row">
                    <?php foreach ($positions as $position): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($position['title']); ?></h5>
                                    <p class="card-text text-muted"><?php echo htmlspecialchars($position['description']); ?></p>
                                    <a href="vote.php" class="btn btn-outline-primary btn-sm">View Candidates</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>