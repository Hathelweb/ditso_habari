<?php
session_start();
include 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get current user data
$sql = "SELECT user_name FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = trim($_POST['new_username']);
    $current_password = $_POST['current_password'];

    // Validate input
    if (empty($new_username) || empty($current_password)) {
        $error = "All fields are required";
    } elseif (strlen($new_username) < 3 || strlen($new_username) > 50) {
        $error = "Username must be between 3 and 50 characters";
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $new_username)) {
        $error = "Username can only contain letters, numbers, and underscores";
    } else {
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($result);

        if (password_verify($current_password, $user_data['password'])) {
            // Check if new username is already taken
            $sql = "SELECT id FROM users WHERE user_name = ? AND id != ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $new_username, $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $error = "Username is already taken";
            } else {
                // Update username
                $sql = "UPDATE users SET user_name = ? WHERE id = ?";
                $stmt = mysqli_prepare($conn, $sql);
                mysqli_stmt_bind_param($stmt, "si", $new_username, $user_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    $success = "Username updated successfully";
                    $user['user_name'] = $new_username;
                } else {
                    $error = "Error updating username";
                }
            }
        } else {
            $error = "Current password is incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Username - Ditso Habari</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/ditso.css">
    <style>
        .change-username-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: var(--white);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-md);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
        }

        .form-control {
            border: 1px solid var(--light-gray);
            border-radius: 8px;
            padding: 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 0.2rem rgba(255, 29, 88, 0.25);
        }

        .btn-primary {
            background: var(--gradient-primary);
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .current-username {
            background: var(--light-gray);
            padding: 0.8rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }

        .back-link {
            color: var(--primary-pink);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .back-link:hover {
            color: var(--secondary-pink);
        }
    </style>
</head>
<body>
    <?php include 'base.php'; renderHeader(); ?>

    <div class="container">
        <div class="change-username-container">
            <a href="Ditso.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
            
            <h2 class="mb-4">Change Username</h2>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <div class="current-username">
                <i class="fas fa-user"></i>
                Current Username: <?php echo isset($user['user_name']) ? htmlspecialchars($user['user_name']) : 'Not available'; ?>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="new_username">New Username</label>
                    <input type="text" 
                           class="form-control" 
                           id="new_username" 
                           name="new_username" 
                           required 
                           minlength="3" 
                           maxlength="50"
                           pattern="[a-zA-Z0-9_]+"
                           title="Username can only contain letters, numbers, and underscores">
                    <small class="form-text text-muted">
                        Username must be 3-50 characters long and can only contain letters, numbers, and underscores.
                    </small>
                </div>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" 
                           class="form-control" 
                           id="current_password" 
                           name="current_password" 
                           required>
                    <small class="form-text text-muted">
                        Please enter your current password to confirm the change.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Username
                </button>
            </form>
        </div>
    </div>

    <?php renderFooter(); ?>
</body>
</html> 