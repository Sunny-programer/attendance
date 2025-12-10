<?php
// In future: session_start(); or redirect logic can be added here
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Attendance Management</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>Select Your Role</h1>

        <div class="role-container">

            <a href="adminlogin.php" class="role-card admin-card">
                <div class="role-icon">👨‍💼</div>
                <h2 class="role-title">Admin</h2>
                <p class="role-description">Manage system settings, users, and overall attendance operations</p>
            </a>

            <a href="stafflogin.php" class="role-card staff-card">
                <div class="role-icon">👨‍🏫</div>
                <h2 class="role-title">Staff</h2>
                <p class="role-description">Mark attendance, view class records, and manage student data</p>
            </a>

            <a href="studentlogin.php" class="role-card student-card">
                <div class="role-icon">🎓</div>
                <h2 class="role-title">Student</h2>
                <p class="role-description">View your attendance and track your progress</p>
            </a>

        </div>
    </div>

    <footer>
        &copy; 2024 College Attendance Management System
    </footer>
</body>
</html>
