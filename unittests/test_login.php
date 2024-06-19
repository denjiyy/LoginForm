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

function mock_mysqli_stmt_bind_param($stmt, $types, &$username) {
    // Параметризиране (няма значение за mock)
}

function mock_mysqli_stmt_execute($stmt) {
    global $mockStmtExecuteSuccess;
    return $mockStmtExecuteSuccess;
}

function mock_mysqli_stmt_get_result($stmt) {
    global $mockResult;
    return $mockResult;
}

function mock_mysqli_fetch_assoc($result) {
    global $mockUserData;
    return $mockUserData;
}

function mock_password_verify($password, $hashedPassword) {
    global $mockPasswordVerifySuccess;
    return $mockPasswordVerifySuccess;
}

function mock_header($location) {
    echo "Header called: $location\n";
}

function mock_exit() {
    throw new Exception('Exit called');
}

$mockStmt = new stdClass();
$mockResult = new class {
    public $num_rows = 1;

    public function fetch_assoc() {
        global $mockUserData;
        return $mockUserData;
    }
};

global $mockStmtExecuteSuccess, $mockUserData, $mockPasswordVerifySuccess;

$mockStmtExecuteSuccess = true;
$mockUserData = [
    'Password' => 'hashed_correctpassword'
];
$mockPasswordVerifySuccess = true;
$_POST['username'] = 'validuser';
$_POST['password'] = 'correctpassword';
test_login_scenario("Header called: Location: home.php\nExit called");

$mockPasswordVerifySuccess = false;
$_POST['username'] = 'validuser';
$_POST['password'] = 'wrongpassword';
test_login_scenario("Header called: Location: loginpage.php?error=Incorrect password\nExit called");

$mockResult->num_rows = 0;
$_POST['username'] = 'nonexistentuser';
$_POST['password'] = 'any_password';
test_login_scenario("Header called: Location: loginpage.php?error=Username not found\nExit called");

$_POST['username'] = 'inv';
$_POST['password'] = 'any_password';
test_login_scenario("Header called: Location: loginpage.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.\nExit called");

function test_login_scenario($expectedOutput) {
    ob_start();
    try {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $conn = mock_mysqli_connect();
        if (!$conn) {
            mock_header("Location: index.php");
            mock_exit();
        }

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $password = $_POST['password'];

            if (!preg_match('/^[a-zA-Z0-9_-]{5,}$/', $username)) {
                mock_header('Location: loginpage.php?error=Invalid username. It must be at least 5 characters long and can contain letters, numbers, underscores, and hyphens.');
                mock_exit();
            }

            $stmt = mock_mysqli_prepare($conn, "SELECT Id, Username, Password FROM users WHERE Username = ? AND IsDeleted = 0");
            mock_mysqli_stmt_bind_param($stmt, "s", $username);
            mock_mysqli_stmt_execute($stmt);
            $result = mock_mysqli_stmt_get_result($stmt);

            if ($result->num_rows == 1) {
                $row = mock_mysqli_fetch_assoc($result);
                $stored_password = $row['Password'];

                if (mock_password_verify($password, $stored_password)) {
                    $_SESSION["username"] = $username;

                    mock_header("Location: home.php");
                    mock_exit();
                } else {
                    mock_header('Location: loginpage.php?error=Incorrect password');
                    mock_exit();
                }
            } else {
                mock_header('Location: loginpage.php?error=Username not found');
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