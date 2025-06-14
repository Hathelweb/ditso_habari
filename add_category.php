<?php
include 'php/config.php';

echo "<h2>Adding Category Column to Articles Table</h2>";

if ($conn) {
    echo "Database connection successful!<br><br>";
    
    // Add category column
    $sql = "ALTER TABLE articles 
            ADD COLUMN category ENUM('academic', 'entertainment', 'sports', 'technology') 
            NOT NULL DEFAULT 'academic' 
            AFTER content";
    
    if (mysqli_query($conn, $sql)) {
        echo "Category column added successfully!<br>";
        
        // Show updated table structure
        $result = mysqli_query($conn, "DESCRIBE articles");
        if ($result) {
            echo "<h3>Updated Articles Table Columns:</h3>";
            echo "<table border='1' cellpadding='5'>";
            echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr>";
            
            while ($row = mysqli_fetch_assoc($result)) {
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
        }
    } else {
        echo "Error adding category column: " . mysqli_error($conn);
    }
    
    mysqli_close($conn);
} else {
    echo "Connection failed: " . mysqli_connect_error();
}
?> 