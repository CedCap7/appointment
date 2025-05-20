<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "scheduling_form";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Fetch Submission ID
if (!isset($_GET['submission_id'])) {
    echo json_encode(["status" => "error", "message" => "Submission ID missing"]);
    exit();
}

$submissionId = $conn->real_escape_string($_GET['submission_id']);

// Fetch lab_id and requestType from database
$sql = "SELECT lab_id, request_type, category FROM submissions WHERE submission_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $submissionId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data) {
    echo json_encode(["status" => "success", "lab_id" => $data['lab_id'], "request_type" => $data['request_type'], "category" => $data['category']]);
} else {
    echo json_encode(["status" => "error", "message" => "No data found"]);
}

$stmt->close();
$conn->close();
?>
