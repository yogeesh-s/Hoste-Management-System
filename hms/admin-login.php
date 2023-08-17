<?php
// Start the session
session_start();

// Include the database connection file
require_once 'connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validate the credentials
    $sql = "SELECT id, username FROM admin WHERE username = '$username' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        // Login success, set the session variables
        $row = $result->fetch_assoc();
        $_SESSION['admin_id'] = $row['id'];
        $_SESSION['admin_username'] = $row['username'];

        // Redirect to the admin dashboard or desired page
        header('Location: admin-dashboard.php');
        exit();
    } else {
        // Login failed, handle it accordingly
        $loginError = 'Invalid username or password.';
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
    <style>
        body {
            background-color: #f8f9fa;
        }

        .container {
            max-width: 400px;
            margin-top: 100px;
        }

        .card {
            border: none;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
        }

        .card-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #0069d9;
        }

        .mt-3 a {
            color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header text-center">
                <h2><i class="fas fa-user"></i> Admin Login</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <?php if (isset($loginError)) : ?>
                        <div class="alert alert-danger"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username:</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
            </div>
        </div>
        <div class="mt-3 text-center">
            <p>Don't have an account? <a href="login.php"><i class="fas fa-user"></i> User Login</a></p>
        </div>
    </div>
    <!-- AdminLTE JS -->
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/bootstrap-4.5.3-dist/js/bootstrap.min.js"></script>
    <!-- Font Awesome JS -->
    <script src="assets/fontawesome-free-5.15.4-web/js/all.min.js"></script>
    <!-- jQuery -->
    <script src="assets/jquery-3.6.0.min.js"></script>
    <!-- Moment JS -->
    <script src="assets/moment.min.js"></script>
</body>
</html>