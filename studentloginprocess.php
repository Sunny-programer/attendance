<?php
session_start();
error_reporting(E_ALL);
ini_set("display_errors", 1);

require 'config.php';

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// 1. Make sure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// 2. Get form data
$roll = $_POST['roll_number'];
$branch = $_POST['branch'];
$year = $_POST['year'];
$mobile = $_POST['mobile'];
$parent_mobile = $_POST['parent_mobile'];
$parent_name = $_POST['parent_name'];
$section = $_POST['section'];
$gender = $_POST['gender'];
$address = $_POST['address'];

// 3. Handle profile pic upload
$profile_pic = "";
if (!empty($_FILES['profile_pic']['name'])) {
    $file_name = time() . "_" . $_FILES['profile_pic']['name'];
    $file_tmp = $_FILES['profile_pic']['tmp_name'];

    if (!is_dir("uploads")) {
        mkdir("uploads", 0777, true);
    }

    move_uploaded_file($file_tmp, "uploads/" . $file_name);
    $profile_pic = $file_name;
}

// 4. Insert details
$sql = $conn->prepare("INSERT INTO student_details 
    (student_id, roll_number, branch, year, mobile_number, parent_mobile, parent_name, section, gender, address, profile_pic)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
$sql->bind_param("issssssssss", $student_id, $roll, $branch, $year, $mobile, $parent_mobile, $parent_name, $section, $gender, $address, $profile_pic);

if ($sql->execute()) {
    header("Location: studentdashboard.php");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
