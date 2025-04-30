<?php include('includes/header.php');

// Database Connection
$servername = "localhost"; 
$username = "root"; 
$password = ""; 
$dbname = "scheduling_form";


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Check if unique_id is provided in the URL
if (!isset($_GET['unique_id'])) {
    echo "<p>No reservation found.</p>";
    exit();
}

$uniqueId = $conn->real_escape_string($_GET['unique_id']);

// Fetch submission details based on unique_id
$sql = "SELECT * FROM submissions WHERE unique_id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $uniqueId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<p>No reservation found.</p>";
    exit();
}

$row = $result->fetch_assoc();
$stmt->close();

// Define lab names
$labNames = [
    1 => 'Tank Truck Calibration',
    2 => 'Chemical Testing Laboratory',
    3 => 'Microbiological Testing Laboratory',
    4 => 'Shelf Life Laboratory'
];

$labName = isset($labNames[$row['lab_id']]) ? $labNames[$row['lab_id']] : 'Unknown Lab';

?>
<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-12 col-md-7 col-lg-5">
            <div style="border-radius: 15px;" class="card card-info">
                <div class="card-header text-center">
                    <h1 style="text-align: center !important"><?php echo htmlspecialchars($labName); ?></h1>
                </div>
                <div class="card-body text-left">
                    <table class="table table-bordered">
                        <tr>
                            <th colspan="2" class="text-center">
                                <h5>Reservation Details <?php echo ($row['lab_id'] == 2 || $row['lab_id'] == 3) ? 'for ' . htmlspecialchars($row['request_type']) : ''; ?></h5>
                            </th>
                        </tr>
                        <tr>
                            <th>Full Name</th>
                            <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                        </tr>
                        <tr>
                            <th>
                                <?php 
                                if ($row['lab_id'] == 1) {
                                    echo 'Renewal or New';
                                } elseif ($row['lab_id'] == 2 || $row['lab_id'] == 3 || $row['lab_id'] == 4) {
                                    echo 'Type of Sample';
                                }
                                ?>
                            </th>
                            <td><?php echo htmlspecialchars($row['category']); ?></td>
                        </tr>
                        <?php if ($row['lab_id'] != 4): ?>
                        <tr>
                            <th>
                                <?php echo ($row['lab_id'] == 1) ? 'Type of Request' : 'No. of Samples'; ?>
                            </th>
                            <td><?php echo htmlspecialchars($row['lab_id'] == 1 ? $row['request_type'] : $row['quantity']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if ($row['lab_id'] == 2 || $row['lab_id'] == 3 || $row['lab_id'] == 4): ?>
                        <tr>
                            <th>Type of Analysis</th>
                            <td><?php echo htmlspecialchars($row['analysis']); ?></td>
                        </tr>
                        <?php endif; ?>
                        <tr>
                            <th>Reservation Date</th>
                            <td><?php echo date('F j, Y \a\t g:i A', strtotime($row['submission_date_selected'] . ' 08:00:00')); ?></td>
                        </tr>
                        <tr>
                            <th>Reservation Status</th>
                            <td>
                                <?php 
                                $statusColors = [0 => 'red', 1 => 'orange', 2 => 'green'];
                                $statusLabels = [0 => 'REJECTED', 1 => 'PENDING', 2 => 'APPROVED'];
                                echo '<strong style="color: ' . $statusColors[$row['status']] . ';">' . $statusLabels[$row['status']] . '</strong>';
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th>Transaction Number</th>
                            <td class="text-center">
                                <h4><strong><?php echo htmlspecialchars($row['unique_id']); ?></strong></h4>
                            </td>
                        </tr>
                    </table>
                    <div class="card-body text-center">
                        <a class="btn btn-info" style="color: white;" href="index.php">BACK TO HOME</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
