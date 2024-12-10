<?php
session_start();

// Check if user is already logged in
if (isset($_SESSION['email'])) {
    header('Location: dashboard.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Email OTP Verification</title>
    <!-- Include Twitter Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5">
                <!-- Email Verification Card -->
                <div class="card shadow-sm">
                    <div class="card-header text-center bg-primary text-white">
                        <h4>Email Verification</h4>
                    </div>
                    <div class="card-body">
                        <p class="text-muted text-center">
                            Please enter your email to receive a one-time password (OTP).
                        </p>
                        <!-- Form to send OTP -->
                        <form method="POST" action="send_otp.php">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Send OTP</button>
                            </div>
                        </form>
                        <!-- Display success or error message -->
                        <?php if (isset($_SESSION['message'])): ?>
                            <div class="alert alert-info mt-3 text-center" role="alert">
                                <?php 
                                    echo $_SESSION['message']; 
                                    unset($_SESSION['message']);
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- End of Email Verification Card -->
            </div>
        </div>
    </div>

    <!-- Include Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
