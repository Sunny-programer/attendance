<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get selected month
$selected_month = isset($_GET['month']) ? $_GET['month'] : date('Y-m');
list($year, $month) = explode('-', $selected_month);

// Fetch Student Basic Info
$sql = "
    SELECT s.full_name, sd.roll_number
    FROM students s
    JOIN student_details sd ON sd.student_id = s.id
    WHERE s.id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stu = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Fetch per-subject attendance
$subjectData = [];
$sql = "
    SELECT subject,
           SUM(status='P') AS present_count,
           COUNT(*) AS total_count
    FROM attendance
    WHERE student_id = ?
      AND MONTH(date) = ?
      AND YEAR(date) = ?
    GROUP BY subject
    ORDER BY subject
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $student_id, $month, $year);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $subjectData[] = $row;
}

$stmt->close();

// Total attendance
$total_present = 0;
$total_classes = 0;
foreach ($subjectData as $d) {
    $total_present += $d['present_count'];
    $total_classes += $d['total_count'];
}

$overall_percent = ($total_classes > 0) ? round(($total_present/$total_classes)*100) : 0;

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>My Attendance</title>
<link rel="stylesheet" href="dashboard.css">
<style>
.container {
    max-width: 900px;
    margin: 25px auto;
    background: #fff;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 4px 18px rgba(0,0,0,0.1);
}
h2 {
    color:#1f3c88;
}
table {
    width: 100%;
    border-collapse: collapse;
    margin-top:10px;
}
th, td {
    border:1px solid #d0d7e2;
    padding:8px;
    font-size:14px;
    text-align:center;
}
th {
    background:#e4ebfb;
    color:#1f3c88;
}
.badge {
    padding:4px 8px;
    border-radius:6px;
    font-size:12px;
    font-weight:bold;
}
.badge-red { background:#ffebee; color:#c62828; }
.badge-yellow { background:#fff8e1; color:#ef6c00; }
.badge-green { background:#e8f5e9; color:#2e7d32; }
</style>
</head>
<body>

<div class="container">
    <a href="studentdashboard.php" style="font-size:13px; color:#1f3c88; text-decoration:none;">← Back</a>

    <h2>My Attendance</h2>
    <p><strong>Name:</strong> <?= htmlspecialchars($stu['full_name']); ?> |
       <strong>Roll:</strong> <?= htmlspecialchars($stu['roll_number']); ?></p>

    <!-- Month Filter -->
    <form method="GET" style="margin-bottom:12px;">
        <input type="month" name="month" value="<?= $selected_month ?>" required>
        <button type="submit">View</button>
    </form>

    <!-- Overall -->
    <p><strong>Overall Attendance:</strong>
    <?php
        $class = 'badge-green';
        if ($overall_percent < 60) $class = 'badge-red';
        else if ($overall_percent < 75) $class = 'badge-yellow';
        echo "<span class='badge $class'>{$overall_percent}%</span>";
    ?>
    </p>

    <!-- Subject Table -->
    <table>
        <thead>
            <tr>
                <th>Subject</th>
                <th>Present</th>
                <th>Total</th>
                <th>Percentage</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($subjectData)): ?>
                <tr><td colspan="4">No attendance recorded</td></tr>
            <?php else: ?>
                <?php foreach ($subjectData as $row): 
                    $perc = ($row['total_count'] > 0) ? round(($row['present_count']/$row['total_count'])*100) : 0;
                    $badge = $perc < 60 ? 'badge-red' : ($perc < 75 ? 'badge-yellow' : 'badge-green');
                ?>
                <tr>
                    <td><?= htmlspecialchars($row['subject']); ?></td>
                    <td><?= $row['present_count']; ?></td>
                    <td><?= $row['total_count']; ?></td>
                    <td><span class="badge <?= $badge ?>"><?= $perc ?>%</span></td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

</div>

</body>
</html>
