<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <form action="login.php" method="post">
        <h2><b>LOGIN</b></h2>
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

        <button type="submit">Login</button>
    </form>
    <a href="index.php">Don't have an account? Sign-Up now!</a>

    <script src="togglePasswordVisibility.js" defer></script>
</body>
</html>