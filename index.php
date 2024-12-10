<?php
session_start();
require_once('db_connection.php');

// Check if the user is already logged in
if (isset($_SESSION['email'])) {
    header('Location: dashboard.php');
    exit();
}

$mysqli = db_connect();

// Fetch vote counts and total votes
$stmt = $mysqli->prepare('SELECT response, COUNT(*) AS count FROM votes GROUP BY response');
$stmt->execute();
$vote_counts_result = $stmt->get_result();

$total_votes = 0;
$vote_counts = [];
while ($row = $vote_counts_result->fetch_assoc()) {
    $vote_counts[$row['response']] = $row['count'];
    $total_votes += $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- Email Verification Form -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Email Verification</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="send_otp.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Enter your email:</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                        </form>
                        <!-- Message Display -->
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-info mt-3">
                                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Vote Statistics -->
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-secondary text-white">
                        <h4>Vote Statistics</h4>
                    </div>
                    <div class="card-body">
                        <ul class="list-group">
                            <?php foreach ($vote_counts as $response => $count): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo htmlspecialchars($response); ?>
                                    <span class="badge bg-primary">
                                        <?php 
                                            $percentage = $total_votes > 0 ? round(($count / $total_votes) * 100, 2) : 0;
                                            echo "{$count} votes ({$percentage}%)"; 
                                        ?>
                                    </span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <strong>Total Votes</strong>
                                <span class="badge bg-success"><?php echo $total_votes; ?></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
