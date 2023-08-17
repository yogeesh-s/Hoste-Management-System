<?php
// Include the database connection file
include 'connection.php';

// Check if the hosteller ID is provided
if (isset($_POST['hosteller_id'])) {
    // Get the hosteller ID
    $hostellerId = $_POST['hosteller_id'];

    // Prepare the SQL statement to delete the hosteller

    $sql = "DELETE FROM hostellers WHERE id = $hostellerId";
    $sql1 = "DELETE FROM `room_allotment` WHERE hostellers_id = $hostellerId";
    $sql2 = "DELETE FROM `attendance` WHERE hosteller_id = $hostellerId;";
    $conn->query($sql1);
    $conn->query($sql2);

    if ($conn->query($sql) === TRUE) {
        // Hosteller deleted successfully
        echo json_encode(['success' => 'Hosteller deleted successfully']);
    } else {
        // Error deleting hosteller
        echo json_encode(['error' => 'Error deleting hosteller']);
    }
} else {
    // Invalid request
    echo json_encode(['error' => 'Invalid request']);
}

// Close the database connection
$conn->close();
?>
