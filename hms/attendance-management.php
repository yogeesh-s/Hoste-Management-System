<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Check if the form is submitted for taking attendance
if (isset($_POST['take_attendance'])) {
    // Retrieve the attendance data from the form
    $hostellerIds = $_POST['hosteller_ids'];

    // Loop through the selected hosteller IDs
    foreach ($hostellerIds as $hostellerId) {
        try {
            // Create the SQL query to insert attendance for each hosteller
            $attendanceQuery = "INSERT INTO attendance (hosteller_id, attendance_date) 
                                SELECT id, CURRENT_DATE 
                                FROM hostellers 
                                WHERE id = '$hostellerId'";

            // Execute the query
            $conn->query($attendanceQuery);
        } catch (Exception $e) {
            // Redirect to the same page with an error message
            header('Location: attendance-management.php?error=1');
            exit();
        }
    }

    // Redirect to the same page after successful attendance insertion
    header('Location: attendance-management.php');
    exit();
}

// Retrieve the list of allocated hostellers
$allocatedQuery = "SELECT hostellers.id, hostellers.name, rooms.room_number
                   FROM hostellers
                   INNER JOIN room_allotment ON hostellers.id = room_allotment.hostellers_id
                   INNER JOIN rooms ON room_allotment.room_id = rooms.id
                   WHERE room_allotment.deallocation_date IS NULL";
$allocatedResult = $conn->query($allocatedQuery);

// Retrieve the monthly attendance data for each hosteller
$attendanceDataQuery = "SELECT hostellers.id, hostellers.name, COUNT(attendance.id) AS total_attendance
                        FROM hostellers
                        LEFT JOIN attendance ON hostellers.id = attendance.hosteller_id
                        WHERE MONTH(attendance.attendance_date) = MONTH(CURRENT_DATE)
                        GROUP BY hostellers.id";
$attendanceDataResult = $conn->query($attendanceDataQuery);
$attendanceData = array();

while ($row = $attendanceDataResult->fetch_assoc()) {
    $attendanceData[$row['id']] = $row['total_attendance'];
}

// Retrieve the hosteller data
$hostellersQuery = "SELECT id, name FROM hostellers WHERE id IN (SELECT hostellers_id FROM room_allotment WHERE deallocation_date IS NULL)";
$hostellersResult = $conn->query($hostellersQuery);
$hostellers = array();

while ($row = $hostellersResult->fetch_assoc()) {
    $hostellers[$row['id']] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Attendance Management</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
    <!-- Add CSS for the modal -->
    <style>
        .modal-attendance {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-attendance .modal-content {
            width: 80%;
            max-width: 600px;
        }

        .modal-attendance .modal-body {
            height: 400px;
            overflow-y: auto;
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
                            <a href="attendance-management.php" class="nav-link  active">
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
                    <div class="row">
                        <div class="col-12 mt-3">
                            <!-- Attendance Form -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Take Attendance</h3>
                                </div>
                                <div class="card-body">
                                    <form id="attendance-form" method="POST">
                                        <div class="form-group">
                                            <label>Select Hostellers:</label>
                                            <select class="form-control" name="hosteller_ids[]" multiple required>
                                                <?php
                                                // Loop through the hostellers and display them in the dropdown
                                                foreach ($hostellers as $hostellerId => $hosteller) {
                                                    echo '<option value="' . $hostellerId . '">' . $hosteller['name'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="take_attendance">Take Attendance</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <!-- Attendance Table -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Attendance for Current Month</h3>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Hosteller Name</th>
                                                <th>Total Attendance</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Loop through the attendance data and display it in the table
                                            foreach ($attendanceData as $hostellerId => $totalAttendance) {
                                                $hostellerName = isset($hostellers[$hostellerId]['name']) ? $hostellers[$hostellerId]['name'] : '';

                                                echo '<tr>';
                                                echo '<td>' . $hostellerId . '</td>';
                                                echo '<td>' . $hostellerName . '</td>';
                                                echo '<td>' . $totalAttendance . '</td>';
                                                echo '<td><button type="button" class="btn btn-primary view-attendance-btn" data-hosteller-id="' . $hostellerId . '">View Attendance</button></td>';
                                                echo '</tr>';
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Modal for Attendance -->
        <div class="modal fade" id="attendance-modal" tabindex="-1" aria-labelledby="attendance-modal-label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-attendance">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="attendance-modal-label">Attendance for Current Month</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Sl No</th><th>Date</th>
                                </tr>
                            </thead>
                            <tbody id="attendance-modal-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
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

        <!-- Add JavaScript for the modal and attendance -->
        <script>
            // Add JavaScript for the modal and attendance
            $(document).on('click', '.view-attendance-btn', function() {
                var hostellerId = $(this).data('hosteller-id');

                // Perform an AJAX request to fetch the attendance data for the hosteller
                $.ajax({
                    url: 'get-hosteller-attendance.php',
                    type: 'POST',
                    data: { hostellerId: hostellerId },
                    dataType: 'json',
                    success: function(data) {
                        // Clear the modal body
                        $('#attendance-modal-body').empty();

                        // Loop through the attendance data and append it to the modal body
                        var serialNumber = 1;
                        for (var day in data) {
                            if (data.hasOwnProperty(day)) {
                                var attendanceCount = data[day];
                                var formattedDate = moment().date(day).format('YYYY-MM-DD');
                                var row = '<tr><td>' + serialNumber + '</td><td>' + formattedDate + '</td></tr>';
                                $('#attendance-modal-body').append(row);
                                serialNumber++;
                            }
                        }

                        // Show the modal
                        $('#attendance-modal').modal('show');
                    },
                    error: function() {
                        alert('Error occurred while fetching attendance data.');
                    }
                });
            });
        </script>

    </div>
</body>
</html>
