<?php
    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: index.php");
        exit;
    }

    $conn = include "db_connection.php";

    $captcha = $_SESSION['captcha_challenge'];

    if(!isset($_POST['captcha_challenge']) || $_POST['captcha_challenge'] !== $_SESSION['captcha_text']) {
        header('Location: editpage.php?error=Incorrect CAPTCHA');
        exit();
    }

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = $_SESSION['username'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $new_username = mysqli_real_escape_string($conn, $_POST['new_username']);
        $new_password = mysqli_real_escape_string($conn, $_POST['new_password']);

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update_user = "UPDATE users SET `Username`='$new_username', `Password`='$hashed_password' WHERE `Username`='$username'";

        if (mysqli_query($conn, $sql_update_user)) {
            $_SESSION['username'] = $new_username;
            echo "Account updated successfully";
            header("Location: home.php");
            exit;
        } else {
            echo "Error updating account: " . mysqli_error($conn);
        }
    }

    mysqli_close($conn);