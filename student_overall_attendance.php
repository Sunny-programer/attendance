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

// Fetch student name + roll
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

// Fetch overall attendance
$sql = "
    SELECT 
        SUM(status='P') AS present_count,
        COUNT(*) AS total_count
    FROM attendance
    WHERE student_id = ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$att = $stmt->get_result()->fetch_assoc();
$stmt->close();

$conn->close();

$present = $att['present_count'] ?? 0;
$total   = $att['total_count'] ?? 0;
$percent = ($total > 0) ? round(($present / $total) * 100) : 0;

// Badge color
$badgeClass = "green";
if ($percent < 60) $badgeClass = "red";
else if ($percent < 75) $badgeClass = "yellow";
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Overall Attendance</title>
<link rel="stylesheet" href="dashboard.css">
<style>
.container {
    max-width: 600px;
    background:#fff;
    margin: 30px auto;
    padding: 20px;
    border-radius: 14px;
    box-shadow:0 4px 18px rgba(0,0,0,0.1);
}
h2 {
    color:#1f3c88;
    margin-bottom:10px;
}
.badge {
    padding:6px 12px;
    border-radius:8px;
    font-weight:bold;
    font-size:15px;
    color:#000;
}
.green { background:#e8f5e9; color:#2e7d32; }
.yellow { background:#fff8e1; color:#ef6c00; }
.red { background:#ffebee; color:#c62828; }
.back-link {
    text-decoration:none;
    color:#1f3c88;
    font-size:14px;
}
</style>
</head>
<body>

<div class="container">
    <a class="back-link" href="studentdashboard.php">← Back</a>

    <h2>Overall Attendance</h2>

    <p><strong>Name:</strong> <?= htmlspecialchars($stu['full_name']) ?><br>
       <strong>Roll No:</strong> <?= htmlspecialchars($stu['roll_number']) ?></p>

    <p><strong>Total Classes:</strong> <?= $total ?></p>
    <p><strong>Attended:</strong> <?= $present ?></p>

    <p><strong>Attendance Percentage:</strong>
        <span class="badge <?= $badgeClass ?>"><?= $percent ?>%</span>
    </p>
</div>

</body>
</html>
