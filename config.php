<?php
$host = "sql303.infinityfree.com";
$user = "if0_40608065";
$pass = "srujanayadav209";   // same as shown in panel
$db   = "if0_40608065_gokul";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>

?>
