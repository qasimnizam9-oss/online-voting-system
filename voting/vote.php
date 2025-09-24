<?php
// Database configuration
$host = 'localhost';
$dbname = 'voting_system';
$username = 'root'; // Change if needed
$password = ''; // Change if needed

// Create connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if form is submitted
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Begin transaction
        $pdo->beginTransaction();
        
        // For demo purposes, we'll use a session-based user ID
        // In a real application, you would get this from your authentication system
        session_start();
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = "user_" . rand(1000, 9999);
        }
        $user_id = $_SESSION['user_id'];
        
        // Check if user has already voted
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM votes WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $result = $stmt->fetch();
        
        if ($result['count'] > 0) {
            $error = 'You have already voted!';
            $pdo->rollBack();
        } else {
            // Process each position vote
            foreach ($_POST as $key => $candidate_id) {
                if (strpos($key, 'position_') === 0 && !empty($candidate_id)) {
                    $position_id = substr($key, 9);
                    
                    // Insert vote into database
                    $stmt = $pdo->prepare("INSERT INTO votes (user_id, candidate_id, position_id) VALUES (?, ?, ?)");
                    $stmt->execute([$user_id, $candidate_id, $position_id]);
                    
                    // Update candidate vote count
                    $stmt = $pdo->prepare("UPDATE candidates SET votes = votes + 1 WHERE id = ?");
                    $stmt->execute([$candidate_id]);
                }
            }
            
            // Mark user as voted
            $stmt = $pdo->prepare("UPDATE users SET has_voted = TRUE WHERE id = ?");
            $stmt->execute([$user_id]);
            
            // Commit transaction
            $pdo->commit();
            
            $success = 'Your vote has been submitted successfully and stored in the database!';
            
            // Reset form after successful submission
            echo '<script>document.getElementById("voteForm").reset();</script>';
            echo '<script>document.querySelectorAll(".candidate-card").forEach(card => card.classList.remove("selected"));</script>';
        }
    } catch(PDOException $e) {
        $pdo->rollBack();
        $error = 'Error submitting vote: ' . $e->getMessage();
    }
}

// Get positions and candidates from database
try {
    $stmt = $pdo->query("
        SELECT p.id as position_id, p.title, p.description, 
               c.id as candidate_id, c.name, c.photo 
        FROM positions p 
        LEFT JOIN candidates c ON p.id = c.position_id 
        ORDER BY p.id, c.name
    ");
    $candidates_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Organize data by position
    $positions = [];
    foreach ($candidates_data as $row) {
        $position_id = $row['position_id'];
        if (!isset($positions[$position_id])) {
            $positions[$position_id] = [
                'id' => $position_id,
                'title' => $row['title'],
                'description' => $row['description'],
                'candidates' => []
            ];
        }
        if ($row['candidate_id']) {
            $positions[$position_id]['candidates'][] = [
                'id' => $row['candidate_id'],
                'name' => $row['name'],
                'photo' => $row['photo'] ?: 'https://via.placeholder.com/100/4e73df/ffffff?text=' . substr($row['name'], 0, 2)
            ];
        }
    }
} catch(PDOException $e) {
    // If database is empty, use default data
    $positions = [
        [
            'id' => 1,
            'title' => 'President',
            'description' => 'Student Union President',
            'candidates' => [
                ['id' => 1, 'name' => 'Emily Davis', 'photo' => 'https://via.placeholder.com/100/4e73df/ffffff?text=ED'],
                ['id' => 2, 'name' => 'Mike Wilson', 'photo' => 'https://via.placeholder.com/100/6f42c1/ffffff?text=MW']
            ]
        ],
        [
            'id' => 2,
            'title' => 'Secretary',
            'description' => 'General Secretary',
            'candidates' => [
                ['id' => 3, 'name' => 'Emily Davis', 'photo' => 'https://via.placeholder.com/100/1cc88a/ffffff?text=ED'],
                ['id' => 4, 'name' => 'Mike Wilson', 'photo' => 'https://via.placeholder.com/100/36b9cc/ffffff?text=MW'],
                ['id' => 5, 'name' => 'Sarah Johnson', 'photo' => 'https://via.placeholder.com/100/f6c23e/ffffff?text=SJ'],
                ['id' => 6, 'name' => 'David Lee', 'photo' => 'https://via.placeholder.com/100/e74a3b/ffffff?text=DL']
            ]
        ]
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Your Vote</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #4e73df;
            --secondary: #6f42c1;
            --success: #1cc88a;
            --danger: #e74a3b;
            --warning: #f6c23e;
            --info: #36b9cc;
            --light: #f8f9fc;
            --dark: #5a5c69;
        }
        
        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            color: #333;
            min-height: 100vh;
            padding: 20px;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .dashboard-card {
            background: rgba(255, 255, 255, 0.97);
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            padding: 25px;
        }
        
        .position-section {
            margin-bottom: 30px;
            padding: 20px;
            border-radius: 10px;
            background: #fff;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .candidate-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: 2px solid transparent;
            height: 100%;
            text-align: center;
            padding: 15px;
        }
        
        .candidate-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }
        
        .candidate-card.selected {
            border-color: var(--success);
            background: #f0f9ff;
        }
        
        .candidate-img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin: 0 auto 15px;
            border: 3px solid var(--primary);
        }
        
        .btn-vote {
            background: var(--success);
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 50px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(28, 200, 138, 0.4);
        }
        
        .btn-vote:hover {
            background: #17a673;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(28, 200, 138, 0.6);
        }
        
        .alert-box {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 350px;
            animation: fadeIn 0.5s, fadeOut 0.5s 4.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
        
        @media (max-width: 768px) {
            .candidate-card {
                margin-bottom: 15px;
            }
            
            .position-section {
                padding: 15px;
            }
            
            .dashboard-card {
                padding: 15px;
            }
        }
        
        .position-title {
            color: var(--primary);
            font-weight: 600;
            margin-bottom: 10px;
        }
        
        .position-description {
            color: var(--dark);
            margin-bottom: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .header h2 {
            color: var(--primary);
            font-weight: 700;
        }
        
        .header p {
            color: var(--dark);
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container py-5">
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="dashboard-card">
                    <div class="header">
                        <h2><i class="fas fa-vote-yea me-2"></i>Cast Your Vote</h2>
                        <p>Select your preferred candidate for each position</p>
                    </div>

                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($success): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="voteForm" method="POST" action="">
                        <?php foreach ($positions as $position): ?>
                            <div class="position-section">
                                <h4 class="position-title"><?php echo $position['title']; ?> - <?php echo $position['description']; ?></h4>
                                <p class="position-description">Vote for your preferred candidate</p>

                                <div class="row">
                                    <?php foreach ($position['candidates'] as $candidate): ?>
                                        <div class="col-md-6 col-lg-4 mb-3">
                                            <div class="candidate-card" data-position="<?php echo $position['id']; ?>" 
                                                 data-candidate="<?php echo $candidate['id']; ?>">
                                                <img src="<?php echo $candidate['photo']; ?>" 
                                                     alt="<?php echo $candidate['name']; ?>" 
                                                     class="candidate-img">
                                                <h5><?php echo $candidate['name']; ?></h5>
                                                <p class="text-muted"><?php echo $position['title']; ?> Candidate</p>
                                                <input type="radio" name="position_<?php echo $position['id']; ?>" 
                                                       value="<?php echo $candidate['id']; ?>" 
                                                       style="display: none;" required>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <hr>
                        <?php endforeach; ?>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-vote">
                                <i class="fas fa-paper-plane me-2"></i>Submit Your Vote
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Handle candidate selection
            const candidateCards = document.querySelectorAll('.candidate-card');
            
            candidateCards.forEach(card => {
                card.addEventListener('click', function() {
                    const position = this.getAttribute('data-position');
                    const candidateId = this.getAttribute('data-candidate');
                    
                    // Remove selected class from all cards in this position
                    document.querySelectorAll(`.candidate-card[data-position="${position}"]`).forEach(c => {
                        c.classList.remove('selected');
                    });
                    
                    // Add selected class to clicked card
                    this.classList.add('selected');
                    
                    // Update the radio input
                    const radioInput = this.querySelector('input[type="radio"]');
                    if (radioInput) {
                        radioInput.checked = true;
                    }
                });
            });
            
            // Handle form validation
            const voteForm = document.getElementById('voteForm');
            
            voteForm.addEventListener('submit', function(e) {
                let allPositionsSelected = true;
                const positionGroups = {};
                
                candidateCards.forEach(card => {
                    const position = card.getAttribute('data-position');
                    if (!positionGroups[position]) {
                        positionGroups[position] = false;
                    }
                    
                    if (card.classList.contains('selected')) {
                        positionGroups[position] = true;
                    }
                });
                
                for (const position in positionGroups) {
                    if (!positionGroups[position]) {
                        allPositionsSelected = false;
                        break;
                    }
                }
                
                if (!allPositionsSelected) {
                    e.preventDefault();
                    alert('Please select a candidate for each position.');
                    return;
                }
            });
        });
    </script>
</body>
</html>