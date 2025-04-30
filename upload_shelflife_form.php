<?php
// Database Connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "scheduling_form";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
}

// Check if a file was uploaded
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["formFile"])) {
    $targetDir = "uploads/"; // Folder where files will be saved
    $fileName = basename($_FILES["formFile"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

    // Allowed file type (PDF only)
    if ($fileType == "pdf") {
        if (move_uploaded_file($_FILES["formFile"]["tmp_name"], $targetFilePath)) {
            // Store file in database
            $stmt = $conn->prepare("INSERT INTO uploads (file_name, file_path) VALUES (?, ?)");
            $stmt->bind_param("ss", $fileName, $targetFilePath);

            if ($stmt->execute()) {
                echo "<script>alert('File uploaded successfully!'); window.location.href='index.php';</script>";
            } else {
                echo "<script>alert('Database error!'); window.history.back();</script>";
            }

            $stmt->close();
        } else {
            echo "<script>alert('File upload failed!'); window.history.back();</script>";
        }
    } else {
        echo "<script>alert('Invalid file type! Only PDF is allowed.'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('No file uploaded!'); window.history.back();</script>";
}

$conn->close();
?>
