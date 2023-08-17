<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Check if the form is submitted for updating the mess food menu
if (isset($_POST['update_menu'])) {
    // Retrieve the menu items from the form
    $menuItems = $_POST['menu_items'];

    // Prepare the update query
    $updateMenuQuery = "INSERT INTO mess_food_menu (menu_date, price) VALUES (?, ?)
                        ON DUPLICATE KEY UPDATE price = ?";
    $updateMenuStmt = $conn->prepare($updateMenuQuery);

    // Loop through the menu items and execute the prepared statement
    foreach ($menuItems as $date => $price) {
        $updateMenuStmt->bind_param("sdd", $date, $price, $price);
        $updateMenuStmt->execute();
    }

    // Redirect to the same page after updating the menu
    header('Location: mess-food-management.php');
    exit();
}

// Calculate the monthly cost
$monthlyCostQuery = "SELECT SUM(price) AS total_cost
                    FROM mess_food_menu
                    WHERE MONTH(menu_date) = MONTH(CURRENT_DATE)";
$monthlyCostResult = $conn->query($monthlyCostQuery);
$monthlyCostRow = $monthlyCostResult->fetch_assoc();
$monthlyCost = $monthlyCostRow['total_cost'];

// Retrieve the mess food menu for the current month
$menuQuery = "SELECT menu_date, price
              FROM mess_food_menu
              WHERE MONTH(menu_date) = MONTH(CURRENT_DATE)
              ORDER BY menu_date";
$menuResult = $conn->query($menuQuery);
$menuItems = array();

while ($row = $menuResult->fetch_assoc()) {
    $menuItems[$row['menu_date']] = $row['price'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mess/Food Management</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
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
                            <a href="admin-dashboard.php" class="nav-link">
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
                            <a href="mess-food-management.php" class="nav-link  active">
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
                    <div class="row">
                        <div class="col-12 mt-3">
                            <!-- Mess Food Menu Form -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Mess Food Cost</h3>
                                </div>
                                <div class="card-body">
                                    <form id="menu-form" method="POST">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Month</th>
                                                    <th>Price</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                
                                                <?php
                                                    // Generate the form field for the first day of the current month
                                                    $currentMonth = date('m');
                                                    $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, date('Y'));
                                                    $firstDayDate = date('Y-m') . '-01'; // First day of the current month
                                                    $price = isset($menuItems[$firstDayDate]) ? $menuItems[$firstDayDate] : '';

                                                    echo '<tr>
                                                        <td>' . date('F', strtotime($firstDayDate)) . '</td>
                                                        <td><input type="number" name="menu_items[' . $firstDayDate . ']" value="' . $price . '" min="0" step="0.01" class="form-control"></td>
                                                    </tr>';
                                                ?>
                                            </tbody>
                                        </table>
                                        <button type="submit" class="btn btn-primary" name="update_menu">Update Cost</button>
                                    </form>
                                </div>
                            </div>

                            <!-- Monthly Cost -->
                            <div class="card">
                                <div class="card-body">
                                    <h5>Monthly Food Cost: <b> ₹ </b><?php echo $monthlyCost; ?></h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

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
    </div>
    <script>
        function printSection(sectionId) {
            var printContents = document.getElementById(sectionId).innerHTML;
            var originalContents = document.body.innerHTML;
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
        }
    </script>
</body>
</html>
