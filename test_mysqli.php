<?php
// Show all PHP information
phpinfo();

// Test mysqli connection
echo "<hr><h2>Testing mysqli connection:</h2>";
if (extension_loaded('mysqli')) {
    echo "mysqli extension is loaded.<br>";
    
    $host = "localhost";
    $username = "root";
    $password = "";
    $database = "new_ditso_habari";
    
    $conn = mysqli_connect($host, $username, $password, $database);
    
    if ($conn) {
        echo "Database connection successful!<br>";
        mysqli_close($conn);
    } else {
        echo "Connection failed: " . mysqli_connect_error() . "<br>";
    }
} else {
    echo "mysqli extension is NOT loaded.<br>";
    echo "Please enable mysqli in your php.ini file.<br>";
    echo "Current php.ini location: " . php_ini_loaded_file() . "<br>";
}
?> 