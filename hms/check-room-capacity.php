<?php
// Include the connection.php file
require_once 'connection.php';

if (isset($_POST['room_id'])) {
    $roomId = $_POST['room_id'];

    // Create the SQL query to check the room capacity
    $roomCapacityQuery = "SELECT room_capacity FROM rooms WHERE id = '$roomId'";
    $roomCapacityResult = $conn->query($roomCapacityQuery);

    if ($roomCapacityResult) {
        $roomCapacityRow = $roomCapacityResult->fetch_assoc();
        $roomCapacity = $roomCapacityRow['room_capacity'];

        // Create the SQL query to count the number of allotted rooms for the given room ID
        $allottedRoomsQuery = "SELECT COUNT(*) as count FROM room_allotment WHERE room_id = '$roomId' AND deallocation_date IS NULL";
        $allottedRoomsResult = $conn->query($allottedRoomsQuery);

        if ($allottedRoomsResult) {
            $allottedRoomsRow = $allottedRoomsResult->fetch_assoc();
            $allottedRoomsCount = $allottedRoomsRow['count'];

            $remainingCapacity = $roomCapacity - $allottedRoomsCount;

            // Return the remaining capacity as a JSON response
            echo json_encode(array('remaining_capacity' => $remainingCapacity));
        } else {
            // Error occurred while counting allotted rooms
            echo json_encode(array('remaining_capacity' => -1));
        }
    } else {
        // Error occurred while retrieving room capacity
        echo json_encode(array('remaining_capacity' => -1));
    }
}
?>
<script>
function checkRoomCapacity(roomId) {
    // Make an AJAX request to check the room capacity
    $.ajax({
        url: 'check-room-capacity.php',
        type: 'POST',
        data: { room_id: roomId },
        dataType: 'json',
        success: function(data) {
            var remainingCapacity = data.remaining_capacity;
            var roomSelect = $('#room_id');
            
            // Enable all options
            roomSelect.find('option').prop('disabled', false);
            
            // Disable options if room is full
            if (remainingCapacity <= 0) {
                roomSelect.find('option').not(':selected').prop('disabled', true);
            }
        },
        error: function() {
            alert('Error occurred while checking room capacity.');
        }
    });
}
</script>