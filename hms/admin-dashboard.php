<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Fetch the numbers from the database
$registeredHostellersCount = 0;
$allocatedHostellersCount = 0;
$nonAllocatedHostellersCount = 0;
$roomsCount = 0;
$totalCapacity = 0;
$remainingCapacity = 0;

// Fetch the count of registered hostellers
$registeredHostellersQuery = "SELECT COUNT(*) AS registered_hostellers_count FROM hostellers";
$registeredHostellersResult = $conn->query($registeredHostellersQuery);
if ($registeredHostellersResult && $registeredHostellersResult->num_rows > 0) {
    $registeredHostellersRow = $registeredHostellersResult->fetch_assoc();
    $registeredHostellersCount = $registeredHostellersRow['registered_hostellers_count'];
}

// Fetch the count of allocated hostellers
$allocatedHostellersQuery = "SELECT COUNT(*) AS allocated_hostellers_count FROM room_allotment WHERE deallocation_date IS NULL";
$allocatedHostellersResult = $conn->query($allocatedHostellersQuery);
if ($allocatedHostellersResult && $allocatedHostellersResult->num_rows > 0) {
    $allocatedHostellersRow = $allocatedHostellersResult->fetch_assoc();
    $allocatedHostellersCount = $allocatedHostellersRow['allocated_hostellers_count'];
}

// Fetch the count of non-allocated hostellers
$nonAllocatedHostellersCount = $registeredHostellersCount - $allocatedHostellersCount;

// Fetch the count of rooms
$roomsQuery = "SELECT COUNT(*) AS rooms_count FROM rooms";
$roomsResult = $conn->query($roomsQuery);
if ($roomsResult && $roomsResult->num_rows > 0) {
    $roomsRow = $roomsResult->fetch_assoc();
    $roomsCount = $roomsRow['rooms_count'];
}

// Fetch the total capacity of all rooms
$totalCapacityQuery = "SELECT SUM(room_capacity) AS total_capacity FROM rooms";
$totalCapacityResult = $conn->query($totalCapacityQuery);
if ($totalCapacityResult && $totalCapacityResult->num_rows > 0) {
    $totalCapacityRow = $totalCapacityResult->fetch_assoc();
    $totalCapacity = $totalCapacityRow['total_capacity'];
}

// Calculate the remaining capacity (available seats)
$remainingCapacity = $totalCapacity - $allocatedHostellersCount;

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/custom.css">    
</head>
<body class="hold-transition sidebar-mini">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" href="#" role="button">Hostel Management System</a>
                </li>
            </ul>
            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="admin-logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </li>
            </ul>
        </nav>

        <!-- Main Sidebar -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="admin-dashboard.php" class="brand-link">
                <span class="brand-text font-weight-light">Admin Dashboard</span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <!-- Dashboard -->
                        <li class="nav-item">
                            <a href="admin-dashboard.php" class="nav-link active">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>
                        <!-- Room Management -->
                        <li class="nav-item">
                            <a href="room-management.php" class="nav-link">
                                <i class="nav-icon fas fa-bed"></i>
                                <p>Room Management</p>
                            </a>
                        </li>
                        <!-- Room Allotment -->
                        <li class="nav-item">
                            <a href="room-allotment.php" class="nav-link">
                                <i class="nav-icon fas fa-home"></i>
                                <p>Room Allotment</p>
                            </a>
                        </li>
                        <!-- Hosteller Management -->
                        <li class="nav-item">
                            <a href="hosteller-management.php" class="nav-link">
                                <i class="nav-icon fas fa-users"></i>
                                <p>Hosteller Management</p>
                            </a>
                        </li>
                        <!-- Attendance Management -->
                        <li class="nav-item">
                            <a href="attendance-management.php" class="nav-link">
                                <i class="nav-icon fas fa-clipboard-check"></i>
                                <p>Attendance Management</p>
                            </a>
                        </li>
                        <!-- Mess/Food Management -->
                        <li class="nav-item">
                            <a href="mess-food-management.php" class="nav-link">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>Mess/Food Management</p>
                            </a>
                        </li>
                        <!-- Payment Details -->
                        <li class="nav-item">
                            <a href="payment-details.php" class="nav-link">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>Payment Details</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Content Wrapper -->
        <div class="content-wrapper">
            <section class="content">
                <div class="container-fluid">
                    <div class="row pt-3">
                        <div class="col-lg-3 col-6">
                            <!-- Number of Registered Hostellers -->
                            <div class="small-box bg-info">
                                <div class="inner">
                                    <h3><?php echo $registeredHostellersCount; ?></h3>
                                    <p>Number of Registered Hostellers</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Number of Allocated Hostellers -->
                            <div class="small-box bg-primary">
                                <div class="inner">
                                    <h3><?php echo $allocatedHostellersCount; ?></h3>
                                    <p>Number of Allocated Hostellers</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-check"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Number of Non-Allocated Hostellers -->
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3><?php echo $nonAllocatedHostellersCount; ?></h3>
                                    <p>Number of Non-Allocated Hostellers</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-user-times"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Number of Rooms -->
                            <div class="small-box bg-success">
                                <div class="inner">
                                    <h3><?php echo $roomsCount; ?></h3>
                                    <p>Number of Rooms</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-home"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-6">
                            <!-- Number of Available Seats -->
                            <div class="small-box bg-danger">
                                <div class="inner">
                                    <h3><?php echo $remainingCapacity; ?></h3>
                                    <p>Number of Available Beds</p>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-bed"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Main Footer -->
        <?php
            // Include the PHP page
            include 'footer.php';
        ?>

        <!-- jQuery -->
        <script src="assets/jquery-3.6.0.min.js"></script>
        <!-- Moment JS -->
        <script src="assets/moment.min.js"></script>
        <!-- Bootstrap JS -->
        <script src="assets/bootstrap-4.5.3-dist/js/bootstrap.min.js"></script>
        <!-- AdminLTE JS -->
        <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
        <!-- Custom JS -->
        <script src="assets/custom.js"></script>

    </div>
</body>
</html>
