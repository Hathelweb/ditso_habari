<?php
session_start();
include 'php/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$message = '';
$error = '';

// Fetch user data
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);

    // Validate input
    if (empty($first_name) || empty($last_name) || empty($email)) {
        $error = "First name, last name, and email are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        // Check if email is already taken by another user
        $check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
        $check_stmt = mysqli_prepare($conn, $check_sql);
        mysqli_stmt_bind_param($check_stmt, "si", $email, $user_id);
        mysqli_stmt_execute($check_stmt);
        $check_result = mysqli_stmt_get_result($check_stmt);

        if (mysqli_num_rows($check_result) > 0) {
            $error = "Email is already taken by another user.";
        } else {
            // Handle profile image upload
            $profile_img = $user['profile_img']; // Keep existing image by default
            if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
                $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
                $max_size = 5 * 1024 * 1024; // 5MB

                if (!in_array($_FILES['profile_img']['type'], $allowed_types)) {
                    $error = "Invalid file type. Please upload a JPEG, PNG, or GIF image.";
                } elseif ($_FILES['profile_img']['size'] > $max_size) {
                    $error = "File size too large. Maximum size is 5MB.";
                } else {
                    $upload_dir = 'uploads/profiles/';
                    
                    // Create directory if it doesn't exist
                    if (!file_exists($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    $file_extension = strtolower(pathinfo($_FILES['profile_img']['name'], PATHINFO_EXTENSION));
                    $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;

                    if (move_uploaded_file($_FILES['profile_img']['tmp_name'], $upload_path)) {
                        // Delete old profile image if it exists and is not the default
                        if ($user['profile_img'] && $user['profile_img'] !== 'images/default-profile.png' && file_exists($user['profile_img'])) {
                            unlink($user['profile_img']);
                        }
                        $profile_img = $upload_path;
                    } else {
                        $error = "Failed to upload profile image.";
                    }
                }
            }

            if (empty($error)) {
                // Update user profile
                $update_sql = "UPDATE users SET 
                    first_name = ?, 
                    last_name = ?, 
                    email = ?, 
                    phone_number = ?,
                    profile_img = ?
                    WHERE id = ?";
                
                $update_stmt = mysqli_prepare($conn, $update_sql);
                mysqli_stmt_bind_param($update_stmt, "sssssi", 
                    $first_name, 
                    $last_name, 
                    $email, 
                    $phone_number,
                    $profile_img,
                    $user_id
                );

                if (mysqli_stmt_execute($update_stmt)) {
                    $message = "Profile updated successfully!";
                    // Refresh user data
                    $result = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
                    $user = mysqli_fetch_assoc($result);
                    
                    // Update session data
                    $_SESSION['user_data'] = $user;
                } else {
                    $error = "Failed to update profile. Please try again.";
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Ditso Habari</title>
    <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="css/ditso.css">
    <style>
        .profile-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .profile-image-container {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 1rem;
        }

        .profile-image {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--primary-pink);
        }

        .image-upload {
            position: absolute;
            bottom: 0;
            right: 0;
            background: var(--primary-pink);
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .image-upload:hover {
            transform: scale(1.1);
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-gray);
            margin-bottom: 0.5rem;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 0.8rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-pink);
            box-shadow: 0 0 0 0.2rem rgba(255, 29, 88, 0.25);
        }

        .btn-save {
            background: var(--gradient-primary);
            color: white;
            border: none;
            padding: 0.8rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }

        @media (max-width: 768px) {
            .profile-container {
                margin: 1rem;
                padding: 1.5rem;
            }

            .profile-image-container {
                width: 120px;
                height: 120px;
            }
        }
    </style>
</head>
<body>
    <?php include 'base.php'; renderHeader(); ?>

    <div class="container">
        <div class="profile-container">
            <div class="profile-header">
                <h2>User Profile</h2>
                <?php if ($message): ?>
                    <div class="alert alert-success"><?php echo $message; ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>
            </div>

            <form method="POST" enctype="multipart/form-data" id="profileForm">
                <div class="profile-image-container">
                    <img src="<?php echo htmlspecialchars($user['profile_img'] ?? 'images/default-profile.png'); ?>" 
                         alt="Profile" 
                         class="profile-image"
                         id="profilePreview"
                         onerror="this.src='images/default-profile.png'">
                    <label for="profile_img" class="image-upload">
                        <i class="fas fa-camera"></i>
                    </label>
                    <input type="file" 
                           id="profile_img" 
                           name="profile_img" 
                           accept="image/jpeg,image/png,image/gif" 
                           style="display: none;"
                           onchange="previewImage(this)">
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">First Name</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name']); ?>" 
                                   required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Last Name</label>
                            <input type="text" 
                                   class="form-control" 
                                   name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name']); ?>" 
                                   required>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" 
                           class="form-control" 
                           name="email" 
                           value="<?php echo htmlspecialchars($user['email']); ?>" 
                           required>
                </div>

                <div class="form-group">
                    <label class="form-label">Phone Number</label>
                    <input type="tel" 
                           class="form-control" 
                           name="phone_number" 
                           value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-save">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profilePreview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Add form submission handler
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission

        const formData = new FormData(this);
        const fileInput = document.getElementById('profile_img');
        
        // Validate file if selected
        if (fileInput.files.length > 0) {
            const file = fileInput.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPEG, PNG, or GIF)');
                return;
            }

            if (file.size > maxSize) {
                alert('File size too large. Maximum size is 5MB');
                return;
            }
        }

        // Show loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
        submitBtn.disabled = true;

        // Submit form using AJAX
        fetch(window.location.href, {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(html => {
            // Create a temporary div to parse the response
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;

            // Check for success message
            const successAlert = tempDiv.querySelector('.alert-success');
            const errorAlert = tempDiv.querySelector('.alert-danger');

            // Update alerts if they exist
            const alertContainer = document.querySelector('.profile-header');
            if (successAlert) {
                alertContainer.innerHTML = successAlert.outerHTML + alertContainer.innerHTML;
                // Update session data
                const newProfileImg = tempDiv.querySelector('#profilePreview').src;
                const newFirstName = tempDiv.querySelector('input[name="first_name"]').value;
                const newLastName = tempDiv.querySelector('input[name="last_name"]').value;
                
                // Update header profile section if it exists
                const headerProfileImg = document.querySelector('.user-profile-section .profile-img');
                const headerUserName = document.querySelector('.user-profile-section .user-name');
                if (headerProfileImg) headerProfileImg.src = newProfileImg;
                if (headerUserName) headerUserName.textContent = `${newFirstName} ${newLastName}`;
            }
            if (errorAlert) {
                alertContainer.innerHTML = errorAlert.outerHTML + alertContainer.innerHTML;
            }

            // Remove alerts after 3 seconds
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            }, 3000);

            // Reset form state
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving your profile. Please try again.');
            submitBtn.innerHTML = originalBtnText;
            submitBtn.disabled = false;
        });
    });

    // Prevent form resubmission on page refresh
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
    </script>

    <?php renderFooter(); ?>
</body>
</html> 