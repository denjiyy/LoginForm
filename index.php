<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign-Up</title>
</head>
<body>
    <form action="signup.php" method="post">
        <h2><b>SIGN-UP</b></h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>
        <label><b>USERNAME</b></label><br>
        <input type="text" name="username" placeholder="USERNAME" required><br>
        <label><b>PASSWORD</b></label>
        <div>
            <input type="password" id="password" name="password" placeholder="PASSWORD" required>
            <button type="button" onclick="togglePasswordVisibility()">Show Password</button>
        </div><br>

        <label><b>CAPTCHA</b></label>
        <div>
            <img src="captcha.php" alt="CAPTCHA Image"><br>
            <input type="text" id="captcha" name="captcha_challenge" pattern="[A-Z]{6}" placeholder="ENTER CAPTCHA" required>
        </div><br>

        <button type="submit">Sign-Up</button>
    </form>
    <a href="loginpage.php">Already have an account? Log in now!</a>

    <script src="togglePasswordVisibility.js" defer></script>
</body>
</html>