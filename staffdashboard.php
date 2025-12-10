<?php
session_start();

// If not logged in, send to login
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

$staff_id = $_SESSION['staff_id'];

// DB connection
require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get staff basic + details
$sql = $conn->prepare("
    SELECT s.name, s.email,
           sd.staff_code, sd.department, sd.mobile_number, sd.subject, sd.profile_pic
    FROM staff s
    LEFT JOIN staff_details sd ON sd.staff_id = s.id
    WHERE s.id = ?
");
$sql->bind_param("i", $staff_id);
$sql->execute();
$result = $sql->get_result();
$staff = $result->fetch_assoc();

$staff_name    = $staff['name'] ?? 'Staff';
$email         = $staff['email'] ?? '-';
$staff_code    = $staff['staff_code'] ?? 'Not updated';
$department    = $staff['department'] ?? 'Not updated';
$mobile_number = $staff['mobile_number'] ?? 'Not updated';
$subject       = $staff['subject'] ?? 'Not updated';
$profile_pic   = $staff['profile_pic'] ?? null;

$sql->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
</head>
<body>

    <!-- TOP BAR – TEACHER DETAIL -->
    <header class="top-bar">
        <div class="top-left">
            <div class="avatar">
                <?php if ($profile_pic): ?>
                    <img src="uploads/<?php echo htmlspecialchars($profile_pic); ?>" alt="Profile">
                <?php else: ?>
                    <span><?php echo strtoupper(substr($staff_name, 0, 1)); ?></span>
                <?php endif; ?>
            </div>
            <div class="teacher-info">
                <h2><?php echo htmlspecialchars($staff_name); ?></h2>
                <p><strong>Staff ID:</strong> <?php echo htmlspecialchars($staff_code); ?></p>
                <p><strong>Department:</strong> <?php echo htmlspecialchars($department); ?></p>
                <p><strong>Subject:</strong> <?php echo htmlspecialchars($subject); ?></p>
            </div>
        </div>

        <div class="top-right">
            <p><?php echo htmlspecialchars($email); ?></p>
            <p><?php echo htmlspecialchars($mobile_number); ?></p>
            <a href="staff_logout.php" class="btn-logout">Logout</a>

        </div>
    </header>

    <!-- MAIN DASHBOARD CONTENT -->
    <main class="dashboard-container">

        <!-- 2. Attendance Marking -->
        <a href="mark_attendance_select.php" class="card">
    <h3>Attendance Marking</h3>
    <p>Mark daily attendance for your classes.</p>
</a>


        <!-- 3. Time Table -->
        <a href="timetable.php" class="card">
            <h3>Time Table</h3>
            <p>View your teaching schedule.</p>
        </a>

        <!-- 4. Attendance Summary -->
        <a href="attendance_summary.php" class="card">
            <h3>Attendance Summary</h3>
            <p>Class-wise attendance percentages.</p>
        </a>

        <!-- 5. Notifications -->
        <a href="staff_notifications.php" class="card">
            <h3>Notifications</h3>
            <p>Post and manage announcements to students.</p>
        </a>

        <!-- 6. Student Individual Attendance Analysis -->
        <a href="student_attendance_analysis.php" class="card">
            <h3>Student Attendance Analysis</h3>
            <p>Check detailed attendance of a student.</p>
        </a>

        <!-- 7. Message to Parents if Absent -->
        <a href="absent_messages.php" class="card">
            <h3>Message Parents (Absent)</h3>
            <p>Send alerts to parents of absent students.</p>
        </a>

    </main>

</body>
</html>
