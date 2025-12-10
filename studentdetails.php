<?php
session_start();

// If not logged in → redirect
if (!isset($_SESSION['student_id'])) {
    header("Location: studentlogin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <link rel="stylesheet" href="studentdetails.css">
</head>

<body>

<div class="details-wrapper">
    <div class="details-card">

        <h2>Complete Your Details</h2>
        <p class="subtitle">Please provide accurate information.</p>

        <form action="studentdetailsprocess.php" method="POST" enctype="multipart/form-data">

            <input type="text" name="roll_number" placeholder="Roll Number" required>

            <input type="text" name="branch" placeholder="Branch" required>

            <input type="text" name="year" placeholder="Year" required>

            <input type="text" name="mobile" placeholder="Student Mobile" required>

            <input type="text" name="parent_mobile" placeholder="Parent Mobile" required>

            <input type="text" name="parent_name" placeholder="Parent Name">

            <input type="text" name="section" placeholder="Section (Optional)">

            <select name="gender">
                <option value="">Select Gender (optional)</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>

            <textarea name="address" placeholder="Address"></textarea>

            <div class="file-row">
                <label>Profile Picture (Optional)</label>
                <input type="file" name="profile_pic" accept="image/*">
            </div>

            <button type="submit">Save Details</button>
        </form>

    </div>
</div>

</body>
</html>
