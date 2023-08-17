<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    // If not logged in, redirect to the admin login page
    header('Location: admin-login.php');
    exit();
}

// Handle the form submission for adding/editing rooms
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if it's an add room request
    if (isset($_POST['add_room'])) {
        $roomNumber = $_POST['room_number'];
        $roomCapacity = $_POST['room_capacity'];

        // Insert the new room into the database
        $sql = "INSERT INTO rooms (room_number, room_capacity) VALUES ('$roomNumber', '$roomCapacity')";
        $conn->query($sql);
    }

    // Check if it's an edit room request
    if (isset($_POST['edit_room'])) {
        $roomId = $_POST['room_id'];
        $roomNumber = $_POST['room_number'];
        $roomCapacity = $_POST['room_capacity'];

        // Update the room in the database
        $sql = "UPDATE rooms SET room_number = '$roomNumber', room_capacity = '$roomCapacity' WHERE id = '$roomId'";
        $conn->query($sql);
    }

    // Check if it's a delete room request
    if (isset($_POST['delete_room'])) {
        $roomId = $_POST['room_id'];

        // Delete the room from the database
        $sql = "DELETE FROM rooms WHERE id = '$roomId'";
        $sql1 = "DELETE FROM `room_allotment` WHERE room_id = '$roomId'";
        $conn->query($sql1);
        $conn->query($sql);
    }

    // Redirect to the room management page to avoid duplicate form submissions
    header('Location: room-management.php');
    exit();
}

// Retrieve the list of rooms from the database
$sql = "SELECT * FROM rooms";
$result = $conn->query($sql);

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Room Management</title>
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
                            <a href="room-management.php" class="nav-link  active">
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
            <!-- Content Header (Page header) -->
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Room Management</h1>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <!-- Add Room Form -->
                                    <form id="add-room-form">
                                        <h4 class="mt-4">Add Room</h4>
                                        <div class="form-row">
                                            <div class="form-group col-md-4">
                                                <label for="room_number">Room Number:</label>
                                                <input type="text" class="form-control" id="room_number" name="room_number" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label for="room_capacity">Room Capacity:</label>
                                                <input type="number" class="form-control" id="room_capacity" name="room_capacity" required>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <button type="submit" class="btn btn-primary mt-4" name="add_room">Add Room</button>
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Room List -->
                                    <h4 class="mt-4">Room List</h4>
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Room Number</th>
                                                <th>Room Capacity</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $row['room_number']; ?></td>
                                                    <td><?php echo $row['room_capacity']; ?></td>
                                                    <td>
                                                        <button class="btn btn-primary btn-sm edit-room" data-toggle="modal" data-target="#editModal" data-room-id="<?php echo $row['id']; ?>" data-room-number="<?php echo $row['room_number']; ?>" data-room-capacity="<?php echo $row['room_capacity']; ?>">Edit</button>
                                                        <button class="btn btn-danger btn-sm delete-room" data-toggle="modal" data-target="#deleteModal" data-room-id="<?php echo $row['id']; ?>">Delete</button>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <!-- Edit Room Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Room</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-room-form">
                            <input type="hidden" id="edit_room_id" name="room_id">
                            <div class="form-group">
                                <label for="edit_room_number">Room Number:</label>
                                <input type="text" class="form-control" id="edit_room_number" name="room_number" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_room_capacity">Room Capacity:</label>
                                <input type="number" class="form-control" id="edit_room_capacity" name="room_capacity" required>
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_room">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Room Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Room</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this room?</p>
                        <form id="delete-room-form">
                            <input type="hidden" id="delete_room_id" name="room_id">
                            <button type="submit" class="btn btn-danger" name="delete_room">Delete</button>
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
            $(document).ready(function() {
                // Add Room Form Submission
                $('#add-room-form').submit(function(e) {
                    e.preventDefault();

                    // Get the form data
                    var roomNumber = $('#room_number').val();
                    var roomCapacity = $('#room_capacity').val();

                    // Send the AJAX request to add the room
                    $.ajax({
                        url: 'room-management.php',
                        type: 'POST',
                        data: {
                            add_room: true,
                            room_number: roomNumber,
                            room_capacity: roomCapacity
                        },
                        success: function(response) {
                            // Reload the page to display the updated room list
                            window.location.reload();
                        }
                    });
                });

                // Edit Room Modal
                $('.edit-room').click(function() {
                    var roomId = $(this).data('room-id');
                    var roomNumber = $(this).data('room-number');
                    var roomCapacity = $(this).data('room-capacity');

                    // Set the values in the edit room modal
                    $('#edit_room_id').val(roomId);
                    $('#edit_room_number').val(roomNumber);
                    $('#edit_room_capacity').val(roomCapacity);
                });

                // Edit Room Form Submission
                $('#edit-room-form').submit(function(e) {
                    e.preventDefault();

                    // Get the form data
                    var roomId = $('#edit_room_id').val();
                    var roomNumber = $('#edit_room_number').val();
                    var roomCapacity = $('#edit_room_capacity').val();

                    // Send the AJAX request to edit the room
                    $.ajax({
                        url: 'room-management.php',
                        type: 'POST',
                        data: {
                            edit_room: true,
                            room_id: roomId,
                            room_number: roomNumber,
                            room_capacity: roomCapacity
                        },
                        success: function(response) {
                            // Reload the page to display the updated room list
                            window.location.reload();
                        }
                    });
                });

                // Delete Room Modal
                $('.delete-room').click(function() {
                    var roomId = $(this).data('room-id');

                    // Set the value in the delete room modal
                    $('#delete_room_id').val(roomId);
                });

                // Delete Room Form Submission
                $('#delete-room-form').submit(function(e) {
                    e.preventDefault();

                    // Get the form data
                    var roomId = $('#delete_room_id').val();

                    // Send the AJAX request to delete the room
                    $.ajax({
                        url: 'room-management.php',
                        type: 'POST',
                        data: {
                            delete_room: true,
                            room_id: roomId
                        },
                        success: function(response) {
                            // Reload the page to display the updated room list
                            window.location.reload();
                        }
                    });
                });
            });
        </script>
    </body>
    </html>
