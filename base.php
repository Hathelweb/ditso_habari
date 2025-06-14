<?php
include 'php/config.php';

// Add caching headers
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(isset($_SESSION['user_id'])) {
    $user = $_SESSION['user_id'];
    
    // Only fetch user data if session data is not set or needs updating
    if (!isset($_SESSION['user_data']) || $_SESSION['user_data']['id'] != $user) {
        $sql = "SELECT * FROM users WHERE id=?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $user);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user_data = mysqli_fetch_assoc($result);
        
        if ($user_data) {
            $_SESSION['user_data'] = $user_data;
        }
    }
}

function renderHeader() {
    global $conn;
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
        <meta http-equiv="Pragma" content="no-cache">
        <meta http-equiv="Expires" content="0">
        <title>Ditso Habari</title>
        <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
        <link rel="stylesheet" type="text/css" href="css/ditso.css">
        <style>
            /* Base styles for all devices */
            :root {
                --primary-pink: #FF1D58;
                --secondary-pink: #FF4D6D;
                --primary-blue: #2E86C1;
                --secondary-blue: #3498DB;
                --light-gray: #F5F6FA;
                --dark-gray: #2C3E50;
                --white: #FFFFFF;
                --hover-bg: #F8F9FA;
                --container-width: 1200px;
                --header-height: 80px;
                --gradient-primary: linear-gradient(135deg, var(--primary-pink) 0%, var(--secondary-pink) 100%);
                --gradient-secondary: linear-gradient(135deg, var(--primary-blue) 0%, var(--secondary-blue) 100%);
                --shadow-sm: 0 2px 4px rgba(0, 0, 0, 0.05);
                --shadow-md: 0 4px 6px rgba(0, 0, 0, 0.1);
                --shadow-lg: 0 10px 15px rgba(0, 0, 0, 0.1);
                --border-radius: 12px;
                --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                background-color: var(--light-gray);
                color: var(--dark-gray);
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
                line-height: 1.6;
                overflow-x: hidden;
                min-height: 100vh;
            }

            .nav-menu {
                background: var(--white);
                padding: 1.5rem 0;
                display: flex;
                flex-direction: column;
                align-items: center;
                width: 100%;
                position: relative;
                box-shadow: none;
                min-height: var(--header-height);
            }

            .nav-header {
                background: var(--white);
                width: 100%;
                padding: 1.5rem;
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 1.5rem;
                position: relative;
                z-index: 100;
            }

            .user-profile-section {
                position: absolute;
                top: 1rem;
                left: 1rem;
                display: flex;
                align-items: center;
                gap: 1rem;
                z-index: 101;
                background: var(--white);
                padding: 0.5rem;
                border-radius: 50px;
                box-shadow: var(--shadow-sm);
                transition: all 0.3s ease;
                will-change: transform;
                transform: translateZ(0);
                backface-visibility: hidden;
            }

            .user-profile-section:hover {
                box-shadow: var(--shadow-md);
            }

            .user-profile-section .profile-img {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                object-fit: cover;
                border: 2px solid var(--primary-pink);
                transition: all 0.3s ease;
                background: var(--light-gray);
                will-change: transform;
                transform: translateZ(0);
                backface-visibility: hidden;
            }

            .user-profile-section .user-info {
                display: flex;
                flex-direction: column;
                min-width: 100px;
            }

            .user-profile-section .user-name {
                font-weight: 600;
                color: var(--dark-gray);
                font-size: 0.9rem;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .user-profile-section .user-role {
                font-size: 0.8rem;
                color: var(--primary-pink);
                white-space: nowrap;
            }

            .nav-header h1 {
                background: var(--gradient-primary);
                -webkit-background-clip: text;
                background-clip: text;
                color: transparent;
                font-weight: 800;
                margin: 0;
                font-size: clamp(1.8rem, 5vw, 2.8rem);
                text-align: center;
                text-transform: uppercase;
                letter-spacing: 3px;
                padding: 0 1.5rem;
                transition: var(--transition);
                text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            }

            .nav-top {
                display: flex;
                justify-content: space-between;
                align-items: center;
                width: 100%;
                max-width: var(--container-width);
                padding: 0 1rem;
                position: relative;
            }

            .settings-button {
                position: absolute;
                top: 1rem;
                right: 1rem;
                background: var(--white);
                border: none;
                padding: 0.5rem;
                border-radius: 50%;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                box-shadow: var(--shadow-sm);
                z-index: 101;
            }

            .settings-button i {
                color: var(--primary-pink);
                font-size: 1.5rem;
                transition: transform 0.3s ease;
            }

            .settings-button:hover {
                transform: scale(1.1);
                box-shadow: var(--shadow-md);
            }

            .settings-button:hover i {
                transform: rotate(30deg);
            }

            .settings-dropdown {
                position: absolute;
                top: 100%;
                right: 1rem;
                background: var(--white);
                border-radius: var(--border-radius);
                box-shadow: var(--shadow-md);
                padding: 1rem;
                min-width: 200px;
                display: none;
                z-index: 1000;
            }

            .settings-dropdown.show {
                display: block;
                animation: slideDown 0.3s ease;
            }

            @keyframes slideDown {
                from {
                    opacity: 0;
                    transform: translateY(-10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .settings-dropdown a {
                display: flex;
                align-items: center;
                gap: 0.8rem;
                padding: 0.8rem 1rem;
                color: var(--dark-gray);
                text-decoration: none;
                border-radius: 8px;
                transition: all 0.3s ease;
            }

            .settings-dropdown a:hover {
                background: var(--light-gray);
                color: var(--primary-pink);
            }

            .settings-dropdown a i {
                color: var(--primary-pink);
                font-size: 1.2rem;
            }

            .settings-dropdown .divider {
                height: 1px;
                background: var(--light-gray);
                margin: 0.5rem 0;
            }

            .settings-dropdown .save-changes-btn {
                width: 100%;
                background: var(--gradient-primary);
                color: var(--white);
                border: none;
                padding: 0.8rem 1rem;
                border-radius: 8px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 0.8rem;
                transition: all 0.3s ease;
                margin: 0.5rem 0;
            }

            .settings-dropdown .save-changes-btn:hover {
                transform: translateY(-2px);
                box-shadow: var(--shadow-md);
            }

            .settings-dropdown .save-changes-btn i {
                color: var(--white);
            }

            .save-success {
                position: fixed;
                top: 20px;
                right: 20px;
                background: #28a745;
                color: white;
                padding: 1rem 2rem;
                border-radius: 8px;
                box-shadow: var(--shadow-md);
                display: flex;
                align-items: center;
                gap: 0.8rem;
                z-index: 1000;
                animation: slideIn 0.3s ease, fadeOut 0.3s ease 2.7s forwards;
            }

            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }

            @keyframes fadeOut {
                from {
                    opacity: 1;
                }
                to {
                    opacity: 0;
                }
            }

            /* Mobile Styles */
            @media (max-width: 767px) {
                .nav-header h1 {
                    font-size: 1.8rem;
                    margin-bottom: 0.8rem;
                    letter-spacing: 2px;
                }

                #content-container {
                    padding: 0 1.2rem;
                }

                .article-card {
                    margin-bottom: 1.2rem;
                }

                .settings-button {
                    top: 0.5rem;
                    right: 0.5rem;
                }

                .settings-dropdown {
                    right: 0.5rem;
                    width: calc(100% - 1rem);
                    max-width: 300px;
                }

                .user-profile-section {
                    top: 0.5rem;
                    left: 0.5rem;
                }

                .user-profile-section .profile-img {
                    width: 35px;
                    height: 35px;
                }

                .user-profile-section .user-name {
                    font-size: 0.8rem;
                }

                .user-profile-section .user-role {
                    font-size: 0.7rem;
                }
            }
        </style>
    </head>
    <body>
        <div class="nav-menu">
            <div class="nav-header">
                <?php if(isset($_SESSION['user_id'])): ?>
                <div class="user-profile-section">
                    <?php
                    $profile_img = isset($_SESSION['user_data']['profile_img']) ? $_SESSION['user_data']['profile_img'] : 'images/default-profile.png';
                    $first_name = isset($_SESSION['user_data']['first_name']) ? $_SESSION['user_data']['first_name'] : '';
                    $last_name = isset($_SESSION['user_data']['last_name']) ? $_SESSION['user_data']['last_name'] : '';
                    $is_admin = isset($_SESSION['user_data']['is_admin']) ? $_SESSION['user_data']['is_admin'] : false;
                    ?>
                    <img src="<?php echo htmlspecialchars($profile_img); ?>" 
                         alt="Profile" 
                         class="profile-img"
                         onerror="this.src='images/default-profile.png'">
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($first_name . ' ' . $last_name); ?></span>
                        <span class="user-role"><?php echo $is_admin ? 'Administrator' : 'User'; ?></span>
                    </div>
                </div>
                <?php endif; ?>
                <button class="settings-button" onclick="toggleSettings()">
                    <i class="fas fa-cog"></i>
                </button>
                <div class="settings-dropdown" id="settingsDropdown">
                    <a href="user_profile.php">
                        <i class="fas fa-user"></i>
                        Edit Profile
                    </a>
                    <a href="change_username.php">
                        <i class="fas fa-at"></i>
                        Change Username
                    </a>
                    <a href="change_password.php">
                        <i class="fas fa-key"></i>
                        Change Password
                    </a>
                    <div class="divider"></div>
                    <button onclick="saveChanges()" class="save-changes-btn">
                        <i class="fas fa-save"></i>
                        Save Changes
                    </button>
                    <div class="divider"></div>
                    <a href="php/logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
                <h1>DITSO HABARI</h1>
            </div>
        </div>

        <script>
        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }

        // Cache DOM elements
        const userProfileSection = document.querySelector('.user-profile-section');
        const profileImg = document.querySelector('.profile-img');
        const userName = document.querySelector('.user-name');
        const userRole = document.querySelector('.user-role');

        function toggleSettings() {
            const dropdown = document.getElementById('settingsDropdown');
            dropdown.classList.toggle('show');

            // Close dropdown when clicking outside
            document.addEventListener('click', function closeDropdown(e) {
                if (!e.target.closest('.settings-button') && !e.target.closest('.settings-dropdown')) {
                    dropdown.classList.remove('show');
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }

        function saveChanges() {
            // Create success message
            const successMessage = document.createElement('div');
            successMessage.className = 'save-success';
            successMessage.innerHTML = `
                <i class="fas fa-check-circle"></i>
                Changes saved successfully
            `;
            document.body.appendChild(successMessage);

            // Remove success message after animation
            setTimeout(() => {
                successMessage.remove();
            }, 3000);

            // Close the settings dropdown
            document.getElementById('settingsDropdown').classList.remove('show');
        }

        // Add event listener for profile image error
        if (profileImg) {
            profileImg.addEventListener('error', function() {
                this.src = 'images/default-profile.png';
            });
        }
        </script>
    <?php
}

function renderFooter() {
    ?>
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="bootstrap/js/bootstrap.min.js"></script>
        <script src="js/ditso.js"></script>
        <script src="js/social-features.js"></script>
    </body>
    </html>
    <?php
}
?>
