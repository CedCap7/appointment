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
    echo json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]);
    exit();
}

// Get submission_id and selected_date from GET request
$submissionId = isset($_GET['submission_id']) ? $conn->real_escape_string($_GET['submission_id']) : null;
$selectedDate = isset($_GET['selected_date']) ? $conn->real_escape_string($_GET['selected_date']) : null;

if (!$submissionId || !$selectedDate) {
    echo json_encode(["status" => "error", "message" => "Missing parameters."]);
    exit();
}

// Fetch lab_id and quantity for this submission_id
$stmt = $conn->prepare("SELECT lab_id, quantity FROM submissions WHERE submission_id = ?");
$stmt->bind_param("s", $submissionId);
$stmt->execute();
$result = $stmt->get_result();
if ($row = $result->fetch_assoc()) {
    $labId = (int) $row['lab_id'];
    $submissionQuantity = (int) $row['quantity'];
} else {
    echo json_encode(["status" => "error", "message" => "Invalid Submission ID."]);
    exit();
}
$stmt->close();

// Check total booked slots for this date and lab_id
$stmt = $conn->prepare("SELECT COUNT(*) as totalBooked FROM submissions WHERE submission_date_selected = ? AND lab_id = ?");
$stmt->bind_param("si", $selectedDate, $labId);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$totalBooked = $row['totalBooked'] ?? 0;

// Get max slots based on lab_id and request type
$maxSlots = 10; // Default
if ($labId == 1) {
    // Get request type for this submission
    $stmt = $conn->prepare("SELECT request_type FROM submissions WHERE submission_id = ?");
    $stmt->bind_param("s", $submissionId);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $requestType = $row['request_type'];
        $maxSlots = ($requestType === "In-House") ? 6 : 1;
    }
} elseif ($labId == 4) {
    $maxSlots = 2;
}

$remainingSlots = $maxSlots - $totalBooked;

// Check if the requested quantity exceeds available slots
if ($remainingSlots <= 0) {
    echo json_encode(["bookable" => false, "message" => "This date is fully booked."]);
} elseif ($submissionQuantity > $remainingSlots) {
    echo json_encode(["bookable" => false, "message" => "Not enough slots available."]);
} else {
    echo json_encode(["bookable" => true, "message" => "Date is available."]);
}

$conn->close();
exit();
