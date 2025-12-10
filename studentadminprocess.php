<?php
require 'config.php';


$id = $_POST['id'];
$name = $_POST['full_name'];
$roll = $_POST['roll_number'];
$branch = $_POST['branch'];
$year = $_POST['year'];
$email = $_POST['email'];

$sql = "UPDATE students SET 
    full_name='$name',
    roll_number='$roll',
    branch='$branch',
    year='$year',
    email='$email'
    WHERE id='$id'";

$conn->query($sql);

header("Location: students_list.php");
exit();
?>
