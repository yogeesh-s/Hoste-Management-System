<?php
// Include the database connection file
include 'connection.php';

// Check if the form data is submitted
if (isset($_POST['hosteller_id'])) {
    // Get the form data
    $hostellerId = $_POST['hosteller_id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $hostellerType = $_POST['hosteller_type'];
    $food = $_POST['food'];

    // Prepare the SQL statement to update hosteller details
    $sql = "UPDATE hostellers SET 
            name = ?,
            email = ?,
            phone = ?,
            gender = ?,
            address = ?,
            dob = ?,
            hosteller_type = ?,
            food = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssssi", $name, $email, $phone, $gender, $address, $dob, $hostellerType, $food, $hostellerId);

    if ($stmt->execute()) {
        // Hosteller details updated successfully
        echo json_encode(['success' => 'Hosteller details updated successfully']);
    } else {
        // Error updating hosteller details
        echo json_encode(['error' => 'Error updating hosteller details']);
    }

    $stmt->close();
} else {
    // Invalid request
    echo json_encode(['error' => 'Invalid request']);
}

// Close the database connection
$conn->close();
?>
