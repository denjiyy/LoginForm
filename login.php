<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    include "db_connection.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if(!isset($_POST['captcha_challenge']) || $_POST['captcha_challenge'] !== $_SESSION['captcha_text']) {
            header('Location: loginpage.php?error=Incorrect CAPTCHA');
            exit();
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{5,}$/', $username)) {
            header('Location: loginpage.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.');
            exit();
        }

        $stmt = $conn->prepare("SELECT Id, Username, Password FROM users WHERE Username = ? AND IsDeleted = 0");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $stored_password = $row['Password'];

            if (password_verify($password, $stored_password)) {
                $_SESSION["username"] = $username;

                header("Location: home.php");
                exit();
            } else {
                header('Location: loginpage.php?error=Incorrect password');
                exit();
            }
        } else {
            header('Location: loginpage.php?error=Username not found');
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }