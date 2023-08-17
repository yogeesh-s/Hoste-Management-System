<?php
// Start the session
session_start();

// Check if the hosteller is logged in
if (!isset($_SESSION['hosteller_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Include the database connection file
require_once 'connection.php';

// Retrieve the hosteller's information using the session variable
$hostellerId = $_SESSION['hosteller_id'];

// Query to fetch the hosteller's information
$sql = "SELECT * FROM hostellers WHERE id = '$hostellerId'";
$result = $conn->query($sql);

// Check if the hosteller record exists
if ($result->num_rows === 1) {
    // Hosteller record found, display the information
    $row = $result->fetch_assoc();

    // Display the hosteller information
    echo '<h2>Hosteller Information</h2>';
    echo '<p><strong>Name:</strong> ' . $row['name'] . '</p>';
    echo '<p><strong>Email:</strong> ' . $row['email'] . '</p>';
    echo '<p><strong>Phone:</strong> ' . $row['phone'] . '</p>';
    echo '<p><strong>Gender:</strong> ' . $row['gender'] . '</p>';
    echo '<p><strong>Address:</strong> ' . $row['address'] . '</p>';
    echo '<p><strong>Date of Birth:</strong> ' . $row['dob'] . '</p>';
    echo '<p><strong>Hosteller Type:</strong> ' . $row['hosteller_type'] . '</p>';

    // Update photo form
    echo '<h2>Update Photo</h2>';
    echo '<form id="update-photo-form" enctype="multipart/form-data">';
    echo '<div class="form-group">
            <label for="photo">New Photo:</label>
            <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            <div id="photo-preview-container">
                <img id="photo-preview" src="uploads/' . $row['photo'] . '" alt="Current Photo">
            </div>
            <button type="submit" class="btn btn-primary mt-2">Upload</button>
          </div>';
    echo '</form>';
} else {
    // No hosteller record found
    echo 'Hosteller not found.';
}

// Close the database connection
$conn->close();
?>
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

    // Update photo form submission
    $('#update-photo-form').submit(function(e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            url: 'update_photo.php', // Replace with your PHP file for updating photo
            type: 'POST',
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(response) {
                // Handle the response from the PHP file
                alert(response); // Display success message or perform desired action
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        });
    });
});
</script>
