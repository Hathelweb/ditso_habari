<?php
echo "<h2>PHP Configuration Check</h2>";

// Check PHP version
echo "PHP Version: " . phpversion() . "<br>";

// Check if mysqli is loaded
echo "mysqli extension loaded: " . (extension_loaded('mysqli') ? 'Yes' : 'No') . "<br>";

// Check loaded extensions
echo "<h3>Loaded Extensions:</h3>";
echo "<pre>";
print_r(get_loaded_extensions());
echo "</pre>";

// Check php.ini location
echo "php.ini location: " . php_ini_loaded_file() . "<br>";

// Check mysqli configuration
if (extension_loaded('mysqli')) {
    echo "<h3>mysqli Configuration:</h3>";
    echo "<pre>";
    print_r(mysqli_get_client_info());
    echo "</pre>";
}

// Instructions for enabling mysqli
echo "<h3>To enable mysqli:</h3>";
echo "1. Open your php.ini file<br>";
echo "2. Find the line ';extension=mysqli'<br>";
echo "3. Remove the semicolon to uncomment it: 'extension=mysqli'<br>";
echo "4. Save the file and restart your web server<br>";
?> 