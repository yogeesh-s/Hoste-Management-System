<?php
require_once 'connection.php';
session_start();

// Redirect to login if admin_id is empty
if (empty($_SESSION['admin_id'])) {
    header("Location: admin-login.php");
    exit;
}

// Function to fetch hosteller details from the database
function fetchHostellerDetails()
{
    global $conn;
    $sql = "SELECT * FROM hostellers";
    $result = $conn->query($sql);
    $hostellers = [];
    while ($row = $result->fetch_assoc()) {
        $hostellers[] = $row;
    }
    return $hostellers;
}

// Function to update hosteller details in the database
function updateHostellerDetails($hostellerId, $name, $email, $phone, $gender, $address, $dob, $hostellerType, $food)
{
    global $conn;
    // Use prepared statements to prevent SQL injection
    $updateSql = "UPDATE hostellers SET name=?, email=?, phone=?, gender=?, address=?, dob=?, hosteller_type=?, food=? WHERE id=?";
    
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("ssssssssi", $name, $email, $phone, $gender, $address, $dob, $hostellerType, $food, $hostellerId);
    $stmt->execute();
    $stmt->close();
}

// Function to delete a hosteller from the database
function deleteHosteller($hostellerId)
{
    global $conn;
    $deleteSql = "DELETE FROM hostellers WHERE id='$hostellerId'";
    $conn->query($deleteSql);
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['edit_hosteller'])) {
        $hostellerId = $_POST['hosteller_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $gender = $_POST['gender'];
        $address = $_POST['address'];
        $dob = $_POST['dob'];
        $hostellerType = $_POST['hosteller_type'];
        $food = isset($_POST['food']) ? 1 : 0;

        updateHostellerDetails($hostellerId, $name, $email, $phone, $gender, $address, $dob, $hostellerType, $food);
    } elseif (isset($_POST['delete_hosteller'])) {
        $hostellerId = $_POST['hosteller_id'];
        deleteHosteller($hostellerId);
    }
}

// Fetch hosteller details from the database
$hostellers = fetchHostellerDetails();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Hosteller Management</title>
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
                            <a href="hosteller-management.php" class="nav-link  active">
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
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Hosteller List</h3>
                                </div>
                                <!-- /.card-header -->
                                <div class="card-body">
                                <div class="table-responsive">
                                    <table id="hosteller-table" class="table table-bordered table-striped">
                                        <thead>
                                            <tr>
                                                <th>Hosteller Code</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Gender</th>
                                                <th>Address</th>
                                                <th>Date of Birth</th>
                                                <th>Hosteller Type</th>
                                                <th>Food</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($hostellers as $hosteller) : ?>
                                                <tr>
                                                    <td><?php echo $hosteller['hostellercode']; ?></td>
                                                    <td><?php echo $hosteller['name']; ?></td>
                                                    <td><?php echo $hosteller['email']; ?></td>
                                                    <td><?php echo $hosteller['phone']; ?></td>
                                                    <td><?php echo $hosteller['gender']; ?></td>
                                                    <td><?php echo $hosteller['address']; ?></td>
                                                    <td><?php echo $hosteller['dob']; ?></td>
                                                    <td><?php echo $hosteller['hosteller_type']; ?></td>
                                                    <td><?php echo $hosteller['food']?></td>
                                                    <td>
                                                        <a href="#" class="btn btn-primary btn-sm edit-hosteller" data-toggle="modal" data-target="#editModal" data-hosteller-id="<?php echo $hosteller['id']; ?>"><i class="fas fa-edit"></i> Edit</a>
                                                        <a href="#" class="btn btn-danger btn-sm delete-hosteller" data-toggle="modal" data-target="#deleteModal" data-hosteller-id="<?php echo $hosteller['id']; ?>"><i class="fas fa-trash"></i> Delete</a>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Edit Hosteller Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit Hosteller</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="edit-hosteller-form">
                            <input type="hidden" id="edit_hosteller_id" name="hosteller_id">
                            <!-- Add the required input fields based on the hosteller table structure -->
                            <div class="form-group">
                                <label for="edit_hosteller_name">Name:</label>
                                <input type="text" class="form-control" id="edit_hosteller_name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_email">Email:</label>
                                <input type="email" class="form-control" id="edit_hosteller_email" name="email" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_phone">Phone:</label>
                                <input type="text" class="form-control" id="edit_hosteller_phone" name="phone" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_gender">Gender:</label>
                                <select class="form-control" id="edit_hosteller_gender" name="gender" required>
                                    <option value="Male">Male</option>
                                    <option value="Female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_address">Address:</label>
                                <input type="text" class="form-control" id="edit_hosteller_address" name="address" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_dob">Date of Birth:</label>
                                <input type="date" class="form-control" id="edit_hosteller_dob" name="dob" required>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_type">Hosteller Type:</label>
                                <select class="form-control" id="edit_hosteller_type" name="hosteller_type" required>
                                    <option value="Student">Student</option>
                                    <option value="Employee">Employee</option>
                                    <option value="Employee">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit_hosteller_food">Food Required:</label>
                                <select class="form-control" id="edit_hosteller_food" name="food" required>
                                    <option value="Required">Required</option>
                                    <option value="Not Required">Not Required</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary" name="edit_hosteller">Save Changes</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Hosteller Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">Delete Hosteller</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this hosteller?</p>
                        <form id="delete-hosteller-form">
                            <input type="hidden" id="delete_hosteller_id" name="hosteller_id">
                            <button type="submit" class="btn btn-danger" name="delete_hosteller">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php
            // Include the PHP page
            include 'footer.php';
        ?>
    </div>

    <!-- jQuery -->
    <script src="assets/jquery-3.6.0.min.js"></script>
    <!-- Moment JS -->
    <script src="assets/moment.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="assets/bootstrap-4.5.3-dist/js/bootstrap.min.js"></script>
    <!-- AdminLTE JS -->
    <script src="assets/AdminLTE-3.2.0/dist/js/adminlte.min.js"></script>
    <script>
        $(document).ready(function() {
            // Edit Hosteller Modal
            $('.edit-hosteller').click(function() {
                var hostellerId = $(this).data('hosteller-id');
                // Get the hosteller details using AJAX and populate the form fields
                $.ajax({
                    url: 'get-hosteller-details.php',
                    type: 'POST',
                    data: {
                        hosteller_id: hostellerId
                    },
                    success: function(response) {
                        // Parse the JSON response
                        var hosteller = JSON.parse(response);
                        // Set the values in the edit hosteller modal
                        $('#edit_hosteller_id').val(hosteller.id);
                        $('#edit_hosteller_name').val(hosteller.name);
                        $('#edit_hosteller_email').val(hosteller.email);
                        $('#edit_hosteller_phone').val(hosteller.phone);
                        $('#edit_hosteller_gender').val(hosteller.gender);
                        $('#edit_hosteller_address').val(hosteller.address);
                        $('#edit_hosteller_dob').val(hosteller.dob);
                        $('#edit_hosteller_type').val(hosteller.hosteller_type);
                        $('#edit_hosteller_food').val(hosteller.hosteller_food);
                    }
                });
            });

            // Edit Hosteller Form Submission
            $('#edit-hosteller-form').submit(function(e) {
                e.preventDefault();

                // Get the form data
                var hostellerId = $('#edit_hosteller_id').val();
                var hostellerName = $('#edit_hosteller_name').val();
                var hostellerEmail = $('#edit_hosteller_email').val();
                var hostellerPhone = $('#edit_hosteller_phone').val();
                var hostellerGender = $('#edit_hosteller_gender').val();
                var hostellerAddress = $('#edit_hosteller_address').val();
                var hostellerDob = $('#edit_hosteller_dob').val();
                var hostellerType = $('#edit_hosteller_type').val();
                var hostellerFood = $('#edit_hosteller_food').val();

                // Send the AJAX request to edit the hosteller
                $.ajax({
                    url: 'edit-hosteller.php',
                    type: 'POST',
                    data: {
                        hosteller_id: hostellerId,
                        name: hostellerName,
                        email: hostellerEmail,
                        phone: hostellerPhone,
                        gender: hostellerGender,
                        address: hostellerAddress,
                        dob: hostellerDob,
                        hosteller_type: hostellerType,
                        food: hostellerFood
                    },
                    success: function(response) {
                        // Reload the page to display the updated hosteller list
                        window.location.reload();
                    }
                });
            });

            // Delete Hosteller Modal
            $('.delete-hosteller').click(function() {
                var hostellerId = $(this).data('hosteller-id');
                // Set the value in the delete hosteller modal
                $('#delete_hosteller_id').val(hostellerId);
            });

            //Add this script after your existing JavaScript code
            $(document).ready(function() {
                // Delete Hosteller Form Submission
                $('#delete-hosteller-form').submit(function(e) {
                    e.preventDefault();

                    // Get the form data
                    var hostellerId = $('#delete_hosteller_id').val();

                    // Send the AJAX request to delete the hosteller
                    $.ajax({
                        url: 'delete-hosteller.php',
                        type: 'POST',
                        data: {
                            hosteller_id: hostellerId
                        },
                        success: function(response) {
                            // Close the modal
                            $('#deleteModal').modal('hide');
                            // Reload the page to display the updated hosteller list
                            window.location.reload();
                        }
                    });
                });
            });
        });
    </script>
</body>
</html>
