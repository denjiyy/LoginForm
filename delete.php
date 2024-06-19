<?php
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    $conn = include "db_connection.php";

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = $_SESSION['username'];

    unset($_SESSION['username']);

    $sql_soft_delete_user = "UPDATE users SET IsDeleted = 1 WHERE username = '$username'";

    if (mysqli_query($conn, $sql_soft_delete_user)) {
        echo "Account deleted successfully";
        header("Location: index.php");
        exit;
    } else {
        echo "Error deleting account: " . mysqli_error($conn);
    }

    mysqli_close($conn);
