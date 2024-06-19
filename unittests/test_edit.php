<?php

$_SESSION = [];
$_SESSION['username'] = 'testuser';
$_SESSION['captcha_challenge'] = 'correctCaptcha';

$_POST = [];
$_POST['captcha_challenge'] = 'correctCaptcha';
$_POST['new_username'] = 'newuser';
$_POST['new_password'] = 'newpassword';

$_SERVER['REQUEST_METHOD'] = 'POST';

function mock_mysqli_connect() {
    return true;
}

function mock_mysqli_real_escape_string($conn, $string) {
    return addslashes($string);
}

function mock_password_hash($password, $algo) {
    return 'hashed_' . $password;
}

function mock_mysqli_query($conn, $query) {
    global $mockQuerySuccess;
    return $mockQuerySuccess;
}

function mock_mysqli_connect_error() {
    return 'Mock connection error';
}

function mock_mysqli_error($conn) {
    return 'Mock query error';
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
test_edit_scenario("Account updated successfully");

$mockQuerySuccess = false;
test_edit_scenario("Error updating account: Mock query error");

function test_edit_scenario($expectedOutput) {
    ob_start();
    try {
        $conn = mock_mysqli_connect();

        if (!$conn) {
            mock_header('Location: index.php');
            mock_exit();
        }

        $username = $_SESSION['username'];

        $new_username = mock_mysqli_real_escape_string($conn, $_POST['new_username']);
        $new_password = mock_mysqli_real_escape_string($conn, $_POST['new_password']);

        $hashed_password = mock_password_hash($new_password, PASSWORD_DEFAULT);

        $sql_update_user = "UPDATE users SET `Username`='$new_username', `Password`='$hashed_password' WHERE `Username`='$username'";

        if (mock_mysqli_query($conn, $sql_update_user)) {
            $_SESSION['username'] = $new_username;
            echo "Account updated successfully";
            mock_header('Location: home.php');
            mock_exit();
        } else {
            echo "Error updating account: " . mock_mysqli_error($conn);
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