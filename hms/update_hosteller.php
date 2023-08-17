<?php
// Include the necessary files and start the session at the beginning
require_once 'connection.php';
session_start();

// Check if the hosteller is logged in
if (!isset($_SESSION['hosteller_id'])) {
    // If not logged in, redirect to the login page
    header('Location: login.php');
    exit();
}

// Retrieve the hosteller's information using the session variable
$hostellerId = $_SESSION['hosteller_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $hostellerType = $_POST['hosteller-type'];
    $newPassword = $_POST['password'];
    $food = $_POST['food'];

    // Check if a new profile photo is uploaded
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        // Process the uploaded photo
        $photoTmpPath = $_FILES['photo']['tmp_name'];
        $photoExtension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);

        // Generate a unique name for the profile photo
        $photoName = uniqid('photo_') . '_' . time();

        // Set the target photo path
        $photoPath = 'uploads/' . $photoName . '.' . $photoExtension;

        // Move the uploaded photo to the target location
        if (move_uploaded_file($photoTmpPath, $photoPath)) {
            // Update the photo path in the database
            $sql = "UPDATE hostellers SET photo = '$photoPath' WHERE id = '$hostellerId'";
            $conn->query($sql);
        }
    }

    // Update the hosteller's information in the database
    $sql = "UPDATE hostellers SET name = '$name', food = '$food', email = '$email', phone = '$phone', gender = '$gender', address = '$address', dob = '$dob', hosteller_type = '$hostellerType' WHERE id = '$hostellerId'";
    $conn->query($sql);

    // Check if a new password is provided
    if (!empty($newPassword)) {
        // Update the password in the database
        //$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $sql = "UPDATE hostellers SET password = '$newPassword' WHERE id = '$hostellerId'";
        $conn->query($sql);
    }

    // Redirect to the dashboard page
    header('Location: dashboard.php');
    exit();
}

// Close the database connection
$conn->close();
?>
