<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Check login
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

// Get form data
$staff_code    = trim($_POST['staff_code']);
$department    = trim($_POST['department']);
$mobile_number = trim($_POST['mobile_number']);
$subject       = trim($_POST['subject']);

// Basic validation
if ($staff_code === "" || $department === "" || $mobile_number === "" || $subject === "") {
    echo "<script>alert('All fields are required!'); window.location.href='staffdetails.php';</script>";
    exit();
}

// Handle profile picture upload
$profile_pic = null;

if (!empty($_FILES['profile_pic']['name'])) {
    $file_name = time() . "_" . basename($_FILES['profile_pic']['name']);
    $file_tmp  = $_FILES['profile_pic']['tmp_name'];

    // Create uploads folder if not exists
    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    if (move_uploaded_file($file_tmp, "uploads/" . $file_name)) {
        $profile_pic = $file_name;
    }
}

// Insert into staff_details
$stmt = $conn->prepare(
    "INSERT INTO staff_details 
    (staff_id, staff_code, department, mobile_number, subject, profile_pic)
     VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "isssss",
    $staff_id,
    $staff_code,
    $department,
    $mobile_number,
    $subject,
    $profile_pic
);

if ($stmt->execute()) {
    echo "<script>alert('Profile details saved successfully!'); 
          window.location.href='staffdashboard.php';</script>";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
