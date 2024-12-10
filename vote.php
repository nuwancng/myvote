<?php
session_start();
require_once('db_connection.php');

// Redirect if the user is not logged in
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

// Check if the user has already voted
$stmt = $mysqli->prepare('SELECT response FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$vote_result = $stmt->get_result();
$previous_vote = $vote_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vote Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">Voting System</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link active" href="#">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
          <a class="nav-link btn btn-danger text-white ms-2" href="logout.php">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<!-- Main Voting Section -->
<div class="container mt-5">
  <div class="row">
    <div class="col-md-8 offset-md-2">
      <div class="card">
        <div class="card-header bg-primary text-white text-center">
          <h4>Vote Now!</h4>
        </div>
        <div class="card-body">
          <form method="POST" action="submit_vote.php">
            <div class="mb-3">
              <label for="voteResponse" class="form-label">Select Your Option:</label>
              <select name="response" class="form-select" id="voteResponse" required>
                <option value="Option A">Option A</option>
                <option value="Option B">Option B</option>
                <option value="Option C">Option C</option>
                <option value="Option D">Option D</option>
              </select>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-success">
                <?php echo isset($previous_vote['response']) ? 'Change Your Vote' : 'Submit Your Vote'; ?>
              </button>
            </div>
          </form>
          <?php if (isset($previous_vote['response'])): ?>
            <div class="mt-3 alert alert-info text-center">
              You previously selected: <strong><?php echo htmlspecialchars($previous_vote['response']); ?></strong>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
