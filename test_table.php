<?php
include 'php/config.php';
echo "<b>Script started.</b><br>";

// Test database connection and show table structure
echo "<h2>Articles Table Structure:</h2>";

if ($conn) {
    echo "Database connection successful!<br><br>";
    
    // Show table structure
    $result = $conn->query("SHOW CREATE TABLE articles");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "<pre>";
        echo htmlspecialchars($row['Create Table']);
        echo "</pre>";
    } else {
        echo "Error getting table structure: " . $conn->error;
    }
    
    // Show column information
    echo "<h3>Column Details:</h3>";
    $result = $conn->query("SHOW COLUMNS FROM articles");
    if ($result) {
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['Field'] . "</td>";
            echo "<td>" . $row['Type'] . "</td>";
            echo "<td>" . $row['Null'] . "</td>";
            echo "<td>" . $row['Key'] . "</td>";
            echo "<td>" . $row['Default'] . "</td>";
            echo "<td>" . $row['Extra'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Error getting column information: " . $conn->error;
    }
    
    $conn->close();
    echo "<br><b>Script finished.</b>";
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?> 