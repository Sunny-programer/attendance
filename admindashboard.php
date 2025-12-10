<?php
session_start();

// Redirect to login if admin not logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

// DB connection
require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch admin details
$admin_id = $_SESSION['admin_id'];
$admin_query = $conn->query("SELECT * FROM admin WHERE id='$admin_id'");
$admin = $admin_query->fetch_assoc();

$profile_pic = $admin['profile_pic'] ? "uploads/".$admin['profile_pic'] : "uploads/default.png";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', sans-serif;
            background: #eef1f7;
        }

        /* ---------- Sidebar ---------- */
        .sidebar {
            width: 250px;
            height: 100vh;
            background: #1d3557;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 20px;
            color: white;
        }

        .sidebar h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: bold;
        }

        .sidebar a {
            display: block;
            padding: 12px 20px;
            color: white;
            text-decoration: none;
            margin: 8px 0;
            transition: 0.3s;
        }

        .sidebar a:hover {
            background: #457b9d;
            padding-left: 30px;
        }

        /* ---------- Top Bar ---------- */
        .topbar {
            margin-left: 250px;
            height: 70px;
            background: #ffffff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 25px;
            position: fixed;
            width: calc(100% - 250px);
            top: 0;
        }

        .admin-info {
            display: flex;
            align-items: center;
            font-size: 16px;
        }

        .admin-info img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 12px;
        }

        .logout-btn {
            padding: 8px 15px;
            background: #e63946;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .logout-btn:hover {
            background: #d62828;
        }

        /* ---------- Dashboard Content ---------- */
        .content {
            margin-left: 250px;
            margin-top: 90px;
            padding: 20px;
        }

        .cards {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .card {
            background: white;
            padding: 25px;
            width: 250px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            text-align: center;
        }

        .card h3 {
            margin-bottom: 10px;
            color: #1d3557;
        }

        .card p {
            font-size: 22px;
            font-weight: bold;
            color: #457b9d;
        }

    </style>
</head>

<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Admin Panel</h2>

    <a href="admindashboard.php">📊 Dashboard</a>
    <a href="managestudent.php">🎓 Manage Students</a>
    <a href="staff_list.php">👨‍🏫 Manage Staff</a>
    <a href="attendance_manage.php">📝 Attendance Management</a>
    <a href="timetable.php">📅 Time Tables</a>
    <a href="notifications.php">🔔 Notifications</a>
    <a href="sms_settings.php">📨 SMS to Parents</a>
</div>

<!-- Top Bar -->
<div class="topbar">
    <div class="admin-info">
        <img src="<?= $profile_pic ?>" alt="Profile">
        <span>Welcome, <b><?= $admin['name'] ?></b></span>
    </div>

    <a href="adminlogout.php" class="logout-btn">Logout</a>
</div>

<!-- Dashboard Content -->
<div class="content">

    <h2>Dashboard Overview</h2>

    <div class="cards">
        <div class="card">
            <h3>Total Students</h3>
            <p>120</p>
        </div>

        <div class="card">
            <h3>Total Staff</h3>
            <p>18</p>
        </div>

        <div class="card">
            <h3>Today Attendance</h3>
            <p>86%</p>
        </div>

        <div class="card">
            <h3>Pending Requests</h3>
            <p>5</p>
        </div>
    </div>

</div>

</body>
</html>
