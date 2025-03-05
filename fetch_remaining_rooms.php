<?php
session_cache_limiter('nocache');
include("scripts/settings.php");
logvalidate($_SESSION['username'], $_SERVER['SCRIPT_FILENAME']);
logvalidate('admin');

if (isset($_GET['sno']) && isset($_GET['check_in'])) {
    $roomsno = mysqli_real_escape_string($db, $_GET['sno']);
    $checkinDate = date('Y-m-d', strtotime(mysqli_real_escape_string($db, $_GET['check_in'])));

    // Get total available rooms for the category
    $query = "SELECT remarks FROM category WHERE sno = '$roomsno' LIMIT 1";
    $result = execute_query($query);

    if ($row = mysqli_fetch_assoc($result)) {
        $totalAvailableRooms = intval($row['remarks']); // Convert to integer

        // Fetch all bookings for the given date where cat_id is present
        $bookingQuery = "SELECT cat_id, number_of_room FROM advance_booking WHERE FIND_IN_SET('$roomsno', cat_id) > 0 AND DATE(check_in) = '$checkinDate'";
        $bookingResult = mysqli_query($db, $bookingQuery);

        $bookedRooms = 0;

        while ($bookingRow = mysqli_fetch_assoc($bookingResult)) {
            $catIds = explode(',', $bookingRow['cat_id']); // Convert cat_id string to array
            $roomNumbers = explode(',', $bookingRow['number_of_room']); // Convert number_of_room string to array

            // Map cat_id to number_of_room and fetch the correct value for the selected category
            $catRoomMap = array_combine($catIds, $roomNumbers); // Creates key-value pair (cat_id => number_of_room)

            if (isset($catRoomMap[$roomsno])) {
                $bookedRooms += intval($catRoomMap[$roomsno]); // Add only the matched cat_id's room count
            }
        }

        // Calculate remaining rooms
        $remainingRooms = max(0, $totalAvailableRooms - $bookedRooms); // Ensure it doesn't go negative

        echo $remainingRooms;
    } else {
        echo "0"; // If no category found, return 0
    }
}

?>
