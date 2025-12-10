<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require 'config.php';

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch student by username
    $stmt = $conn->prepare("SELECT id, full_name, password FROM students WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($student_id, $full_name, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows == 0) {
        echo "<script>alert('Username not found'); window.location='studentlogin.php';</script>";
        exit();
    }

    // Verify password
    if (!password_verify($password, $hashed_password)) {
        echo "<script>alert('Invalid password'); window.location='studentlogin.php';</script>";
        exit();
    }

    // Store session
    $_SESSION['student_id'] = $student_id;
    $_SESSION['student_username'] = $username;
    $_SESSION['student_full_name'] = $full_name; // optional, for dashboard display

    // Redirect to dashboard
    header("Location: studentdashboard.php");
    exit();
}
?>




