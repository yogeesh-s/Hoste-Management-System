<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Check if the form is submitted for allotting a room
if (isset($_POST['allot_room'])) {
    // Retrieve the hosteller ID and room ID from the form
    $hostellerId = $_POST['hosteller_id'];
    $roomId = $_POST['room_id'];

    // Create the SQL query to check the room capacity
    $roomCapacityQuery = "SELECT room_capacity FROM rooms WHERE id = '$roomId'";
    $roomCapacityResult = $conn->query($roomCapacityQuery);

    // Check if the room capacity query executed successfully
    if ($roomCapacityResult) {
        $roomCapacityRow = $roomCapacityResult->fetch_assoc();
        $roomCapacity = $roomCapacityRow['room_capacity'];

        // Create the SQL query to count the number of allotted rooms for the given room ID
        $allottedRoomsQuery = "SELECT COUNT(*) as count FROM room_allotment WHERE room_id = '$roomId' AND deallocation_date IS NULL";
        $allottedRoomsResult = $conn->query($allottedRoomsQuery);

        // Check if the allotted rooms query executed successfully
        if ($allottedRoomsResult) {
            $allottedRoomsRow = $allottedRoomsResult->fetch_assoc();
            $allottedRoomsCount = $allottedRoomsRow['count'];

            // Compare the allotted rooms count with the room capacity
            if ($allottedRoomsCount < $roomCapacity) {
                // Create the SQL query to allot the room
                $allotmentQuery = "INSERT INTO room_allotment (hostellers_id, room_id, allocation_date) VALUES ('$hostellerId', '$roomId', CURRENT_DATE)";

                // Execute the query
                $conn->query($allotmentQuery);
                header('Location: room-allotment.php');
                exit();
            } else {
                // Room capacity has been reached, display an error message
                echo "<script>alert('Room capacity has been reached. Room allotment failed.');</script>";
            }
        } else {
            // Error occurred while counting allotted rooms, display an error message
            echo "<script>alert('An error occurred while counting allotted rooms. Room allotment failed.');</script>";
        }
    } else {
        // Error occurred while retrieving room capacity, display an error message
        echo "<script>alert('An error occurred while retrieving room capacity. Room allotment failed.');</script>";
    }
}

// Check if the form is submitted for deallocating a room
if (isset($_POST['deallocate_room'])) {
    // Retrieve the allotment ID from the form
    $allotmentId = $_POST['allotment_id'];

    // Create the SQL query to check if the room is already deallocated
    $checkDeallocationQuery = "SELECT * FROM room_allotment WHERE id = '$allotmentId' AND deallocation_date IS NOT NULL";
    $checkDeallocationResult = $conn->query($checkDeallocationQuery);

    // Check if the query executed successfully
    if ($checkDeallocationResult && $checkDeallocationResult->num_rows > 0) {
        // Room is already deallocated, display a message
        echo "<script>alert('Room is already deallocated.');</script>";
    } else {
        // Create the SQL query to deallocate the room
        $deallocationQuery = "UPDATE room_allotment SET deallocation_date = CURRENT_DATE WHERE id = '$allotmentId'";

        // Execute the query
        if ($conn->query($deallocationQuery)) {
            // Deallocation successful
            echo "<script>alert('Room deallocated successfully.');</script>";
            header('Location: room-allotment.php');
            exit();
        } else {
            // Error occurred while deallocating the room
            echo "<script>alert('An error occurred while deallocating the room.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Room Allotment</title>
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
                            <a href="room-allotment.php" class="nav-link  active">
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
                    <div class="row">
                        <div class="col-12 mt-3">
                            <!-- Room Allotment Form -->
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Allot Room</h3>
                                </div>
                                <div class="card-body">
                                    <form id="allot-room-form" method="POST">
                                        <div class="form-group">
                                            <label for="hosteller_id">Hosteller:</label>
                                            <select class="form-control" id="hosteller_id" name="hosteller_id" required>
                                                <?php
                                                    $hostellersQuery = "SELECT * FROM hostellers WHERE id NOT IN (SELECT hostellers_id FROM room_allotment WHERE deallocation_date IS NULL)";
                                                    $hostellersResult = $conn->query($hostellersQuery);
                                                    while ($hosteller = $hostellersResult->fetch_assoc()) {
                                                        echo '<option value="' . $hosteller['id'] . '">' . $hosteller['name'] . '</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="room_id">Room:</label>
                                            <select class="form-control" id="room_id" name="room_id" required>
                                                <?php
                                                $roomsQuery = "SELECT rooms.id, rooms.room_number, rooms.room_capacity
                                                FROM rooms
                                                LEFT JOIN room_allotment ON rooms.id = room_allotment.room_id AND room_allotment.deallocation_date IS NULL
                                                GROUP BY rooms.id
                                                HAVING COUNT(room_allotment.id) < rooms.room_capacity";
                                                $roomsResult = $conn->query($roomsQuery);
                                                while ($room = $roomsResult->fetch_assoc()) {
                                                    echo '<option value="' . $room['id'] . '">' . $room['room_number'] . '</option>';
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="allot_room">Allot Room</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                    <table id="allotted-hostellers-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hosteller Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Room Number</th>
                                <th>Allotment Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Retrieve the allotted hostellers
                            $allottedQuery = "SELECT room_allotment.id, hostellers.name AS hosteller_name, hostellers.email, hostellers.phone, rooms.room_number, room_allotment.allocation_date
                            FROM room_allotment
                            INNER JOIN hostellers ON room_allotment.hostellers_id = hostellers.id
                            INNER JOIN rooms ON room_allotment.room_id = rooms.id
                            WHERE room_allotment.deallocation_date IS NULL";
                            $allottedResult = $conn->query($allottedQuery);

                            while ($allotted = $allottedResult->fetch_assoc()) {
                                echo '<tr>
                                    <td>' . $allotted['id'] . '</td>
                                    <td>' . $allotted['hosteller_name'] . '</td>
                                    <td>' . $allotted['email'] . '</td>
                                    <td>' . $allotted['phone'] . '</td>
                                    <td>' . $allotted['room_number'] . '</td>
                                    <td>' . $allotted['allocation_date'] . '</td>
                                    <td>';

                                // Deallocate button
                                echo '<a href="#" class="btn btn-danger btn-sm deallocate-hosteller" data-toggle="modal" data-target="#deallocateModal" data-allotment-id="' . $allotted['id'] . '"><i class="fas fa-times"></i> Deallocate</a>';

                                echo '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <table id="room-details-table" class="table table-bordered table-striped ">
                        <thead>
                            <tr>
                                <th>Room Number</th>
                                <th>Allocated Hosteller(s)</th>
                                <th>Remaining Capacity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $roomDetailsQuery = "SELECT rooms.room_number, GROUP_CONCAT(hostellers.name SEPARATOR ', ') AS allocated_hostellers, rooms.room_capacity, COUNT(room_allotment.id) AS allocated_rooms
                            FROM rooms
                            LEFT JOIN room_allotment ON rooms.id = room_allotment.room_id AND room_allotment.deallocation_date IS NULL
                            LEFT JOIN hostellers ON room_allotment.hostellers_id = hostellers.id
                            GROUP BY rooms.id";
                            $roomDetailsResult = $conn->query($roomDetailsQuery);
                            while ($roomDetails = $roomDetailsResult->fetch_assoc()) {
                                $remainingCapacity = $roomDetails['room_capacity'] - $roomDetails['allocated_rooms'];
                                echo '<tr>
                                    <td>' . $roomDetails['room_number'] . '</td>
                                    <td>' . $roomDetails['allocated_hostellers'] . '</td>
                                    <td>' . $remainingCapacity . '</td>
                                </tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Deallocation Modal -->
        <div class="modal fade" id="deallocateModal" tabindex="-1" role="dialog" aria-labelledby="deallocateModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deallocateModalLabel">Deallocate Room</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to deallocate this room?</p>
                    </div>
                    <div class="modal-footer">
                        <form id="deallocate-room-form" method="POST">
                            <input type="hidden" id="allotment_id" name="allotment_id">
                            <button type="submit" class="btn btn-danger" name="deallocate_room">Deallocate</button>
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        </form>
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
        <!-- Custom JS -->
        <script src="assets/custom.js"></script>
        <script>
            // Handle click on deallocate button
            $(document).on('click', '.deallocate-hosteller', function() {
                var allotmentId = $(this).data('allotment-id');
                $('#allotment_id').val(allotmentId);
            });
        </script>
    </div>
</body>
</html>
