<?php
// Start the session
session_start();

// Include the database connection file
require_once 'connection.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // Validate the credentials
    //$hashedPassword = md5($password);
    $sql = "SELECT id FROM hostellers WHERE phone = '$phone' AND password = '$password'";
    $result = $conn->query($sql);

    if ($result->num_rows === 1) {
        // Login success, set the session variable
        $row = $result->fetch_assoc();
        $_SESSION['hosteller_id'] = $row['id'];
        
        // Redirect to the dashboard or desired page
        header('Location: dashboard.php');
        exit();
    } else {
        // Login failed, handle it accordingly
        $loginError = 'Invalid mobile number or password.';
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
    <title>Login</title>
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
            border-radius: 10px;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            text-align: center;
            padding: 20px;
            border-radius: 10px 10px 0 0;
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
            <div class="card-header">
                <h2><i class="fas fa-user-lock"></i>Hosteller Login</h2>
            </div>
            <div class="card-body">
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                    <?php if (isset($loginError)) : ?>
                        <div class="alert alert-danger"><?php echo $loginError; ?></div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="phone"><i class="fas fa-phone"></i> Mobile Number:</label>
                        <input type="text" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Login</button>
                </form>
                <div class="mt-3">
                    <p>Don't have an account? <a href="register.php">Register here</a></p>
                    <p><a href="admin-login.php">Admin Login</a></p>
                </div>
            </div>
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
