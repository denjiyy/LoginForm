<?php

$_SESSION = [];

function mock_mysqli_connect() {
    return true;
}

function mock_mysqli_real_escape_string($conn, $string) {
    return addslashes($string);
}

function mock_mysqli_query($conn, $query) {
    global $mockQuerySuccess;
    return $mockQuerySuccess;
}

function mock_mysqli_error($conn) {
    return 'Mock query error';
}

function mock_mysqli_connect_error() {
    return 'Mock connection error';
}

function mock_mysqli_close($conn) {
    // Не прави нищо за Mock тестовете
}

function mock_header($location) {
    echo "Header called: $location\n";
}

function mock_exit() {
    throw new Exception('Exit called');
}

global $mockQuerySuccess;

$mockQuerySuccess = true;
test_delete_scenario("Account deleted successfully");

$mockQuerySuccess = false;
test_delete_scenario("Error deleting account: Mock query error");

function test_delete_scenario($expectedOutput) {
    ob_start();
    try {
        $conn = mock_mysqli_connect();

        if (!$conn) {
            mock_header('Location: index.php');
            mock_exit();
        }

        $_SESSION['username'] = "testuser";

        $username = $_SESSION['username'];

        unset($_SESSION['username']);

        $sql_soft_delete_user = "UPDATE users SET IsDeleted = 1 WHERE username = '$username'";

        if (mock_mysqli_query($conn, $sql_soft_delete_user)) {
            echo "Account deleted successfully";
            mock_header('Location: index.php');
            mock_exit();
        } else {
            echo "Error deleting account: " . mock_mysqli_error($conn);
        }

        mock_mysqli_close($conn);

    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    $output = ob_get_clean();

    if (strpos($output, $expectedOutput) === false) {
        echo "Test failed: Expected '$expectedOutput' not found in output.\n";
        exit(1);
    } else {
        echo "Test passed: '$expectedOutput' found in output.\n";
    }
}

echo "All tests passed.\n";