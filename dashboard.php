<?php
session_start();
require_once('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['loggedin'])) {
    header('Location: index.php');
    exit();
}

$mysqli = db_connect();

// Fetch user details
$stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Fetch the user's vote
$stmt = $mysqli->prepare('SELECT response FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$vote_result = $stmt->get_result();
$user_vote = $vote_result->fetch_assoc();

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
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Dashboard Card -->
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>User Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <h5 class="text-center">Welcome, <?php echo htmlspecialchars($user['email']); ?>!</h5>
                        <p class="text-muted text-center">Here's your information:</p>

                        <!-- User Info -->
                        <ul class="list-group mb-4">
                            <li class="list-group-item">
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Last Logged In:</strong> 
                                <?php echo $user['last_logged_in'] ? htmlspecialchars($user['last_logged_in']) : "Never"; ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Your Vote:</strong> 
                                <?php echo $user_vote ? htmlspecialchars($user_vote['response']) : "Not Voted Yet"; ?>
                                <a href="vote.php" class="btn btn-link">Change Vote</a>
                            </li>
                        </ul>

                        <!-- Vote Statistics -->
                        <h5 class="text-center mb-3">Vote Statistics</h5>
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

                        <!-- Logout Button -->
                        <div class="mt-4 text-center">
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
                <!-- End of Dashboard Card -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
