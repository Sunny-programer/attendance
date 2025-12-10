<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign Up - College Attendance</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">👨‍💼</div>
            <h2>Admin Sign Up</h2>
            <form action="adminsignupprocess.php" method="POST" enctype="multipart/form-data">
    <input type="text" name="name" placeholder="Full Name" required>
    <input type="email" name="email" placeholder="Email Address" required>
    <input type="text" name="username" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="contact_number" placeholder="Contact Number">
    <input type="file" name="profile_pic" accept="image/*">
    <button type="submit">Sign Up</button>
</form>

            <p class="signup-text">
                Already have an account? <a href="adminlogin.php">Login Here</a>
            </p>
        </div>
    </div>
</body>
</html>
