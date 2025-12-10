<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    // Fetch admin record
    $sql = "SELECT * FROM admin WHERE username='$username'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $row = $result->fetch_assoc();

        // Verify password
        if(password_verify($password, $row['password'])){
            // Login successful: store session info
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name'];
            $_SESSION['admin_username'] = $row['username'];

            // Redirect to admin dashboard
            header("Location: admindashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "Username not found.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>
