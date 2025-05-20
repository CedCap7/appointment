<?php
// Set JSON Header
header('Content-Type: application/json');

// Database Connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "scheduling_form";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Fetch Submission ID
$submissionId = isset($_GET['submission_id']) ? $conn->real_escape_string($_GET['submission_id']) : null;
$submissionQuantity = 0;
$labId = null;
$category = null;
$analysis = null;
$requestType = null;
$maxSlots = 10; // Default slot limit

if ($submissionId) {
    // Fetch lab_id, category, quantity, analysis, and request_type
    $stmt = $conn->prepare("SELECT lab_id, category, quantity, analysis, request_type FROM submissions WHERE submission_id = ?");
    $stmt->bind_param("s", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $labId = (int) $row['lab_id'];
        $category = $row['category'];
        $analysis = $row['analysis'];
        $requestType = $row['request_type']; 
        $submissionQuantity = (int) $row['quantity'];
    }
    $stmt->close();

    // ✅ Set Max Slots based on Category
    if ($labId == 1) { 
        if ($requestType === "In-House") {
            $maxSlots = 6; // In-House: 6 slots max
        } elseif ($requestType === "On-Site") {
            $maxSlots = 1; // On-Site: 1 slot max
        } else {
            $maxSlots = 6; // Default for In-House (if request type unknown)
        }
    } elseif ($labId == 4) {
        $maxSlots = 2; // ✅ Fixed 2 slots for Lab 4
    } else {
        $maxSlots = 10; // ✅ Default for other labs
    }
}

// ✅ Fetch Booked Dates (Correct Handling for Each Lab)
$bookedDates = [];

if ($labId == 3 || $labId == 2) {
    // ✅ For lab_id 2 & 3, count all booked samples
    $sql = "SELECT submission_date_selected, SUM(quantity) AS totalBooked
            FROM submissions 
            WHERE lab_id = ? 
            GROUP BY submission_date_selected";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $labId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $date = $row['submission_date_selected'];
        $totalBooked = (int) $row['totalBooked'];
        $remainingSlots = $maxSlots - $totalBooked;

        // ✅ Show Remaining Slots instead of "Vacant"
        if ($remainingSlots <= 0) {
            $eventTitle = "Fully Booked!";
            $eventColor = "#ff0000"; // ✅ Red for full
        } else {
            $eventTitle = "$remainingSlots Slots Available";
            $eventColor = "#28a745"; // ✅ Green for available
        }

        $bookedDates[$date] = [
            "title" => $eventTitle,
            "start" => $date,
            "remainingSlots" => $remainingSlots,
            "color" => $eventColor, 
            "bookable" => ($submissionQuantity <= $remainingSlots)
        ];
    }
} elseif ($labId == 1) {
    // For lab_id 1, check if the date is already booked for this submission
    $sql = "SELECT submission_date_selected, COUNT(*) as isBooked
            FROM submissions 
            WHERE submission_id = ? AND submission_date_selected IS NOT NULL";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $bookedDate = $row['submission_date_selected'];
        $bookedDates[$bookedDate] = [
            "title" => "Already Booked",
            "start" => $bookedDate,
            "color" => "#ff0000", // Red for booked
            "bookable" => false
        ];
    }
    
    // Also get all booked dates for this lab
    $sql = "SELECT submission_date_selected, COUNT(*) as totalBooked
            FROM submissions 
            WHERE lab_id = 1 AND submission_date_selected IS NOT NULL
            GROUP BY submission_date_selected";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $date = $row['submission_date_selected'];
        $totalBooked = (int) $row['totalBooked'];
        $remainingSlots = $maxSlots - $totalBooked;

        if (!isset($bookedDates[$date])) { // Only set if not already marked as booked by this submission
            if ($remainingSlots <= 0) {
                $eventTitle = "Fully Booked!";
                $eventColor = "#ff0000";
                $bookable = false; // Make fully booked dates unbookable
            } else {
                $eventTitle = "$remainingSlots Slots Available";
                $eventColor = "#28a745";
                $bookable = ($submissionQuantity <= $remainingSlots);
            }

            $bookedDates[$date] = [
                "title" => $eventTitle,
                "start" => $date,
                "remainingSlots" => $remainingSlots,
                "color" => $eventColor,
                "bookable" => $bookable
            ];
        }
    }
} elseif ($labId == 4) {
    // ✅ Lab 4: Show remaining slots instead of booked clients
    $sql = "SELECT submission_date_selected, COUNT(DISTINCT submission_id) AS totalClients
            FROM submissions 
            WHERE lab_id = 4 
            GROUP BY submission_date_selected";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $date = $row['submission_date_selected'];
        $totalClients = (int) $row['totalClients'];
        $remainingSlots = $maxSlots - $totalClients;

        // ✅ Show Remaining Slots instead of Clients Booked
        if ($remainingSlots <= 0) {
            $eventTitle = "Fully Booked!";
            $eventColor = "#ff0000"; // ✅ Red for full
        } else {
            $eventTitle = "$remainingSlots Slots Available";
            $eventColor = "#28a745"; // ✅ Green for available
        }

        $bookedDates[$date] = [
            "title" => $eventTitle,
            "start" => $date,
            "color" => $eventColor,
            "remainingSlots" => $remainingSlots,
            "bookable" => ($remainingSlots > 0) // ✅ Only bookable if slots remain
        ];
    }
}

// ✅ Generate Vacant Dates (Only show remaining slots)
$events = [];
$today = new DateTime();
$today->modify('+3 days'); // Booking allowed only 3 days ahead

for ($i = 0; $i < 365; $i++) { // ✅ Generate 1 year ahead
    $formattedDate = $today->format("Y-m-d");
    $dayOfWeek = $today->format("w"); // 0=Sunday, ..., 6=Saturday

    // ✅ Allowed days based on lab_id & request_type
    $allowedDays = [];
    if ($labId == 1) {
        if ($requestType === "In-House") {
            $allowedDays = [6]; // ✅ Saturdays only
        } elseif ($requestType === "On-Site") {
            $allowedDays = [3, 4, 5]; // Wednesday, Thursday, Friday
        }
    } else {
        switch ($labId) {
            case 2:
                // Set allowed days based on category for lab_id 2
                $categoryLower = strtolower($category);
                if (strpos($categoryLower, 'food') !== false || strpos($categoryLower, 'feeds') !== false) {
                    $allowedDays = [1, 2]; // Monday and Tuesday
                } elseif (strpos($categoryLower, 'water') !== false) {
                    $allowedDays = [2, 3]; // Tuesday and Wednesday
                } else {
                    $allowedDays = [1, 2, 3]; // Monday, Tuesday, and Wednesday
                }
                break;
            case 3:
                // Set allowed days based on category for lab_id 3
                $categoryLower = strtolower($category);
                if (strpos($categoryLower, 'food') !== false) {
                    $allowedDays = [1]; // Monday only
                } elseif (strpos($categoryLower, 'swab') !== false) {
                    $allowedDays = [1, 2]; // Monday and Tuesday
                } elseif (strpos($categoryLower, 'water') !== false) {
                    $allowedDays = [2, 3]; // Tuesday and Wednesday
                } else {
                    $allowedDays = [1, 2]; // Default: Monday and Tuesday
                }
                break;
            case 4: $allowedDays = [1, 2]; break; // ✅ Mon-Tue
        }
    }

    if (in_array($dayOfWeek, $allowedDays)) { 
        if (!isset($bookedDates[$formattedDate])) {
            $events[] = [
                "title" => "$maxSlots Slots Available", // ✅ Show slots instead of "Vacant"
                "start" => $formattedDate,
                "remainingSlots" => $maxSlots,
                "color" => "#28a745", // ✅ Green for available
                "bookable" => true // ✅ Always bookable
            ];
        } else {
            $events[] = $bookedDates[$formattedDate];
        }
    }

    $today->modify("+1 day"); // ✅ Move to the next day
}

// ✅ Return JSON Output
echo json_encode($events, JSON_PRETTY_PRINT);
$conn->close();
exit();
