<?php
$sname = "localhost";
$uname = "root";
$password = "";
$db_name = "LoginFormDb";

$conn = new mysqli($sname, $uname, $password, $db_name);

if ($conn->connect_error) {
    echo "Test failed: Connection failed - " . $conn->connect_error . "\n";
} else {
    echo "Test passed: Connection successful\n";

    $table_name = "users";
    $query_check_table = "SHOW TABLES LIKE '$table_name'";
    $result_check_table = $conn->query($query_check_table);
    if ($result_check_table->num_rows > 0) {
        echo "Table '$table_name' exists\n";
    } else {
        echo "Test failed: Table '$table_name' does not exist\n";
    }

    $insert_query = "INSERT INTO users (username, password) VALUES ('test_user', 'password123')";
    if ($conn->query($insert_query) === TRUE) {
        echo "Test passed: Record inserted successfully\n";

        $select_query = "SELECT * FROM users WHERE username = 'test_user'";
        $result_select = $conn->query($select_query);
        if ($result_select && $result_select->num_rows > 0) {
            $row = $result_select->fetch_assoc();
            if (!empty($row)) {
                echo "Test passed: Retrieved record - Username: " . $row['Username'] . ", Password: " . $row['Password'] . "\n";
            } else {
                echo "Test failed: Retrieved record is empty\n";
            }
        } else {
            echo "Test failed: No records found for username 'test_user'\n";
        }

        $delete_query = "DELETE FROM users WHERE username = 'test_user'";
        $conn->query($delete_query);
    } else {
        echo "Test failed: Error inserting record - " . $conn->error . "\n";
    }
}

$conn->close();