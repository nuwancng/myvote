<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email OTP Verification</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h3>Email Verification</h3>
            <form method="POST" action="send_otp.php" class="mb-3">
                <div class="mb-3">
                    <label for="email" class="form-label">Enter your email:</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Send OTP</button>
            </form>
            <p class="text-success"><?php echo isset($_SESSION['message']) ? $_SESSION['message'] : ''; ?></p>
        </div>
    </div>
</div>
</body>
</html>
