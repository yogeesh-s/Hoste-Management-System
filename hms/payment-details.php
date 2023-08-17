<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Calculate the monthly cost
$monthlyCostQuery = "SELECT SUM(price) AS total_cost
                    FROM mess_food_menu
                    WHERE MONTH(menu_date) = MONTH(CURRENT_DATE)";
$monthlyCostResult = $conn->query($monthlyCostQuery);
$monthlyCostRow = $monthlyCostResult->fetch_assoc();
$monthlyCost = $monthlyCostRow['total_cost'];


// Fetch hostellers' payment details for the current month, last month, and two months ago
$currentMonth = date('Y-m');
$previousMonth1 = date('Y-m', strtotime('-1 month'));
$previousMonth2 = date('Y-m', strtotime('-2 months'));

$paymentQuery = "SELECT hostellers.id, hostellers.name, hostellers.hostellercode, hostellers.food, room_allotment.deallocation_date, room_allotment.allocation_date
                FROM hostellers
                LEFT JOIN room_allotment ON hostellers.id = room_allotment.hostellers_id
                WHERE (MONTH(room_allotment.allocation_date) = MONTH(CURRENT_DATE) AND DATE_FORMAT(room_allotment.allocation_date, '%Y-%m') = ?)
                OR (MONTH(room_allotment.allocation_date) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 1 MONTH)) AND DATE_FORMAT(room_allotment.allocation_date, '%Y-%m') = ?)
                OR (MONTH(room_allotment.allocation_date) = MONTH(DATE_SUB(CURRENT_DATE, INTERVAL 2 MONTH)) AND DATE_FORMAT(room_allotment.allocation_date, '%Y-%m') = ?)
                GROUP BY hostellers.id";
$paymentStmt = $conn->prepare($paymentQuery);
$paymentStmt->bind_param("sss", $currentMonth, $previousMonth1, $previousMonth2);
$paymentStmt->execute();
$paymentResult = $paymentStmt->get_result();
$payments = array(
    'current' => array(),
    'last' => array(),
    'twoMonthsAgo' => array()
);

while ($row = $paymentResult->fetch_assoc()) {
    $hostellerId = $row['id'];
    $name = $row['name'];
    $hostellercode = $row['hostellercode'];
    $foodRequired = $row['food'];
    $deallocationDate = $row['deallocation_date'];
    $allocationDate = $row['allocation_date'];

    // Calculate leave days based on the current month
    $firstDayOfMonth = date('Y-m-01'); // First day of the current month
    $currentDate = date('Y-m-d'); // Current date
    $allocationDateTime = new DateTime($allocationDate);
    $deallocationDateTime = new DateTime($deallocationDate);
    $firstDayDateTime = new DateTime($firstDayOfMonth);
    $currentDayDateTime = new DateTime($currentDate);

    // Check if the hosteller is allocated for the current month
    if ($allocationDateTime <= $currentDayDateTime && (!$deallocationDateTime || $deallocationDateTime >= $firstDayDateTime)) {
        // Calculate the number of days between the first day and the current day of the month
        $interval = $firstDayDateTime->diff($currentDayDateTime);
        $totalDaysInMonth = $interval->days + 1; // Add 1 to include the current day

        // Calculate the attendance count for the current month
        $attendanceCountQuery = "SELECT COUNT(id) AS attendance_count
                                FROM attendance
                                WHERE hosteller_id = ? AND DATE_FORMAT(attendance_date, '%Y-%m') = ?";
        $attendanceCountStmt = $conn->prepare($attendanceCountQuery);
        $attendanceCountStmt->bind_param("is", $hostellerId, $currentMonth);
        $attendanceCountStmt->execute();
        $attendanceCountResult = $attendanceCountStmt->get_result();
        $attendanceCountRow = $attendanceCountResult->fetch_assoc();
        $attendanceCount = $attendanceCountRow['attendance_count'];

        // Deduct the attendance count from the total days in the month to get the leave days
        $leaveDays = $totalDaysInMonth - $attendanceCount;

        $foodCost = 0;
        $roomRent = 2500;

        if ($foodRequired =="Required") {
            $foodCost = $monthlyCost;

            if ($leaveDays >= 20) {
                $foodCost -= 2000;
            } elseif ($leaveDays >= 10) {
                $foodCost -= 1000;
            }
        }

        // Determine the month for which the payment belongs and add it to the respective array
        if (date('Y-m', strtotime($allocationDate)) == $currentMonth) {
            $payments['current'][] = array(
                'name' => $name,
                'hostellercode' => $hostellercode,
                'foodRequired' => $foodRequired,
                'foodCost' => $foodCost,
                'roomRent' => $roomRent,
                'attendanceCount' => $attendanceCount,
                'totalDaysInMonth' => $totalDaysInMonth
            );
        } elseif (date('Y-m', strtotime($allocationDate)) == $previousMonth1) {
            $payments['last'][] = array(
                'name' => $name,
                'hostellercode' => $hostellercode,
                'foodRequired' => $foodRequired,
                'foodCost' => $foodCost,
                'roomRent' => $roomRent,
                'attendanceCount' => $attendanceCount,
                'totalDaysInMonth' => $totalDaysInMonth
            );
        } elseif (date('Y-m', strtotime($allocationDate)) == $previousMonth2) {
            $payments['twoMonthsAgo'][] = array(
                'name' => $name,
                'hostellercode' => $hostellercode,
                'foodRequired' => $foodRequired,
                'foodCost' => $foodCost,
                'roomRent' => $roomRent,
                'attendanceCount' => $attendanceCount,
                'totalDaysInMonth' => $totalDaysInMonth
            );
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Payment Details</title>
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
                            <a href="mess-food-management.php" class="nav-link">
                                <i class="nav-icon fas fa-utensils"></i>
                                <p>Mess/Food Management</p>
                            </a>
                        </li>
                        <!-- Payment Details -->
                        <li class="nav-item">
                            <a href="payment-details.php" class="nav-link  active">
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
                            <!-- Hosteller Payments -->

                            <!-- Hosteller Payments - Current Month -->
                            <div id="print-section-current" class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Hosteller Payments - Month <?php echo date('F', strtotime('0 months')); ?></h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($payments['current'])) : ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Hosteller Code</th>
                                                        <th>Food Required</th>
                                                        <th>Attendance / Total Days</th>
                                                        <th>Food Cost</th>
                                                        <th>Room Rent</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($payments['current'] as $payment) : ?>
                                                        <tr>
                                                            <td><?= $payment['name']; ?></td>
                                                            <td><?= $payment['hostellercode']; ?></td>
                                                            <td><?= $payment['foodRequired']; ?></td>
                                                            <td><?= $payment['attendanceCount'] . ' / ' . $payment['totalDaysInMonth']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['foodCost']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['roomRent']; ?></td>
                                                            <td><b> ₹ </b><?= ($payment['foodCost'] + $payment['roomRent']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <button class="btn btn-primary" onclick="printSection('print-section-current')">Print</button>
                                        </div>
                                    <?php else : ?>
                                        <p>No payments found for the current month.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Hosteller Payments - Last Month -->
                            <div id="print-section-last" class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Hosteller Payments - Month <?php echo date('F', strtotime('-1 months')); ?></h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($payments['last'])) : ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Hosteller Code</th>
                                                        <th>Food Required</th>
                                                        <th>Attendance / Total Days</th>
                                                        <th>Food Cost</th>
                                                        <th>Room Rent</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($payments['last'] as $payment) : ?>
                                                        <tr>
                                                            <td><?= $payment['name']; ?></td>
                                                            <td><?= $payment['hostellercode']; ?></td>
                                                            <td><?= $payment['foodRequired']; ?></td>
                                                            <td><?= $payment['attendanceCount'] . ' / ' . $payment['totalDaysInMonth']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['foodCost']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['roomRent']; ?></td>
                                                            <td><b> ₹ </b><?= ($payment['foodCost'] + $payment['roomRent']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <button class="btn btn-primary" onclick="printSection('print-section-last')">Print</button>
                                        </div>
                                    <?php else : ?>
                                        <p>No payments found for the last month.</p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Hosteller Payments - Two Months Ago -->
                            <div id="print-section-two-months-ago" class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Hosteller Payments - Month <?php echo date('F', strtotime('-2 months')); ?></h3>
                                </div>
                                <div class="card-body">
                                    <?php if (!empty($payments['twoMonthsAgo'])) : ?>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>Name</th>
                                                        <th>Hosteller Code</th>
                                                        <th>Food Required</th>
                                                        <th>Attendance / Total Days</th>
                                                        <th>Food Cost</th>
                                                        <th>Room Rent</th>
                                                        <th>Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($payments['twoMonthsAgo'] as $payment) : ?>
                                                        <tr>
                                                            <td><?= $payment['name']; ?></td>
                                                            <td><?= $payment['hostellercode']; ?></td>
                                                            <td><?= $payment['foodRequired']; ?></td>
                                                            <td><?= $payment['attendanceCount'] . ' / ' . $payment['totalDaysInMonth']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['foodCost']; ?></td>
                                                            <td><b> ₹ </b><?= $payment['roomRent']; ?></td>
                                                            <td><b> ₹ </b><?= ($payment['foodCost'] + $payment['roomRent']); ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                            <button class="btn btn-primary" onclick="printSection('print-section-two-months-ago')">Print</button>
                                        </div>
                                    <?php else : ?>
                                        <p>No payments found for two months ago.</p>
                                    <?php endif; ?>
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
