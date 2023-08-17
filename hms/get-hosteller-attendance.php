<?php
// Include the connection.php file
require_once 'connection.php';

// Check if hostellerId is received via POST
if (isset($_POST['hostellerId'])) {
    $hostellerId = $_POST['hostellerId'];

    // Get the current month
    $currentMonth = date('Y-m');

    // Prepare the query to fetch attendance data for the hosteller in the current month
    $attendanceQuery = "SELECT attendance_date FROM attendance WHERE hosteller_id = ? AND DATE_FORMAT(attendance_date, '%Y-%m') = ?";
    $attendanceStmt = $conn->prepare($attendanceQuery);
    $attendanceStmt->bind_param("is", $hostellerId, $currentMonth);
    $attendanceStmt->execute();
    $attendanceResult = $attendanceStmt->get_result();
    $attendanceData = array();

    // Fetch the attendance data into an array
    while ($row = $attendanceResult->fetch_assoc()) {
        $attendanceDate = $row['attendance_date'];
        // Extract the day from the date
        $day = date('d', strtotime($attendanceDate));
        $attendanceData[$day] = isset($attendanceData[$day]) ? $attendanceData[$day] + 1 : 1;
    }

    // Send the attendance data as a JSON response
    echo json_encode($attendanceData);
} else {
    // Send an error response if hostellerId is not received
    echo json_encode(array('error' => 'Hosteller ID not provided.'));
}
?>
