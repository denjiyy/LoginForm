<?php
    session_start();

    include "db_connection.php";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if(!isset($_POST['captcha_challenge']) || $_POST['captcha_challenge'] !== $_SESSION['captcha_text']) {
            header('Location: index.php?error=Incorrect CAPTCHA');
            exit();
        }

        if (!preg_match('/^[a-zA-Z0-9_-]{5,}$/', $username)) {
            header('Location: index.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.');
            exit();
        }
    
        if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            header('Location: index.php?error=Invalid password. It must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.');
            exit();
        }

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        if ($conn->connect_error) {
            die('Connection failed: ' . $conn->connect_error);
        }

        $stmt = $conn->prepare("SELECT Username FROM users WHERE Username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $conn->close();
            header("Location: index.php?error=Username already exists!");
            exit();
        } else {
            $stmt = $conn->prepare("INSERT INTO users (Username, Password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $hashed_password);
            $stmt->execute();
            $conn->close();

            $_SESSION["username"] = $username;
            unset($_SESSION['captcha']);

            header("Location: home.php");
            exit();
        }
    } else {
        header("Location: index.php");
        exit();
    }