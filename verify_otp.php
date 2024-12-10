<?php
session_start();
require_once('db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input_otp = $_POST['otp'];

    if (!isset($_SESSION['otp'])) {
        $_SESSION['message'] = "Session expired. Please try again.";
        header('Location: index.php');
        exit();
    }

    if ($input_otp == $_SESSION['otp']) {
        $mysqli = db_connect();

        $email = $_SESSION['email'];
        $stmt = $mysqli->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            // Insert new user if not found
            $stmt = $mysqli->prepare('INSERT INTO users (email, first_name, last_name, last_logged_in) VALUES (?, "", "", NOW())');
            $stmt->bind_param('s', $email);
            $stmt->execute();
        } else {
            // Update last logged in timestamp
            $stmt = $mysqli->prepare('UPDATE users SET last_logged_in = NOW() WHERE email = ?');
            $stmt->bind_param('s', $email);
            $stmt->execute();
        }

        // Redirect user to dashboard
        $_SESSION['email'] = $email;
        header('Location: dashboard.php');
        exit();
    } else {
        $_SESSION['message'] = "Invalid OTP, please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Verify OTP</title>
    <!-- Include Twitter Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Main Content -->
    <div class="container d-flex align-items-center justify-content-center min-vh-100">
        <div class="card p-4 shadow" style="width: 100%; max-width: 400px;">
            <h3 class="text-center mb-4">OTP Verification</h3>

            <!-- Display Messages -->
            <?php if (isset($_SESSION['message'])) { ?>
                <div class="alert alert-danger" role="alert">
                    <?php echo $_SESSION['message'];
                    unset($_SESSION['message']); ?>
                </div>
            <?php } ?>

            <!-- OTP Form -->
            <form method="POST">
                <div class="mb-3">
                    <label for="otp" class="form-label">Enter Your OTP</label>
                    <input type="text" class="form-control" id="otp" name="otp" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Verify OTP</button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted">Didn't receive an OTP? <a href="index.php">Try again</a>.</p>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
