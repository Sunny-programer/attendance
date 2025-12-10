<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">🎓</div>
            <h2>Student Login</h2>

            <form action="studentauth.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>

            <p class="signup-text">
                Don't have an account? <a href="studentsignup.php">Create New Account</a>
            </p>
        </div>
    </div>
</body>
</html>

