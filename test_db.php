<?php
include 'php/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...<br>";

if ($conn) {
    echo "Database connection successful!<br>";
    
    // Test likes table
    $result = mysqli_query($conn, "SHOW TABLES LIKE 'likes'");
    if (mysqli_num_rows($result) > 0) {
        echo "Likes table exists!<br>";
        
        // Show table structure
        $result = mysqli_query($conn, "DESCRIBE likes");
        echo "Likes table structure:<br>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "Column: " . $row['Field'] . " - Type: " . $row['Type'] . "<br>";
        }
    } else {
        echo "Likes table does not exist!<br>";
    }
} else {
    echo "Database connection failed!<br>";
    echo "Error: " . mysqli_connect_error();
}
?> 