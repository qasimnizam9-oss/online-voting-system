<?php
require_once 'database.php';
require_once 'auth.php';

// Get election results
$results_stmt = $pdo->query("
    SELECT p.title as position_title, c.name as candidate_name, c.photo, c.votes,
           (SELECT COUNT(*) FROM votes WHERE position_id = p.id) as total_votes
    FROM positions p
    LEFT JOIN candidates c ON p.id = c.position_id
    ORDER BY p.id, c.votes DESC
");
$results_data = $results_stmt->fetchAll();

// Organize results by position
$results = [];
foreach ($results_data as $row) {
    $position = $row['position_title'];
    if (!isset($results[$position])) {
        $results[$position] = [
            'total_votes' => $row['total_votes'],
            'candidates' => []
        ];
    }
    if ($row['candidate_name']) {
        $percentage = $row['total_votes'] > 0 ? round(($row['votes'] / $row['total_votes']) * 100, 2) : 0;
        $results[$position]['candidates'][] = [
            'name' => $row['candidate_name'],
            'photo' => $row['photo'],
            'votes' => $row['votes'],
            'percentage' => $percentage
        ];
    }
}
?>

<?php include 'header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <div class="dashboard-card">
                <h2 class="text-center mb-4">Election Results</h2>
                <p class="text-center text-muted mb-4">Live results from the current election</p>

                <?php foreach ($results as $position => $data): ?>
                    <div class="position-results mb-5">
                        <h4 class="mb-3"><?php echo htmlspecialchars($position); ?></h4>
                        <p class="text-muted mb-3">Total Votes: <?php echo $data['total_votes']; ?></p>

                        <div class="results-chart">
                            <?php foreach ($data['candidates'] as $candidate): ?>
                                <div class="candidate-result mb-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <img src="<?php echo $candidate['photo'] ?: 'https://via.placeholder.com/50'; ?>" 
                                             alt="<?php echo $candidate['name']; ?>" 
                                             class="rounded-circle me-3" width="50" height="50">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0"><?php echo htmlspecialchars($candidate['name']); ?></h6>
                                            <small class="text-muted"><?php echo $candidate['votes']; ?> votes (<?php echo $candidate['percentage']; ?>%)</small>
                                        </div>
                                    </div>
                                    <div class="progress">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?php echo $candidate['percentage']; ?>%"
                                             aria-valuenow="<?php echo $candidate['percentage']; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <hr>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <a href="dashboard.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>