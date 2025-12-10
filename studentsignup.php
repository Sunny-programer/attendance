<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Sign Up</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">🎓</div>
            <h2>Student Sign Up</h2>

            <form action="studentsignupprocess.php" method="POST" enctype="multipart/form-data">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>


                <button type="submit">Sign Up</button>
            </form>

            <p class="signup-text">
                Already have an account? <a href="studentlogin.php">Login Here</a>
            </p>
        </div>
    </div>
</body>
</html>

