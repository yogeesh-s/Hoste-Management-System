<?php
// Include the necessary files and start the session at the beginning
require_once 'connection.php';
session_start();

// Get the hosteller ID
$hostellerId = $_SESSION['hosteller_id'];

// SQL query to fetch attendance details for the specific hosteller
$attendanceQuery = "SELECT attendance_date AS start, attendance_date AS end, status FROM attendance WHERE hosteller_id = '$hostellerId'";
$attendanceResult = $conn->query($attendanceQuery);

// Create an empty array to store the events
$events = [];

if ($attendanceResult->num_rows > 0) {
    while ($row = $attendanceResult->fetch_assoc()) {
        // Format the attendance date
        $attendanceDate = date('Y-m-d', strtotime($row['start']));

        // Create an event object
        $event = [
            'title' => $row['status'],
            'start' => $attendanceDate,
            'end' => $attendanceDate
        ];

        // Add the event to the events array
        $events[] = $event;
    }
}

// Convert the events array to JSON format
$jsonEvents = json_encode($events);

// Set the appropriate response headers
header('Content-Type: application/json');
echo $jsonEvents;
?>