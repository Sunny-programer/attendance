<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: adminlogin.php");
    exit();
}

require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch students
$sql = "SELECT * FROM students";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Students</title>

<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
        background: #eef1f7;
    }

    .container {
        margin-left: 260px;
        padding: 20px;
    }

    h2 {
        color: #1d3557;
    }

    table {
        width: 100%;
        background: white;
        border-collapse: collapse;
        margin-top: 20px;
        border-radius: 8px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.2);
        overflow: hidden;
    }

    th, td {
        padding: 12px;
        text-align: center;
        border-bottom: 1px solid #ddd;
    }

    th {
        background: #1d3557;
        color: white;
    }

    tr:hover {
        background: #f1f1f1;
    }

    .btn-edit {
        padding: 6px 12px;
        background: #457b9d;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    .btn-delete {
        padding: 6px 12px;
        background: #e63946;
        color: white;
        border-radius: 5px;
        text-decoration: none;
    }

    .profile-img {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
    }
</style>

</head>

<body>

<div class="container">
    <h2>Manage Students</h2>

    <table>
        <tr>
            <th>ID</th>
            <th>Profile</th>
            <th>Name</th>
            <th>Roll Number</th>
            <th>Branch</th>
            <th>Year</th>
            <th>Email</th>
            <th>Actions</th>
        </tr>

        <?php
        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                // Check profile pic
                $pic = (!empty($row['profile_pic'])) ? "uploads/".$row['profile_pic'] : "uploads/default.png";

                echo "<tr>
                        <td>".$row['id']."</td>
                        <td><img src='$pic' class='profile-img'></td>
                        <td>".$row['full_name']."</td>
                        <td>".$row['roll_number']."</td>
                        <td>".$row['branch']."</td>
                        <td>".$row['year']."</td>
                        <td>".$row['email']."</td>
                        <td>
                            <a class='btn-edit' href='edit_student.php?id=".$row['id']."'>Edit</a> 
                            <a class='btn-delete' href='delete_student.php?id=".$row['id']."' 
                               onclick=\"return confirm('Are you sure want to delete this student?');\">
                               Delete
                            </a>
                        </td>
                    </tr>";
            }

        } else {
            echo "<tr><td colspan='8'>No students found</td></tr>";
        }
        ?>
    </table>

</div>

</body>
</html>
