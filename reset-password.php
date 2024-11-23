<?php
session_start(); // Ensure sessions are started

include_once 'config/settings-config.php';
require_once 'dashboard/authentication/class.php';

$resetPass = new IMS();

// Check if token and user ID are provided
if (isset($_GET['token']) && isset($_GET['id'])) {
    $token = $_GET['token'];
    $userId = $_GET['id'];

    // Validate the token
    if ($resetPass->validateResetToken($userId, $token)) {
        // Check if the form was submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn-reset-password'])) {
            $newPassword = trim($_POST['password']);

            // Validate password (e.g., length, complexity)
            if (strlen($newPassword) < 8) {
                $_SESSION['alert'] = [
                    'type' => 'danger',
                    'message' => 'Password must be at least 8 characters long.',
                ];
            } else {
                // Hash the new password before saving
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // Reset the password
                if ($resetPass->resetPassword($userId, $hashedPassword)) {
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'message' => 'Password reset successfully. You can now log in.',
                    ];
                    header("Location: index.php"); // Redirect to login page
                    exit;
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'danger',
                        'message' => 'Failed to reset the password. Please try again.',
                    ];
                }
            }
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'danger',
            'message' => 'Invalid or expired token.',
        ];
        header("Location: forgot-password.php");
        exit;
    }
} else {
    $_SESSION['alert'] = [
        'type' => 'danger',
        'message' => 'Invalid request. Missing required parameters.',
    ];
    header("Location: forgot-password.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="card p-4" style="max-width: 500px; width: 100%;">
            <h4 class="card-title text-center mb-4">Reset Your Password</h4>

            <?php
            if (isset($_SESSION['alert'])) {
                $alert = $_SESSION['alert'];
                echo '<div class="alert alert-' . htmlspecialchars($alert['type']) . '" role="alert">';
                echo htmlspecialchars($alert['message']);
                echo '</div>';
                unset($_SESSION['alert']);
            }
            ?>

            <p class="text-center mb-4">Enter your new password below.</p>

            <form method="POST" action="reset-password.php?token=<?php echo htmlspecialchars($token); ?>&id=<?php echo htmlspecialchars($userId); ?>">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter new password" required>
                </div>
                <div class="d-grid gap-2">
                    <button type="submit" name="btn-reset-password" class="btn btn-primary">Reset Password</button>
                </div>
            </form>
            <div class="text-center mt-3">
                <a href="index.php">Back to Login</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
