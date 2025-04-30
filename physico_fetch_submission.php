<?php
// Database Connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "scheduling_form";

// Create MySQL connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// JotForm API Credentials
$apiKey = "d37e51d6aad09b119b74247149f5fadb"; // Replace with your JotForm API Key
$formId = "250771604192455"; // Update to match Physico-Chemical Testing form ID

// API URL to fetch the latest submission
$url = "https://api.jotform.com/form/{$formId}/submissions?apiKey={$apiKey}&limit=1&orderby=created_at,DESC";

// Fetch data using cURL
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    die(json_encode(["error" => "JSON decode error: " . json_last_error_msg()]));
}

// Check if submissions exist
if (!empty($data['content'])) {
    $submission = $data['content'][0]; // Get the latest submission
    $submissionId = $conn->real_escape_string($submission['id']);
    $submissionDate = date("Y-m-d H:i:s", strtotime($submission['created_at']));

    // Initialize variables
    $uniqueId = $fullName = $address = $contactNumber = $emailAddress = $category = $requestType = $analysis = "";
    $quantity = 0;
    $labId = 2; // **Physico-Chemical Testing**

    // Extract form answers
    foreach ($submission['answers'] as $answer) {
        $fieldName = $answer['name'];
        $fieldValue = isset($answer['answer']) ? $answer['answer'] : '';

        if (is_array($fieldValue)) {
            $fieldValue = implode('#', $fieldValue); // Store multiple selections using "#"
        }

        $fieldValue = $conn->real_escape_string($fieldValue);

        switch ($fieldName) {
            case "uniqueId": 
                $uniqueId = $fieldValue; 
                break;
            case "fullName": 
                if (isset($answer['answer']['first']) && isset($answer['answer']['last'])) {
                    $fullName = trim($conn->real_escape_string($answer['answer']['first'] . ' ' . $answer['answer']['last']));
                } else {
                    $fullName = $fieldValue;
                }
                break;
            case "address": 
                $address = $fieldValue; 
                break;
            case "contactNumber": 
                $contactNumber = $fieldValue; 
                break;
            case "emailAddress": 
                $emailAddress = $fieldValue; 
                break;
            case "categoryValue": 
                $category = $fieldValue; 
                break;
            case "quantity": 
                $quantity = intval($fieldValue); 
                break;
            case "requestType": 
                $requestType = $fieldValue; 
                break;
            case "selectedAnalysis": 
                $analysis = $fieldValue; 
                break;
        }
    }

    // **Check if submission already exists**
    $stmt = $conn->prepare("SELECT submission_id FROM submissions WHERE submission_id = ?");
    $stmt->bind_param("s", $submissionId);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows == 0) {
        $stmt->close();

        // **Insert New Submission**
        $status = 1; // Default status (Pending)
        $stmt = $conn->prepare("INSERT INTO submissions 
            (submission_id, unique_id, submission_date, full_name, address, contact_number, email_address, category, quantity, request_type, analysis, status, lab_id) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param("ssssssssissii", 
            $submissionId, $uniqueId, $submissionDate, $fullName, $address, 
            $contactNumber, $emailAddress, $category, $quantity, $requestType, $analysis, $status, $labId
        );

        if ($stmt->execute()) {
            header("Location: appointment.php?submission_id=$submissionId");
            exit();
        } else {
            die(json_encode(["error" => "Error saving to database: " . $stmt->error]));
        }
    } else {
        header("Location: reservation_details.php?submission_id=$submissionId");
        exit();
    }

    $stmt->close();
} else {
    die(json_encode(["error" => "No new submissions found."]));
}

// Close MySQL connection
$conn->close();
?>
