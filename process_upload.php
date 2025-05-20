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
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["proofFile"])) {
    $fullName = $conn->real_escape_string($_POST['full_name']);
    $email = $conn->real_escape_string($_POST['email']);
    $contact = $conn->real_escape_string($_POST['contact']);

    // Validate and process file
    $file = $_FILES["proofFile"];
    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

    if ($fileType !== "pdf") {
        echo "<script>alert('Invalid file type! Only PDF is allowed.'); window.history.back();</script>";
        exit();
    }

    // Create uploads directory if it doesn't exist
    $uploadDir = 'uploads/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Generate a unique transaction number (e.g., SSE-10000)
    $uniqueIdPrefix = "SSE-";
    do {
        $randomNumber = rand(1000, 99999);
        $uniqueId = $uniqueIdPrefix . $randomNumber;
        $checkQuery = "SELECT unique_id FROM submissions WHERE unique_id = '$uniqueId'";
        $result = $conn->query($checkQuery);
    } while ($result->num_rows > 0);

    // Set analysis type and filename based on form type
    $analysisType = "Shelf Life Evaluation";
    $fileName = "Shelflife_Evaluation" . $uniqueId . ".pdf";
    
    // If this is a microbiological analysis submission
    if (isset($_POST['form_type']) && $_POST['form_type'] === 'micro') {
        $analysisType = "Sensory Evaluation";
        $fileName = "proof_of_microbiological_analysis" . $uniqueId . ".pdf";
    }

    $targetPath = $uploadDir . $fileName;

    // Move uploaded file to uploads directory
    if (move_uploaded_file($file["tmp_name"], $targetPath)) {
        // Generate a unique submission_id
        $submissionId = strval(time()) . rand(1000, 9999);
        $currentDate = date('Y-m-d H:i:s');

        // Insert into database with file path
        $sql = "INSERT INTO submissions (submission_id, unique_id, full_name, contact_number, email_address, analysis, category, lab_id, quantity, status, submission_date, file) 
                VALUES (?, ?, ?, ?, ?, ?, 'Food', 4, 1, 1, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $submissionId, $uniqueId, $fullName, $contact, $email, $analysisType, $currentDate, $targetPath);

        if ($stmt->execute()) {
            // Redirect to appointment selection
            header("Location: appointment.php?submission_id=$submissionId");
            exit();
        } else {
            // If database insert fails, delete the uploaded file
            unlink($targetPath);
            echo "<script>alert('Error saving data: " . $stmt->error . "'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Error uploading file!'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No file uploaded!'); window.history.back();</script>";
}
?>
