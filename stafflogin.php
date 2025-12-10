<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">🎓</div>
            <h2>Staff Login</h2>

            <form action="staffauth.php" method="POST">
                <!-- Username -->
                <input 
                    type="text" 
                    name="username" 
                    placeholder="Username" 
                    autocomplete="username" 
                    required
                >

                <!-- Password -->
                <input 
                    type="password" 
                    name="password" 
                    placeholder="Password" 
                    autocomplete="current-password" 
                    required
                >

                <button type="submit">Login</button>
            </form>

            <p class="signup-text">
                Don't have an account? <a href="staffsignup.php">Create New Account</a>
            </p>
        </div>
    </div>
</body>
</html>
