<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f6fa;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 50px auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 30px;
            color: #2c3e50;
        }

        .card-container {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: 0.3s;
            cursor: pointer;
        }

        .card:hover {
            transform: translateY(-5px);
            background: #e8f4ff;
        }

        .card h3 {
            color: #2980b9;
            margin-bottom: 12px;
        }

        .back-btn {
            display: block;
            width: 150px;
            margin: 20px auto;
            padding: 10px;
            text-align: center;
            background: #2980b9;
            color: white;
            text-decoration: none;
            border-radius: 8px;
        }

        .back-btn:hover {
            background: #1e6ea8;
        }
    </style>
</head>

<body>

<div class="container">
    <h1>Manage Students</h1>

    <div class="card-container">

        <div class="card" onclick="window.location.href='studentlist.php'">
            <h3>View Students</h3>
            <p>See list of all students</p>
        </div>

        <div class="card" onclick="window.location.href='studentedit.php'">
            <h3>Edit Student</h3>
            <p>Update student details</p>
        </div>

        <div class="card" onclick="window.location.href='studentdelete.php'">
            <h3>Delete Student</h3>
            <p>Remove student permanently</p>
        </div>

    </div>

    <a class="back-btn" href="admindashboard.php">⬅ Back to Dashboard</a>

</div>

</body>
</html>
