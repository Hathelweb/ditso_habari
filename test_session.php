<?php
session_start();
include 'php/config.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing session...<br>";

if (isset($_SESSION['user_id'])) {
    echo "User is logged in!<br>";
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    
    // Get user details
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT * FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($user = mysqli_fetch_assoc($result)) {
        echo "User details:<br>";
        echo "Name: " . $user['first_name'] . " " . $user['last_name'] . "<br>";
        echo "Email: " . $user['email'] . "<br>";
    } else {
        echo "Could not fetch user details!<br>";
    }
} else {
    echo "No user is logged in!<br>";
    echo "Please <a href='registration.php'>login</a> to test the like functionality.<br>";
}

// Test like functionality
if (isset($_SESSION['user_id'])) {
    echo "<br>Testing like functionality...<br>";
    
    // Get an article ID
    $sql = "SELECT id FROM articles LIMIT 1";
    $result = mysqli_query($conn, $sql);
    
    if ($article = mysqli_fetch_assoc($result)) {
        $article_id = $article['id'];
        echo "Found article ID: " . $article_id . "<br>";
        
        // Check if user has liked this article
        $sql = "SELECT id FROM likes WHERE article_id = ? AND user_id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "ii", $article_id, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) > 0) {
            echo "User has already liked this article.<br>";
        } else {
            echo "User has not liked this article yet.<br>";
        }
    } else {
        echo "No articles found in the database!<br>";
    }
}
?> 