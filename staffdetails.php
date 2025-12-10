<?php
session_start();

// Ensure staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header("Location: stafflogin.php");
    exit();
}

$staff_name = $_SESSION['staff_name'] ?? "Staff";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Details</title>
    <link rel="stylesheet" href="login.css"> <!-- you can reuse same CSS -->
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">👨‍🏫</div>
            <h2>Complete Your Profile</h2>
            <p>Welcome, <?php echo htmlspecialchars($staff_name); ?>. Please fill in your details.</p>

            <form action="staffdetailsprocess.php" method="POST" enctype="multipart/form-data">
                <!-- Staff ID (college given ID) -->
                <input 
                    type="text" 
                    name="staff_code" 
                    placeholder="Staff ID (College ID)" 
                    required
                >

                <!-- Department -->
                <input 
                    type="text" 
                    name="department" 
                    placeholder="Department (e.g., CSE, ECE)" 
                    required
                >

                <!-- Mobile Number -->
                <input 
                    type="text" 
                    name="mobile_number" 
                    placeholder="Mobile Number" 
                    required
                >

                <!-- Subject -->
                <input 
                    type="text" 
                    name="subject" 
                    placeholder="Subject (e.g., Mathematics, Physics)" 
                    required
                >

                <!-- Profile Picture -->
                <label style="margin-top:10px; display:block; text-align:left;">
                    Profile Picture:
                </label>
                <input 
                    type="file" 
                    name="profile_pic" 
                    accept="image/*"
                >

                <button type="submit" style="margin-top:15px;">Save Details</button>
            </form>
        </div>
    </div>
</body>
</html>
