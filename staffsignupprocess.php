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

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Basic validation
    if ($name === "" || $email === "" || $username === "" || $password === "") {
        echo "<script>alert('All fields are required!'); window.location.href='staffsignup.php';</script>";
        exit();
    }

    // Check if email or username exists
    $check = $conn->prepare("SELECT id FROM staff WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('Email or Username already exists!'); window.location.href='staffsignup.php';</script>";
        exit();
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert staff record
    $stmt = $conn->prepare("INSERT INTO staff (name, email, username, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $username, $hashed_password);

    if ($stmt->execute()) {
        // ✅ Get the new staff id
        $new_staff_id = $conn->insert_id;

        // ✅ Set session so details page knows who is logged in
        $_SESSION['staff_id']   = $new_staff_id;
        $_SESSION['staff_name'] = $name;
        $_SESSION['staff_user'] = $username;

        // ✅ Redirect to staff details page
        echo "<script>
                alert('Signup successful! Please complete your profile details.');
                window.location.href='staffdetails.php';
              </script>";
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $check->close();
}

$conn->close();  // 👈 semicolon added
?>


