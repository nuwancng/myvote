<?php
session_start();
require_once('db_connection.php');

// Redirect if the user is not logged in
if (!isset($_SESSION['email'])) {
    header('Location: index.php');
    exit();
}

$mysqli = db_connect();

// Fetch user details
$stmt = $mysqli->prepare('SELECT id FROM users WHERE email = ?');
$stmt->bind_param('s', $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user already voted
$stmt = $mysqli->prepare('SELECT response FROM votes WHERE user_id = ?');
$stmt->bind_param('i', $user['id']);
$stmt->execute();
$vote_result = $stmt->get_result();
$current_response = '';

if ($vote_result->num_rows > 0) {
    $row = $vote_result->fetch_assoc();
    $current_response = $row['response'];
}

// Define dropdown options
$options = [
    'Option A',
    'Option B',
    'Option C',
    'Option D'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Vote Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Cast Your Vote</h1>
        <div class="card mt-4">
            <div class="card-body">
                <?php if (isset($_SESSION['message'])) { ?>
                    <div class="alert alert-success">
                        <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                    </div>
                <?php } ?>
                <form action="submit_vote.php" method="POST">
                    <div class="mb-3">
                        <label for="response" class="form-label">Select Your Response</label>
                        <select class="form-select" id="response" name="response" required>
                            <option value="">-- Select an Option --</option>
                            <?php foreach ($options as $option) { ?>
                                <option value="<?php echo htmlspecialchars($option); ?>" 
                                    <?php echo ($option === $current_response) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($option); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Submit Vote</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
