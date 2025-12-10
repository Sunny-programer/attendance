<?php
session_start();

if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch notifications for students
$sql = "
    SELECT title, message, audience, created_by_role, created_at
    FROM notifications
    WHERE audience = 'ALL' OR audience = 'STUDENTS'
    ORDER BY created_at DESC
";
$result = $conn->query($sql);

$notifications = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $notifications[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Notifications</title>
<link rel="stylesheet" href="dashboard.css">
<style>
body {
    background: #f4f5fb;
    font-family: Arial, sans-serif;
}
.wrap {
    max-width: 900px;
    margin: 25px auto;
    background: #fff;
    padding: 18px 22px;
    border-radius: 14px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.1);
}
h2 {
    color: #1f3c88;
    margin-bottom: 12px;
}
.note-card {
    border-bottom: 1px solid #e0e4f0;
    padding: 10px 6px;
}
.note-title {
    font-weight: bold;
    font-size: 15px;
    color: #1f3c88;
}
.note-meta {
    font-size: 11px;
    color: #777;
    margin: 2px 0;
}
.note-message {
    font-size: 13px;
    margin-top: 4px;
    white-space: pre-wrap;
}
.badge {
    display: inline-block;
    font-size: 11px;
    padding: 2px 6px;
    border-radius: 10px;
    margin-left: 6px;
}
.badge-all { background: #e8f5e9; color: #2e7d32; }
.badge-stu { background: #e3f2fd; color: #1565c0; }
.back-link {
    text-decoration: none;
    font-size: 13px;
    color: #1f3c88;
}
</style>
</head>
<body>

<div class="wrap">

    <a href="studentdashboard.php" class="back-link">← Back</a>
    <h2>Notifications</h2>

    <?php if (empty($notifications)): ?>
        <p style="font-size:13px; color:#555;">No notifications available.</p>
    <?php else: ?>
        <?php foreach ($notifications as $n): ?>
            <div class="note-card">
                <div class="note-title">
                    <?php echo htmlspecialchars($n['title']); ?>
                    <?php if ($n['audience'] === 'ALL'): ?>
                        <span class="badge badge-all">All</span>
                    <?php else: ?>
                        <span class="badge badge-stu">Students</span>
                    <?php endif; ?>
                </div>
                <div class="note-meta">
                    Posted by <?php echo htmlspecialchars($n['created_by_role']); ?> · 
                    <?php echo htmlspecialchars($n['created_at']); ?>
                </div>
                <div class="note-message"><?php echo nl2br(htmlspecialchars($n['message'])); ?></div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

</div>

</body>
</html>
