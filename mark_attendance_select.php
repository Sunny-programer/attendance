<?php
session_start();
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

// Fixed class info (single-section system)
$CLASS_BRANCH  = 'CSE-DS';
$CLASS_YEAR    = '2nd Year';
$CLASS_SECTION = 'A';

// DB connection for subject dropdown
require 'config.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Class - Attendance</title>
<link rel="stylesheet" href="dashboard.css">
<style>
.container {
    max-width: 450px;
    background: #fff;
    margin: 40px auto;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.container h2 {
    text-align: center;
    margin-bottom: 15px;
    color: #1f3c88;
}
label {
    font-size: 13px;
    color: #555;
}
input, select, button {
    width: 100%;
    padding: 10px;
    margin: 6px 0;
    border-radius: 8px;
    border: 1px solid #ccc;
}
input[readonly] {
    background:#f3f5ff;
    cursor: not-allowed;
}
button {
    background: #1f3c88;
    color: #fff;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
}
button:hover {
    background: #163473;
}
</style>
</head>
<body>

<div class="container">
    <h2>Mark Attendance</h2>

    <form action="mark_attendance.php" method="POST">

        <label>Class:</label>
        <input type="text" value="<?php echo $CLASS_BRANCH . ' / ' . $CLASS_YEAR . ' / ' . $CLASS_SECTION; ?>" readonly>

        <!-- hidden values to send to next page -->
        <input type="hidden" name="branch" value="<?php echo $CLASS_BRANCH; ?>">
        <input type="hidden" name="year" value="<?php echo $CLASS_YEAR; ?>">
        <input type="hidden" name="section" value="<?php echo $CLASS_SECTION; ?>">

        <label>Date:</label>
        <input type="date" name="date" value="<?php echo date('Y-m-d'); ?>" required>

        <label>Period:</label>
        <select name="period" required>
            <option value="">Select Period</option>
            <option value="1">Period 1</option>
            <option value="2">Period 2</option>
            <option value="3">Period 3</option>
            <option value="4">Period 4</option>
            <option value="5">Period 5</option>
            <option value="6">Period 6</option>
        </select>

        <label>Subject:</label>
        <select name="subject" required>
            <option value="">Select Subject</option>
            <?php
            $result = $conn->query("SELECT subject_name FROM subjects ORDER BY subject_name");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . $row['subject_name'] . "'>" . $row['subject_name'] . "</option>";
                }
            }
            ?>
        </select>

        <button type="submit">Next →</button>
    </form>
</div>

</body>
</html>


