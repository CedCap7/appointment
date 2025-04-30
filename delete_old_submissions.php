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
$apiKey = "7b514f8560aaffa77243ce0ab3b877e0"; // Your JotForm API Key
$formId = "250561379626463"; // Your JotForm Form ID

// Calculate the threshold date (15 days ago)
$thresholdDate = date("Y-m-d", strtotime("-15 days"));

// ** 1️⃣ Fetch Submissions from JotForm API **  
$url = "https://api.jotform.com/form/{$formId}/submissions?apiKey={$apiKey}";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);

$data = json_decode($response, true);

if (!empty($data['content'])) {
    foreach ($data['content'] as $submission) {
        $submissionId = $submission['id'];
        $appointmentDate = "";

        // Extract the appointment date from JotForm submission
        foreach ($submission['answers'] as $answer) {
            if ($answer['name'] == "appointment") { // Adjust this field name if needed
                $appointmentDate = date("Y-m-d", strtotime($answer['answer']));
                break;
            }
        }

        // **Delete JotForm Submission if Older than 15 Days**
        if (!empty($appointmentDate) && $appointmentDate < $thresholdDate) {
            $deleteUrl = "https://api.jotform.com/submission/{$submissionId}?apiKey={$apiKey}";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $deleteUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $deleteResponse = curl_exec($ch);
            curl_close($ch);

            echo "✅ Deleted JotForm submission ID: $submissionId (Appointment Date: $appointmentDate) <br>";
        }
    }
} else {
    echo "⚠ No expired JotForm submissions found.<br>";
}

// Close MySQL connection
$conn->close();
?>
