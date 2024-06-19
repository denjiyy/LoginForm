<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Account</title>
</head>
<body>
    <form action="edit.php" method="post">
        <h2><b>EDIT ACCOUNT</b></h2>
        <?php if (isset($_GET['error'])) { ?>
            <p class="error"><?php echo htmlspecialchars($_GET['error']); ?></p>
        <?php } ?>
        <label><b>USERNAME</b></label><br>
        <input type="text" id="new_username" name="new_username" placeholder="NEW USERNAME" required><br>
        <label><b>PASSWORD</b></label>
        <div>
            <input type="password" id="new_password" name="new_password" placeholder="NEW PASSWORD" required>
            <button type="button" onclick="togglePasswordVisibilityEdit()">Show Password</button>
        </div><br>

        <label><b>CAPTCHA</b></label>
        <div>
            <img src="captcha.php" alt="CAPTCHA Image"><br>
            <input type="text" id="captcha" name="captcha_challenge" pattern="[A-Z]{6}" placeholder="ENTER CAPTCHA" required>
        </div><br>

        <button type="submit">Update</button>
    </form>
    <a href="home.php">Cancel</a>

    <script src="togglePasswordVisibilityEdit.js" defer></script>
</body>
</html>