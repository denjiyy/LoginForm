<?php

$_SESSION = [];
function mock_session_start() {
    // Не прави нищо за session_start() в mock
}
function mock_session_unset() {
    $_SESSION = [];
}
function mock_session_destroy() {
    $_SESSION = [];
}
function mock_header($location) {
    echo "Header called: $location\n";
}
function mock_exit() {
    echo "Exit called\n";
}

test_logout_scenario();

function test_logout_scenario() {
    ob_start();
    try {
        mock_session_start();
        mock_session_unset();
        mock_session_destroy();

        mock_header("Location: index.php");
        mock_exit();
    } catch (Exception $e) {
        echo $e->getMessage() . "\n";
    }
    $output = ob_get_clean();

    if (strpos($output, "Header called: Location: index.php") === false) {
        echo "Test failed: Expected 'Header called: Location: index.php' not found in output.\n";
        exit(1);
    } else {
        echo "Test passed: 'Header called: Location: index.php' found in output.\n";
    }

    echo "All tests passed.\n";
}