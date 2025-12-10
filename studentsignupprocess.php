<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'config.php';

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO students (full_name, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $username, $password);

    if ($stmt->execute()) {
        // store session including student ID
        $_SESSION['student_id'] = $stmt->insert_id;
        $_SESSION['student_name'] = $name;
        $_SESSION['student_username'] = $username;

        header("Location: studentdetails.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>


