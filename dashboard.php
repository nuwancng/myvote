<?php
session_start();
require_once('db_connection.php');

// Check if session exists
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$mysqli = db_connect();
$stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

$user = $result->fetch_assoc();

// Fetch the user's current vote
$stmt = $mysqli->prepare('SELECT response FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$vote_result = $stmt->get_result();
$current_vote = $vote_result->num_rows > 0 ? $vote_result->fetch_assoc()['response'] : "No vote yet";

// Fetch response counts and percentages
$total_votes_query = $mysqli->query('SELECT COUNT(*) as total FROM votes');
$total_votes = $total_votes_query->fetch_assoc()['total'];

$response_stats_query = $mysqli->query('SELECT response, COUNT(*) as count FROM votes GROUP BY response');
$response_stats = [];
while ($row = $response_stats_query->fetch_assoc()) {
    $response_stats[] = [
        'response' => $row['response'],
        'count' => $row['count'],
        'percentage' => $total_votes > 0 ? round(($row['count'] / $total_votes) * 100, 2) : 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>User Dashboard</title>
    <!-- Include Twitter Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Main container for the dashboard -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Dashboard Card -->
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>User Dashboard</h4>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <h5>Welcome, <?php echo htmlspecialchars($user['email']); ?>!</h5>
                            <p class="text-muted">Here's your information:</p>
                        </div>
                        <ul class="list-group">
                            <li class="list-group-item">
                                <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?>
                            </li>
                            <li class="list-group-item">
                                <strong>Last Logged In:</strong> 
                                <?php echo $user['last_logged_in'] ? htmlspecialchars($user['last_logged_in']) : "Never"; ?>
                            </li>
                        </ul>

                        <div class="mt-4">
                            <h5>My Vote</h5>
                            <ul class="list-group mb-3">
                                <li class="list-group-item">
                                    <strong>Status:</strong> <?php echo htmlspecialchars($current_vote); ?>
                                    <a href="vote.php" class="btn btn-sm btn-secondary float-end">Change My Vote</a>
                                </li>
                            </ul>

                            <h5>Vote Summary</h5>
                            <ul class="list-group">
                                <?php foreach ($response_stats as $stat) : ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($stat['response']); ?>
                                        <span>
                                            <?php echo $stat['count']; ?> votes 
                                            (<?php echo $stat['percentage']; ?>%)
                                        </span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                        <div class="mt-4 text-center">
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>
                    </div>
                </div>
                <!-- End of Dashboard Card -->
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
