<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Sign Up</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-icon">🎓</div>
            <h2>Staff Sign Up</h2>

            <form action="staffsignupprocess.php" method="POST">
                <!-- Full Name -->
                <input 
                    type="text" 
                    name="name" 
                    placeholder="Full Name" 
                    autocomplete="name" 
                    required
                >

                <!-- Email -->
                <input 
                    type="email" 
                    name="email" 
                    placeholder="Email Address" 
                    autocomplete="email" 
                    required
                >

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
                    autocomplete="new-password" 
                    required
                >

                <button type="submit">Sign Up</button>
            </form>

            <p class="signup-text">
                Already have an account? <a href="stafflogin.php">Login Here</a>
            </p>
        </div>
    </div>
</body>
</html>
