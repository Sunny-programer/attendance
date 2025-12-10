<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ensure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get form data
$roll = $_POST['roll_number'];
$branch = $_POST['branch'];
$year = $_POST['year'];
$mobile = $_POST['mobile'];
$parent_mobile = $_POST['parent_mobile'];
$parent_name = $_POST['parent_name'];
$section = $_POST['section'];
$gender = $_POST['gender'];
$address = $_POST['address'];

// Handle profile pic upload
$profile_pic = "";
if (!empty($_FILES['profile_pic']['name'])) {
    $file_name = time() . "_" . basename($_FILES['profile_pic']['name']);
    $file_tmp = $_FILES['profile_pic']['tmp_name'];

    // Optional: validate file type
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    if (in_array($ext, $allowed)) {
        if (!is_dir("uploads")) mkdir("uploads", 0777, true);
        move_uploaded_file($file_tmp, "uploads/" . $file_name);
        $profile_pic = $file_name;
    }
}

// Insert using prepared statement
$stmt = $conn->prepare("INSERT INTO student_details 
    (student_id, roll_number, branch, year, mobile_number, parent_mobile, parent_name, section, gender, address, profile_pic) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("issssssssss", $student_id, $roll, $branch, $year, $mobile, $parent_mobile, $parent_name, $section, $gender, $address, $profile_pic);

if ($stmt->execute()) {
    header("Location: studentdashboard.php");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
