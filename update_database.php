<?php
include 'php/config.php';

// Function to check if a column exists
function columnExists($conn, $table, $column) {
    $result = mysqli_query($conn, "SHOW COLUMNS FROM $table LIKE '$column'");
    return mysqli_num_rows($result) > 0;
}

// Add new columns to users table
$new_columns = [
    'theme' => "ALTER TABLE users ADD COLUMN theme VARCHAR(10) DEFAULT 'light'",
    'notification_preferences' => "ALTER TABLE users ADD COLUMN notification_preferences TEXT DEFAULT '[]'",
    'bio' => "ALTER TABLE users ADD COLUMN bio TEXT",
    'updated_at' => "ALTER TABLE users ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
];

echo "<h2>Updating Database Structure</h2>";

foreach ($new_columns as $column => $sql) {
    if (!columnExists($conn, 'users', $column)) {
        echo "Adding $column column...<br>";
        if (!mysqli_query($conn, $sql)) {
            echo "Error adding $column column: " . mysqli_error($conn) . "<br>";
        } else {
            echo "Successfully added $column column<br>";
        }
    } else {
        echo "$column column already exists<br>";
    }
}

// Create uploads directory if it doesn't exist
$uploads_dir = 'uploads/profiles';
if (!file_exists($uploads_dir)) {
    if (mkdir($uploads_dir, 0777, true)) {
        echo "Created uploads directory successfully<br>";
    } else {
        echo "Failed to create uploads directory<br>";
    }
} else {
    echo "Uploads directory already exists<br>";
}

// Update existing users to have default values
$update_sql = "UPDATE users SET 
    theme = COALESCE(theme, 'light'),
    notification_preferences = COALESCE(notification_preferences, '[]'),
    bio = COALESCE(bio, ''),
    updated_at = CURRENT_TIMESTAMP
    WHERE theme IS NULL 
    OR notification_preferences IS NULL 
    OR bio IS NULL 
    OR updated_at IS NULL";

if (mysqli_query($conn, $update_sql)) {
    echo "Updated existing user records with default values<br>";
} else {
    echo "Error updating user records: " . mysqli_error($conn) . "<br>";
}

echo "<br>Database update completed. You can now refresh your page.";
?> 