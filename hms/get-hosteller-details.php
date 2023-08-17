<?php
// Include the database connection file
include 'connection.php';

// Check if the hosteller ID is provided
if (isset($_POST['hosteller_id'])) {
    $hostellerId = $_POST['hosteller_id'];

    // Prepare the SQL statement to fetch hosteller details
    $sql = "SELECT * FROM hostellers WHERE id = $hostellerId";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        // Fetch the hosteller details as an associative array
        $hosteller = $result->fetch_assoc();

        // Return the hosteller details as a JSON response
        echo json_encode($hosteller);
    } else {
        // Hosteller not found
        echo json_encode(['error' => 'Hosteller not found']);
    }
} else {
    // Invalid request
    echo json_encode(['error' => 'Invalid request']);
}

// Close the database connection
$conn->close();
?>
