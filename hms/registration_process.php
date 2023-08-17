<?php
// Include the database connection file
require_once 'connection.php';

// Function to generate a unique photo name
function generateUniquePhotoName($photoName)
{
    $extension = pathinfo($photoName, PATHINFO_EXTENSION);
    $filename = md5(uniqid()) . '.' . $extension;
    return $filename;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $password = $_POST['password'];
    $hostellerType = $_POST['hosteller-type'];
    $food = $_POST['food'];

    // Handle file upload
    $photoName = $_FILES['photo']['name'];
    $photoTmpName = $_FILES['photo']['tmp_name'];

    // Generate a unique photo name
    $uniquePhotoName = generateUniquePhotoName($photoName);

    // Move the uploaded photo to a specific folder
    $photoDestination = 'uploads/' . $uniquePhotoName;
    move_uploaded_file($photoTmpName, $photoDestination);

    // Hash the password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert form data into the database
    $sql = "INSERT INTO hostellers (name, email, phone, gender, address, dob, password, hosteller_type, photo, food)
            VALUES ('$name', '$email', '$phone', '$gender', '$address', '$dob', '$hashedPassword', '$hostellerType', '$photoDestination', '$food')";
    
    if ($conn->query($sql) === TRUE) {
        // Registration success, redirect to login.php
        header('Location: login.php');
        exit();
    } else {
        // Error occurred, handle it accordingly
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close the database connection
$conn->close();
