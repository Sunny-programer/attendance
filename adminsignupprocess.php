<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Connect to database
require 'config.php';


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Get and escape form data
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $username = $conn->real_escape_string($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // hash password
    $contact = $conn->real_escape_string($_POST['contact_number']);

    // Handle profile picture upload
    $profile_pic = "";
    if(isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0){
        $file_name = time() . "_" . basename($_FILES['profile_pic']['name']);
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $upload_dir = "uploads/";

        // Create uploads folder if it doesn't exist
        if(!is_dir($upload_dir)){
            mkdir($upload_dir, 0777, true);
        }

        if(move_uploaded_file($file_tmp, $upload_dir.$file_name)){
            $profile_pic = $file_name;
        } else {
            echo "Error uploading profile picture.";
            exit();
        }
    }

    // Insert data into admin table
    $sql = "INSERT INTO admin (name, email, username, password, contact_number, profile_pic)
            VALUES ('$name', '$email', '$username', '$password', '$contact', '$profile_pic')";

    if($conn->query($sql) === TRUE){
        // Redirect to admin dashboard
        header("Location: admindashboard.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    echo "Invalid request.";
}

// Close connection
$conn->close();
?>

