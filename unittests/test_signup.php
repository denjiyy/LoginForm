<?php

$_SESSION = [];
$_POST = [];
$_SERVER['REQUEST_METHOD'] = 'POST';

function mock_mysqli_connect() {
    return true;
}

function mock_mysqli_prepare($conn, $query) {
    global $mockStmt;
    return $mockStmt;
}

function mock_mysqli_stmt_bind_param($stmt, $types, &$username, &$password = null) {
}

function mock_mysqli_stmt_execute($stmt) {
    global $mockStmtExecuteSuccess;
    return $mockStmtExecuteSuccess;
}

function mock_mysqli_stmt_store_result($stmt) {
}

function mock_mysqli_stmt_num_rows($stmt) {
    global $mockStmtNumRows;
    return $mockStmtNumRows;
}

function mock_password_hash($password, $algo) {
    return 'hashed_' . $password;
}

function mock_header($location) {
    echo "Header called: $location\n";
}

function mock_exit() {
    throw new Exception('Exit called');
}

$mockStmt = new stdClass();

global $mockStmtExecuteSuccess, $mockStmtNumRows;

$mockStmtExecuteSuccess = true;
$mockStmtNumRows = 0;
$_SESSION['captcha_text'] = 'correctCaptcha';
$_POST['captcha_challenge'] = 'correctCaptcha';
$_POST['username'] = 'newuser';
$_POST['password'] = 'ValidPassword1!';
test_signup_scenario("Header called: Location: home.php\nExit called");

$_SESSION['captcha_text'] = 'correctCaptcha';
$_POST['captcha_challenge'] = 'wrongCaptcha';
$_POST['username'] = 'newuser';
$_POST['password'] = 'ValidPassword1!';
test_signup_scenario("Header called: Location: index.php?error=Incorrect CAPTCHA\nExit called");

$_SESSION['captcha_text'] = 'correctCaptcha';
$_POST['captcha_challenge'] = 'correctCaptcha';
$_POST['username'] = 'inv';
$_POST['password'] = 'ValidPassword1!';
test_signup_scenario("Header called: Location: index.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.\nExit called");

$_SESSION['captcha_text'] = 'correctCaptcha';
$_POST['captcha_challenge'] = 'correctCaptcha';
$_POST['username'] = 'validuser';
$_POST['password'] = 'invalid';
test_signup_scenario("Header called: Location: index.php?error=Invalid password. It must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.\nExit called");

$mockStmtNumRows = 1;
$_SESSION['captcha_text'] = 'correctCaptcha';
$_POST['captcha_challenge'] = 'correctCaptcha';
$_POST['username'] = 'existinguser';
$_POST['password'] = 'ValidPassword1!';
test_signup_scenario("Header called: Location: index.php?error=Username already exists!\nExit called");

function test_signup_scenario($expectedOutput) {
    ob_start();
    try {

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['captcha_text'] = 'correctCaptcha';

        $conn = mock_mysqli_connect();
        if (!$conn) {
            die('Connection failed: ' . mock_mysqli_connect_error());
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (!isset($_POST['captcha_challenge']) || $_POST['captcha_challenge'] !== $_SESSION['captcha_text']) {
                mock_header('Location: index.php?error=Incorrect CAPTCHA');
                mock_exit();
            }

            if (!preg_match('/^[a-zA-Z0-9_-]{5,}$/', $username)) {
                mock_header('Location: index.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.');
                mock_exit();
            }
        
            if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
                mock_header('Location: index.php?error=Invalid password. It must be at least 8 characters long and include at least one uppercase letter, one lowercase letter, one digit, and one special character.');
                mock_exit();
            }

            $hashed_password = mock_password_hash($password, PASSWORD_DEFAULT);

            $stmt = mock_mysqli_prepare($conn, "SELECT Username FROM users WHERE Username = ?");
            mock_mysqli_stmt_bind_param($stmt, "s", $username);
            mock_mysqli_stmt_execute($stmt);
            mock_mysqli_stmt_store_result($stmt);

            if (mock_mysqli_stmt_num_rows($stmt) > 0) {
                mock_header("Location: index.php?error=Username already exists!");
                mock_exit();
            } else {
                $stmt = mock_mysqli_prepare($conn, "INSERT INTO users (Username, Password) VALUES (?, ?)");
                mock_mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_password);
                mock_mysqli_stmt_execute($stmt);

                $_SESSION["username"] = $username;
                unset($_SESSION['captcha']);

                mock_header("Location: home.php");
                mock_exit();
            }
        } else {
            mock_header("Location: index.php");
            mock_exit();
        }
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }

    $output = ob_get_clean();

    if (strpos($output, $expectedOutput) === false) {
        echo "Test failed: Expected '$expectedOutput' not found in output. Actual output: $output\n";
        exit(1);
    } else {
        echo "Test passed: '$expectedOutput' found in output.\n";
    }
}

echo "All tests passed.\n";