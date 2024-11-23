<?php
require_once '../authentication/class.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User</title>
    <link rel="stylesheet" href="../../src/css/bootstrap.css">
    <link rel="stylesheet" href="../../src/css/user.css">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>

<body>
    <div class="wrapper">
        <?php include '../../includes/sidebar-user.php' ?>

        <div class="main-content">
            <h1>Welcome User</h1>
            <p>Select an option from the sidebar to get started.</p>
        </div>
    </div>

    <footer class="text-center py-3">
        <p>&copy; 2024 Inventory Management System</p>
    </footer>

    <script src="../../src/js/script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
</body>

</html>
