<?php
require 'config.php';


$id = $_GET['id'];

$student = $conn->query("SELECT * FROM students WHERE id='$id'")->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Student</title>

<style>
    body { font-family: Arial; background: #eef1f7; }
    .box {
        width: 400px;
        margin: 30px auto;
        background: white;
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.15);
    }
    input {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
    }
    button {
        width: 100%;
        padding: 12px;
        background: #1d3557;
        border: none;
        color: white;
        border-radius: 6px;
    }
</style>
</head>

<body>

<div class="box">
    <h2>Edit Student</h2>

    <form action="edit_student_process.php" method="POST">
        <input type="hidden" name="id" value="<?= $student['id'] ?>">

        <input type="text" name="full_name" value="<?= $student['full_name'] ?>" required>
        <input type="text" name="roll_number" value="<?= $student['roll_number'] ?>" required>
        <input type="text" name="branch" value="<?= $student['branch'] ?>" required>
        <input type="text" name="year" value="<?= $student['year'] ?>" required>
        <input type="email" name="email" value="<?= $student['email'] ?>" required>

        <button type="submit">Update Student</button>
    </form>
</div>

</body>
</html>
