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

// Validate form inputs
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["formFile"])) {
    $fullName = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);

    // Generate a unique submission_id
    do {
        $submissionId = strval(rand(6182708182616550000, 6182708182616599999));
        $checkQuery = "SELECT submission_id FROM submissions WHERE submission_id = '$submissionId'";
        $result = $conn->query($checkQuery);
    } while ($result->num_rows > 0);

    // Generate a unique transaction number (e.g., SLE-12000)
    $uniqueIdPrefix = "SLE-";
    do {
        $randomNumber = rand(1000, 99999);
        $uniqueId = $uniqueIdPrefix . $randomNumber;
        $checkQuery = "SELECT unique_id FROM submissions WHERE unique_id = '$uniqueId'";
        $result = $conn->query($checkQuery);
    } while ($result->num_rows > 0);

    // Validate and process file
    $file = $_FILES["formFile"];
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($fileType !== "pdf") {
        echo "<script>alert('Invalid file type! Only PDF is allowed.'); window.history.back();</script>";
        exit();
    }

    // Insert into database
    $sql = "INSERT INTO submissions (submission_id, unique_id, full_name, contact_number, email_address, analysis, category, lab_id, status, quantity) 
            VALUES ('$submissionId', '$uniqueId', '$fullName', '$contact', '$email', 'Shelf Life Evaluation', 'Food', 4, 1, 1)";

    if ($conn->query($sql) === TRUE) {
        // Redirect to appointment selection
        header("Location: appointment.php?submission_id=$submissionId");
        exit();
    } else {
        echo "<script>alert('Error saving data: " . $conn->error . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No file uploaded!'); window.history.back();</script>";
}
?>
