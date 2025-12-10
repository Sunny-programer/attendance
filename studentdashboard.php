<?php
session_start();
require 'config.php';


if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student details + profile
$sql = "SELECT s.full_name, s.username, d.* 
        FROM students s 
        LEFT JOIN student_details d ON s.id = d.student_id
        WHERE s.id='$student_id'";
$result = $conn->query($sql);
$student = $result->fetch_assoc();

$profile_pic = (!empty($student['profile_pic']))
    ? "uploads/" . $student['profile_pic']
    : "uploads/default.png";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: #f0f4f8;
        }

        .header {
            background: linear-gradient(90deg, #4c67f5, #6e8bfa);
            padding: 25px;
            text-align: center;
            color: white;
        }

        .profile-img {
            width: 90px;
            height: 90px;
            border-radius: 50%;
            border: 3px solid white;
            margin-bottom: 10px;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            grid-gap: 20px;
            padding: 20px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            cursor: pointer;
            transition: 0.3s;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
        }

        .card h3 {
            margin-bottom: 10px;
            color: #344767;
        }

        .logout {
            text-align: center;
            margin: 20px 0;
        }

        .logout a {
            background: #ff4b5c;
            padding: 12px 25px;
            color: white;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout a:hover {
            background: #d63a4a;
        }
    </style>
</head>

<body>

<!-- Header/Profile -->
<div class="header">
    <img src="<?php echo $profile_pic; ?>" class="profile-img">
    <h2><?php echo $student['full_name']; ?></h2>
    <p><?php echo $student['roll_number']; ?> | <?php echo $student['branch']; ?> | Year <?php echo $student['year']; ?></p>
</div>

<!-- Cards Section -->
<div class="card-container">

    <div class="card" onclick="window.location='student_attendence.php'">
        <h3>📚  Attendance</h3>
        <p>View attendance </p>
    </div>

    <div class="card" onclick="window.location='student_overall_attendance.php'">
        <h3>📊 Overall Attendance</h3>
        <p>Your complete attendance report</p>
    </div>

    <div class="card" onclick="window.location='student_notifications.php'">
        <h3>🔔 Notifications</h3>
        <p>Latest updates from teachers & admin</p>
    </div>

    <div class="card" onclick="window.location='timetable.php'">
        <h3>📅 Time Table</h3>
        <p>View weekly class schedule</p>
    </div>

</div>

<!-- Logout Button -->
<div class="logout">
    <a href="student_logout.php" class="btn-logout">Logout</a>

</div>

</body>
</html>
