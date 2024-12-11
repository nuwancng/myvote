<?php
session_start();
require_once('db_connection.php');

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

// Database connection
$mysqli = db_connect();

// Get the user details
$stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Get the current user's vote
$stmt = $mysqli->prepare('SELECT response FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$result = $stmt->get_result();
$current_vote = $result->fetch_assoc();
$current_response = $current_vote['response'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vote Page</title>
    <!-- Include Twitter Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Main container -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <!-- Voting Card -->
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-success text-white">
                        <h4>Vote Now</h4>
                    </div>
                    <div class="card-body">
                        <!-- Welcome Message -->
                        <div class="mb-3">
                            <h5>Welcome, <?php echo htmlspecialchars($user['email']); ?>!</h5>
                            <p class="text-muted">Please select your response below:</p>
                        </div>

                        <!-- Voting Form -->
                        <form action="submit_vote.php" method="POST">
                            <div class="mb-3">
                                <label for="response" class="form-label">Your Response</label>
                                <select class="form-select" id="response" name="response" required>
                                    <option value="" disabled>Select your option</option>
                                    <option value="Democratic Party (DNC)" <?php echo $current_response === 'Yes' ? 'selected' : ''; ?>>Democratic Party (DNC)</option>
                                    <option value="Republican Party (RNC)" <?php echo $current_response === 'No' ? 'selected' : ''; ?>>Republican Party (RNC)</option>
                                </select>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Submit Vote</button>
                            </div>
                        </form>

                        <!-- Logout Button -->
                        <!--<div class="mt-4 text-center">
                            <a href="logout.php" class="btn btn-danger">Logout</a>
                        </div>-->
                    </div>
                </div>
                <!-- End of Voting Card -->
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
