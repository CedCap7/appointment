<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Include PHPMailer

// Database Connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "scheduling_form";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "Database connection failed: " . $conn->connect_error]));
}

// Validate POST request
if (!isset($_POST['submission_id']) || !isset($_POST['selected_date'])) {
    die(json_encode(["status" => "error", "message" => "Invalid request."]));
}

$submissionId = $conn->real_escape_string($_POST['submission_id']);
$selectedDate = $conn->real_escape_string($_POST['selected_date']);

// Fetch submission details including email_address
$sql = "SELECT * FROM submissions WHERE submission_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $submissionId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die(json_encode(["status" => "error", "message" => "No submission found."]));
}

$row = $result->fetch_assoc();
$stmt->close();

// Update the reservation date
$updateSql = "UPDATE submissions SET submission_date_selected = ? WHERE submission_id = ?";
$stmt = $conn->prepare($updateSql);
$stmt->bind_param("ss", $selectedDate, $submissionId);

if ($stmt->execute()) {
    // Send Confirmation Emails (Admin & Client)
    $mail = new PHPMailer(true);
    
    try {
        // ✅ SMTP Configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'cedrickcap7@gmail.com'; // Admin Email
        $mail->Password = 'todc eodz utod kcik'; // 🔹 Replace with your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // **Determine Category Name**
        $categoryName = ($row['category'] == 1) ? "Renewal" : (($row['category'] == 2) ? "New" : (($row['category'] == 3) ? "Renewal/New" : $row['category']));

        // **Determine Lab Name**
        $labNames = [
            1 => 'Tank Truck Calibration',
            2 => 'Chemical Testing Laboratory',
            3 => 'Microbiological Testing Laboratory',
            4 => 'Shelf Life Laboratory'
        ];
        $labName = isset($labNames[$row['lab_id']]) ? $labNames[$row['lab_id']] : 'Unknown Lab';

        // **Determine Type Labels for `lab_id 1`**
        $sampleLabel = ($row['lab_id'] == 1) ? "Renewal or New" : "Type of Sample";
        $analysisLabel = ($row['lab_id'] == 1) ? "Type of Request" : "Type of Analysis";
        $analysisValue = ($row['lab_id'] == 1) ? $row['request_type'] : $row['analysis'];

        $reservationTime = date('F j, Y \a\t g:i A', strtotime($selectedDate . ' 08:00:00'));
        $reservationCheckLink = "http://192.168.0.214/scheduling/index.php";

        // ✅ Email to Admin
        $mail->setFrom('cedrickcap7@gmail.com', 'RSTL Sample Submission');
        $mail->addAddress('cedrickcap7@gmail.com', 'Admin'); // Admin Email
        $mail->Subject = "New Reservation - " . $row['unique_id'];
        $mail->Body = "
        <html>
        <body>
            <h2>New Reservation Details for $labName</h2>
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr><th>Field</th><th>Details</th></tr>
                <tr><td>Full Name</td><td>{$row['full_name']}</td></tr>
                <tr><td>Email</td><td>{$row['email_address']}</td></tr>
                <tr><td>Contact Number</td><td>{$row['contact_number']}</td></tr>
                <tr><td>Analysis</td><td>{$row['analysis']}</td></tr>
                <tr><td>Category</td><td>{$row['category']}</td></tr>
                <tr><td>Transaction Number</td><td>{$row['unique_id']}</td></tr>
                <tr><td>Reservation Date</td><td>{$reservationTime}</td></tr>
            </table>
            <p>The reservation request has been received and is pending approval.</p>
        </body>
        </html>";
        $mail->isHTML(true);
        $mail->send();

        // ✅ Email to Client
        $mail->clearAddresses();
        $mail->addAddress($row['email_address'], $row['full_name']); // Send to client
        $mail->Subject = "Reservation Confirmation (Pending Approval) - " . $row['unique_id'];
        $mail->Body = "
        <html>
        <body>
            <h2>Reservation Confirmation (Pending Approval)</h2>
            <p>Hello <strong>{$row['full_name']}</strong>,</p>
            <p>We have received your reservation request, and it is currently under <strong>pending approval</strong>. You will be notified via email once your reservation has been reviewed and approved.</p>
            <table border='1' cellpadding='10' cellspacing='0' style='border-collapse: collapse;'>
                <tr>
                    <th>Transaction Number</th>
                    <td>{$row['unique_id']}</td>
                </tr>
                <tr>
                    <th>Laboratory</th>
                    <td>{$labName}</td>
                </tr>
                <tr>
                    <th>Full Name</th>
                    <td>{$row['full_name']}</td>
                </tr>
                <tr>
                    <th>{$sampleLabel}</th>
                    <td>{$categoryName}</td>
                </tr>
                <tr>
                    <th>{$analysisLabel}</th>
                    <td>{$analysisValue}</td>
                </tr>
                <tr>
                    <th>Reservation Date</th>
                    <td>{$reservationTime}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><strong style='color: orange;'>PENDING APPROVAL</strong></td>
                </tr>
            </table>
            
            <h3 style='color: #2c3e50; font-family: Arial, sans-serif;'>Check Your Reservation Status</h3>
            <p style='font-size: 14px; color: #34495e; font-family: Arial, sans-serif;'>You can monitor the status of your reservation at any time by visiting our reservation portal:</p>
            <p style='font-size: 14px; font-family: Arial, sans-serif;'>
                Click <a href='{$reservationCheckLink}' target='_blank' style='color: #2980b9; text-decoration: none; font-weight: bold;'>here</a> to check your reservation status
            </p>
            <p style='font-size: 14px; color: #34495e; font-family: Arial, sans-serif;'>Once on the portal, please enter your transaction number: <strong style='color: #2c3e50;'>{$row['unique_id']}</strong> to view the details.</p>
        </body>
        </html>";
        $mail->send();

        echo json_encode(["status" => "success", "message" => "Reservation confirmed and email sent!", "redirect_url" => "reservation_details.php?unique_id={$row['unique_id']}"]);
    } catch (Exception $e) {
        echo json_encode(["status" => "error", "message" => "Email sending failed: " . $mail->ErrorInfo]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Error updating reservation."]);
}

$conn->close();
exit();
?>
