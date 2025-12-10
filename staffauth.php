<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// DB connection
require 'config.php';
 // change if needed

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Only handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        echo "<script>alert('Please enter both username and password'); window.location.href='stafflogin.php';</script>";
        exit();
    }

    // Get staff by username
    $stmt = $conn->prepare("SELECT id, name, email, username, password FROM staff WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $row['password'])) {
            // ✅ Login success - store session
            $_SESSION['staff_id']   = $row['id'];
            $_SESSION['staff_name'] = $row['name'];
            $_SESSION['staff_user'] = $row['username'];

            // ✅ Go directly to dashboard
            header("Location: staffdashboard.php");
            exit();
        } else {
            echo "<script>alert('Invalid password'); window.location.href='stafflogin.php';</script>";
        }
    } else {
        echo "<script>alert('No staff found with that username'); window.location.href='stafflogin.php';</script>";
    }

    $stmt->close();
}

$conn->close();
?>

