<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Hosteller Information</title>
    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="assets/AdminLTE-3.2.0/dist/css/adminlte.min.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="assets/bootstrap-4.5.3-dist/css/bootstrap.min.css">
    <!-- Font Awesome CSS -->
    <link rel="stylesheet" href="assets/fontawesome-free-5.15.4-web/css/all.min.css"> 
</head>
<body>
    <div class="container">
        <?php
            // Include the database connection file
            require_once 'connection.php';

            // Retrieve the hosteller's information from the database
            $hostellerId = $_SESSION['hosteller_id'];
            $sql = "SELECT * FROM hostellers WHERE id = '$hostellerId'";
            $result = $conn->query($sql);

            if ($result->num_rows === 1) {
                $row = $result->fetch_assoc();
                ?>
                <form id="manage-hosteller-form" method="post" action="update_hosteller.php" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $row['name']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $row['email']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo $row['phone']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender:</label>
                        <select class="form-control" id="gender" name="gender" required>
                            <option value="Male" <?php if ($row['gender'] === 'Male') echo 'selected'; ?>>Male</option>
                            <option value="Female" <?php if ($row['gender'] === 'Female') echo 'selected'; ?>>Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <textarea class="form-control" id="address" name="address" required><?php echo $row['address']; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="dob">Date of Birth:</label>
                        <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $row['dob']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="hosteller-type">Hosteller Type:</label>
                        <select class="form-control" id="hosteller-type" name="hosteller-type" required>
                            <option value="Student" <?php if ($row['hosteller_type'] === 'Student') echo 'selected'; ?>>Student</option>
                            <option value="Employee" <?php if ($row['hosteller_type'] === 'Employee') echo 'selected'; ?>>Employee</option>
                            <option value="Other" <?php if ($row['hosteller_type'] === 'Other') echo 'selected'; ?>>Other</option>
                            <!-- Add more options as needed -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="food">Food Preference:</label>
                        <select class="form-control" id="food" name="food" required>
                            <option value="Required" <?php if ($row['food'] === 'Required') echo 'selected'; ?>>Required</option>
                            <option value="Not Required" <?php if ($row['food'] === 'Not Required') echo 'selected'; ?>>Not Required</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="photo"><i class="fas fa-camera"></i> Photo:</label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="photo" name="photo" accept="image/*" required>
                            <label class="custom-file-label" for="photo">Choose file</label>
                        </div>
                        <img id="photo-preview" src="<?php echo $row['photo']; ?>" alt="Photo Preview" style="max-width: 150px; height: auto;">
                    </div>
                    <div class="form-group">
                        <label for="password">New Password:</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm-password">Confirm Password:</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
        <?php } ?>
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
    <script>
        $(document).ready(function() {
            // Preview photo on file selection
            $('#photo').change(function(e) {
                var file = e.target.files[0];
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#photo-preview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });
        });
    </script>
</body>
</html>
