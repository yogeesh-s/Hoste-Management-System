<?php
require_once 'connection.php';
session_start();

// Redirect to login if hosteller_id is empty
if (empty($_SESSION['hosteller_id'])) {
    header("Location: login.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
    <style>

        #topButton {
            display: none;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99;
            font-size: 20px;
            width: 40px;
            height: 40px;
            line-height: 40px;
            text-align: center;
            border: none;
            background-color: #007bff;
            color: #fff;
            cursor: pointer;
            border-radius: 50%;
        }

        #topButton:hover {
            background-color: #0056b3;
        }

        .allotted-room-section {
        margin-top: 20px;
        }

        .section-title {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
        }

        .allotted-room-card {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        }

        .room-number {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
        }

        .room-details {
        margin-left: 10px;
        }

        .capacity, .food-status {
        margin-bottom: 5px;
        }

        .no-details {
        margin-top: 20px;
        font-style: italic;
        }

        .total-attendance {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 5px;
        }

    </style>
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
                <?php
                // Fetch and display hosteller name and photo
                $hostellerId = $_SESSION['hosteller_id'];
                $hostellerQuery = "SELECT name, photo FROM hostellers WHERE id = '$hostellerId'";
                $hostellerResult = $conn->query($hostellerQuery);

                if ($hostellerResult->num_rows > 0) {
                    $hostellerData = $hostellerResult->fetch_assoc();
                    $hostellerName = $hostellerData['name'];
                    $hostellerPhoto = $hostellerData['photo'];

                    echo "<a class='nav-link' href='#'>";
                    echo "<img src='$hostellerPhoto' class='img-circle' alt='User Image' style='height: 24px; width: 24px; object-fit: cover;'>";
                    echo " $hostellerName";
                    echo "</a>";
                }
                ?>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            </li>
        </ul>
    </nav>


    <!-- Sidebar -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4 fixed-sidebar">
        <!-- Brand Logo -->
        <a href="#" class="brand-link">
            <span class="brand-text font-weight-light text-center">Hosteller Dashboard</span>
        </a>

        <!-- Sidebar -->
        <div class="sidebar">
            <!-- Sidebar Menu -->
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column align-items-center" data-widget="treeview" role="menu" data-accordion="false">
                    <!-- Allotted Room Details -->
                    <li class="nav-item">
                        <a href="#allotted-room" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Room Allotted Details</p>
                        </a>
                    </li>
                    <!-- Expenses -->
                    <li class="nav-item">
                        <a href="#expenses" class="nav-link">
                        &nbsp&nbsp<i class="fas fa-rupee-sign"></i>
                            <p>&nbsp&nbspExpenses</p>
                        </a>
                    </li>
                    <!-- Attendance -->
                    <li class="nav-item">
                        <a href="#attendance" class="nav-link">
                            <i class="nav-icon fas fa-bed"></i>
                            <p>Attendance</p>
                        </a>
                    </li>
                    <!-- Manage Information -->
                    <li class="nav-item">
                        <a href="#manage-hosteller" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p>Manage Information</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </div>
        <!-- /.content-header -->

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <!-- Allotted Room Section -->
                <section id="allotted-room" class="content-section">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title text-white"><i class="fas fa-tachometer-alt"></i> Allotted Details</h3>
                        </div>
                        <div class="card-body">
                        <?php
                            // Get the hosteller ID
                            $hostellerId = $_SESSION['hosteller_id'];

                            // SQL query to fetch allotted room details for the specific hosteller
                            $allottedRoomQuery = "SELECT ra.*, r.room_number, r.room_capacity, h.food
                                                    FROM room_allotment AS ra
                                                    JOIN rooms AS r ON ra.room_id = r.id
                                                    JOIN hostellers AS h ON ra.hostellers_id = h.id
                                                    WHERE ra.hostellers_id = '$hostellerId'
                                                    AND ra.deallocation_date IS NULL";
                            $allottedRoomResult = $conn->query($allottedRoomQuery);

                            if ($allottedRoomResult->num_rows > 0) {
                                echo "<div class='allotted-room-section'>";

                                while ($row = $allottedRoomResult->fetch_assoc()) {
                                    $roomNumber = $row['room_number'];
                                    $roomCapacity = $row['room_capacity'];
                                    $foodStatus = $row['food'];

                                    echo "<div class='allotted-room-card'>";
                                    echo "<div class='room-number'>Room Number: $roomNumber</div>";
                                    echo "<div class='room-details'>";
                                    echo "<div class='capacity'>Room Capacity: $roomCapacity</div>";
                                    echo "<div class='food-status'>Food: $foodStatus</div>";
                                    echo "</div>";
                                    echo "</div>";
                                }

                                echo "</div>"; // Close allotted-room-section
                            } else {
                                echo "<p class='no-details'>No allotted room details available.</p>";
                            }
                        ?>
                        </div>
                    </div>
                </section>

                <!-- Expenses Section -->
                <section id="expenses" class="content-section">
                    <div class="card">
                        <div class="card-header bg-info">
                            <h3 class="card-title text-white"><i class="fas fa-rupee-sign"></i> Expenses</h3>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            <!-- Food Expenses Card -->
                            <div class="col-md-4">
                                <div class="card bg-primary text-white">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-utensils"></i> Food Expenses</h3>
                                    </div>
                                    <div class="card-body">
                                    <?php
                                        // Get the hosteller ID
                                        $hostellerId = $_SESSION['hosteller_id'];

                                        $foodQuery = "SELECT food FROM hostellers WHERE id = '$hostellerId'";
                                        $foodResult = $conn->query($foodQuery);
                                        $foodRow = $foodResult->fetch_assoc();
                                        $requiresFood = $foodRow['food'];

                                        // Fetch the food rate from the mess_food_menu table for the current month
                                        $currentMonth = date('Y-m');
                                        $foodRateQuery = "SELECT price FROM mess_food_menu WHERE MONTH(menu_date) = MONTH(CURRENT_DATE)";
                                        $foodRateResult = $conn->query($foodRateQuery);
                                        $foodRate = 0;

                                        while ($row = $foodRateResult->fetch_assoc()) {
                                            $foodRate += $row['price'];
                                        }

                                        // Check if hosteller requires food
                                        if ($requiresFood == 'Required') {
                                            // Calculate food expenses based on attendance and leave days
                                            $currentDate = date('Y-m-d');
                                            
                                            // Get the total attendance until the current date
                                            $attendanceQuery = "SELECT COUNT(*) AS total_attendance 
                                                                FROM attendance 
                                                                WHERE hosteller_id = '$hostellerId' 
                                                                AND DATE_FORMAT(attendance_date, '%Y-%m-%d') <= '$currentDate'";
                                            $attendanceResult = $conn->query($attendanceQuery);
                                            $attendanceRow = $attendanceResult->fetch_assoc();
                                            $totalAttendance = $attendanceRow['total_attendance'];

                                            // Get the total days in the current month until the current date
                                            $firstDayOfMonth = date('Y-m-01'); // First day of the current month
                                            $currentDayDateTime = new DateTime($currentDate);
                                            $firstDayDateTime = new DateTime($firstDayOfMonth);
                                            $interval = $firstDayDateTime->diff($currentDayDateTime);
                                            $totalDaysInMonth = $interval->days + 1; // Add 1 to include the current date

                                            // Calculate leave days
                                            $leaveDays = $totalDaysInMonth - $totalAttendance;

                                            $discount = 0; // Discount amount based on leave days

                                            // Calculate discount based on leave days
                                            if ($leaveDays >= 20) {
                                                $discount = 2000;
                                            } elseif ($leaveDays >= 10) {
                                                $discount = 1000;
                                            }

                                            $foodExpensesWithDiscount = $foodRate - $discount;

                                            // Display food expenses, leave days, and total days in the month until the current date
                                            echo "<p>Food Expenses:<b> ₹ </b> $foodExpensesWithDiscount</p>";

                                            // Update the logic here
                                            // You can perform any calculations or update other variables based on the food expenses, leave days, total days in the month, and attendance information
                                        } else {
                                            echo "Food not required for this hosteller.";
                                        }
                                    ?>

                                    </div>
                                </div>
                            </div>

                            <!-- Room Rent Card -->
                            <div class="col-md-4">
                                <div class="card bg-success text-white">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-home"></i> Room Rent</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php
                                        // Calculate the room rent amount
                                        $roomRentAmount = 2500;
                                        echo "<p>Room Rent Amount:<b> ₹ </b> $roomRentAmount</p>";
                                        ?>
                                    </div>
                                </div>
                            </div>

                            <!-- Total Amount Card -->
                            <div class="col-md-4">
                                <div class="card bg-info text-white">
                                    <div class="card-header">
                                        <h3 class="card-title"><i class="fas fa-coins"></i> Total Amount</h3>
                                    </div>
                                    <div class="card-body">
                                        <?php

                                            // Calculate the total amount
                                            if ($requiresFood == 'Required') {
                                                $totalAmount = $foodExpensesWithDiscount + $roomRentAmount;
                                                echo "<p>Total Amount:<b> ₹ </b> $totalAmount</p>";
                                            } else {
                                                $totalAmount = $roomRentAmount;
                                                echo "<p>Total Amount (Room Rent Only):<b> ₹ </b> $totalAmount</p>";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                    </div>
                </section>

                <!-- Attendance Section -->
                <section id="attendance" class="content-section">
                    <div class="card">
                        <div class="card-header bg-success">
                            <h3 class="card-title text-white"><i class="fas fa-bed"></i> Attendance</h3>
                        </div>
                        <div class="card-body">
                        <?php
                            // Get the hosteller ID
                            $hostellerId = $_SESSION['hosteller_id'];

                            // Check if the hosteller has an allocated room
                            $roomAllocationQuery = "SELECT * FROM room_allotment WHERE hostellers_id = '$hostellerId' AND deallocation_date IS NULL";
                            $roomAllocationResult = $conn->query($roomAllocationQuery);

                            if ($roomAllocationResult->num_rows > 0) {
                                // SQL query to fetch attendance details for the specific hosteller in the current month
                                $currentMonth = date('Y-m');
                                $attendanceQuery = "SELECT * FROM attendance WHERE hosteller_id = '$hostellerId' AND DATE_FORMAT(attendance_date, '%Y-%m') = '$currentMonth'";
                                $attendanceResult = $conn->query($attendanceQuery);
                                $totalAttendance = mysqli_num_rows($attendanceResult);

                                // Calculate total days in the current month until the current date
                                $currentDate = date('Y-m-d');
                                $firstDayOfMonth = date('Y-m-01');
                                $totalDaysInMonth = (new DateTime($currentDate))->diff(new DateTime($firstDayOfMonth))->days + 1;

                                if ($attendanceResult->num_rows > 0) {
                                    echo "<div class='attendance-section'>";
                                    echo "<h4 class='section-title'>Attendance for Current Month</h4>";
                                    echo "<table class='table'>";
                                    echo "<thead><tr><th>Serial Number</th><th>Attendance Date</th></tr></thead>";
                                    echo "<tbody>";
                                    $serialNumber = 1;
                                    while ($row = $attendanceResult->fetch_assoc()) {
                                        $attendanceDate = $row['attendance_date'];
                                        echo "<tr>";
                                        echo "<td>$serialNumber</td>";
                                        echo "<td>$attendanceDate</td>";
                                        echo "</tr>";
                                        $serialNumber++;
                                    }
                                    echo "</tbody>";
                                    echo "</table>";
                                    echo "</div>"; // Close attendance-section
                                } else {
                                    echo "<p class='no-details'>No attendance recorded for the current month.</p>";
                                }
                                echo "<div class='total-attendance'>Total Attendance: $totalAttendance / $totalDaysInMonth</div>";
                            } else {
                                echo "<p class='no-details'>Attendance is available only for hostellers with allocated rooms.</p>";
                            }
                        ?>
                        </div>
                    </div>
                </section>

                <!-- Manage Hosteller Information -->
                <section id="manage-hosteller" class="content-section">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title text-white"><i class="fas fa-home"></i> Manage Information</h3>
                        </div>
                        <div class="card-body">
                            <!-- Content for Manage Hosteller Information Section -->
                            <section id="manage-hosteller" class="content-section">
                                <?php include 'manage_hosteller.php'; ?>
                            </section>
                        </div>
                    </div>
                </section>
                            
            </div>
        </div>
        <button id="topButton" onclick="scrollToTop()" title="Go to top">
            <i class="fas fa-arrow-up"></i>
        </button>
        <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->
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

    <script>
        // Initialize FullCalendar
        $(document).ready(function() {
            $('#attendance-calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                events: 'fetch_attendance.php'
            });
        });

        // Show/hide the top button based on scroll position
        window.onscroll = function() {
        scrollFunction();
        };

        function scrollFunction() {
        var topButton = document.getElementById("topButton");
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            topButton.style.display = "block";
        } else {
            topButton.style.display = "none";
        }
        }

        // Scroll to the top when the button is clicked
        function scrollToTop() {
        document.body.scrollTop = 0; // For Safari
        document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
        }

    </script>
</body>
</html>
