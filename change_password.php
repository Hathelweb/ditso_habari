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

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    // Validate input
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = "All fields are required";
    } elseif (strlen($new_password) < 8) {
        $error = "New password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "New passwords do not match";
    } else {
        // Verify current password
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($result);

        if (password_verify($current_password, $user_data['password'])) {
            // Hash new password
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            
            // Update password
            $sql = "UPDATE users SET password = ? WHERE id = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Password updated successfully";
            } else {
                $error = "Error updating password";
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
    <title>Change Password - Ditso Habari</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/ditso.css">
    <style>
        .change-password-container {
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

        .password-requirements {
            background: var(--light-gray);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .password-requirements li {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0.5rem;
            color: var(--dark-gray);
        }

        .password-requirements li i {
            color: var(--primary-pink);
        }

        .password-strength-meter {
            height: 5px;
            background: var(--light-gray);
            border-radius: 3px;
            margin-top: 0.5rem;
            overflow: hidden;
        }

        .password-strength-meter div {
            height: 100%;
            width: 0;
            transition: all 0.3s ease;
        }

        .strength-weak { background-color: #dc3545; width: 25%; }
        .strength-medium { background-color: #ffc107; width: 50%; }
        .strength-good { background-color: #28a745; width: 75%; }
        .strength-strong { background-color: #20c997; width: 100%; }

        .password-strength-text {
            font-size: 0.875rem;
            margin-top: 0.25rem;
            color: var(--dark-gray);
        }

        .password-input-group {
            position: relative;
        }

        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--dark-gray);
            cursor: pointer;
            padding: 5px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--primary-pink);
        }

        .password-input-group .form-control {
            padding-right: 40px;
        }
    </style>
</head>
<body>
    <?php include 'base.php'; renderHeader(); ?>

    <div class="container">
        <div class="change-password-container">
            <a href="Ditso.php" class="back-link">
                <i class="fas fa-arrow-left"></i>
                Back to Home
            </a>
            
            <h2 class="mb-4">Change Password</h2>

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

            <div class="password-requirements">
                <h5>Password Requirements:</h5>
                <ul>
                    <li><i class="fas fa-check-circle"></i> At least 8 characters long</li>
                    <li><i class="fas fa-check-circle"></i> Include uppercase and lowercase letters</li>
                    <li><i class="fas fa-check-circle"></i> Include numbers</li>
                    <li><i class="fas fa-check-circle"></i> Include special characters</li>
                </ul>
            </div>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-input-group">
                        <input type="password" 
                               class="form-control" 
                               id="current_password" 
                               name="current_password" 
                               required>
                        <button type="button" class="password-toggle" onclick="togglePassword('current_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-input-group">
                        <input type="password" 
                               class="form-control" 
                               id="new_password" 
                               name="new_password" 
                               required 
                               minlength="8"
                               onkeyup="checkPasswordStrength(this.value)">
                        <button type="button" class="password-toggle" onclick="togglePassword('new_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength-meter">
                        <div id="strength-meter"></div>
                    </div>
                    <div class="password-strength-text" id="strength-text"></div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-input-group">
                        <input type="password" 
                               class="form-control" 
                               id="confirm_password" 
                               name="confirm_password" 
                               required 
                               minlength="8">
                        <button type="button" class="password-toggle" onclick="togglePassword('confirm_password')">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Update Password
                </button>
            </form>
        </div>
    </div>

    <script>
    function togglePassword(inputId) {
        const input = document.getElementById(inputId);
        const icon = input.nextElementSibling.querySelector('i');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function checkPasswordStrength(password) {
        const meter = document.getElementById('strength-meter');
        const text = document.getElementById('strength-text');
        
        // Reset classes
        meter.className = '';
        
        // Calculate strength
        let strength = 0;
        let feedback = [];

        if (password.length >= 8) {
            strength += 1;
        } else {
            feedback.push('At least 8 characters');
        }

        if (/[A-Z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('uppercase letter');
        }

        if (/[a-z]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('lowercase letter');
        }

        if (/[0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('number');
        }

        if (/[^A-Za-z0-9]/.test(password)) {
            strength += 1;
        } else {
            feedback.push('special character');
        }

        // Update meter and text
        switch(strength) {
            case 0:
            case 1:
                meter.classList.add('strength-weak');
                text.textContent = 'Very Weak';
                text.style.color = '#dc3545';
                break;
            case 2:
                meter.classList.add('strength-medium');
                text.textContent = 'Weak';
                text.style.color = '#ffc107';
                break;
            case 3:
                meter.classList.add('strength-good');
                text.textContent = 'Good';
                text.style.color = '#28a745';
                break;
            case 4:
                meter.classList.add('strength-strong');
                text.textContent = 'Strong';
                text.style.color = '#20c997';
                break;
            case 5:
                meter.classList.add('strength-strong');
                text.textContent = 'Very Strong';
                text.style.color = '#20c997';
                break;
        }

        // Show feedback if password is not strong enough
        if (strength < 5) {
            text.textContent += ' - Add ' + feedback.join(', ');
        }
    }
    </script>

    <?php renderFooter(); ?>
</body>
</html> 