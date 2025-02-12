<?php
session_start();



$servername = "localhost";
$dbusername = "root"; // Database username
$dbpassword = ""; // Database password
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

    // Fetch user data from the database
    $stmt = $conn->prepare("SELECT ProfilePicture FROM user WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $profile_picture_path = $row['ProfilePicture'];
    } else {
        // Handle case where user data is not found
        $profile_picture_path = 'Icon/account.png'; // Default profile picture
    }
    
    $stmt->close();
}
    
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_username'])) {
        $new_username = $_POST['newUsername'];
    
        $stmt = $conn->prepare("UPDATE user SET Username = ? WHERE Username = ?");
        $stmt->bind_param("ss", $new_username, $username);
        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $username = $new_username;
        } else {
            echo "Error updating username.";
        }
    
        $stmt->close();
    }
    
    if (isset($_POST['update_password'])) {
        $new_password =$_POST['newPassword'];
        $stmt = $conn->prepare("UPDATE user SET Password = ? WHERE Username = ?");
        $stmt->bind_param("ss", $new_password, $username);
        if ($stmt->execute()) {
        } else {
            echo "Error updating password.";
        }
        $stmt->close();
    }

    if (isset($_POST['update_profile'])) {
        $profile_picture = $_FILES['ProfilePicture']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture);
    
        if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE user SET ProfilePicture = ? WHERE Username = ?");
            $stmt->bind_param("ss", $target_file, $username);
            if ($stmt->execute()) {
                $profile_picture_path = $target_file;
            } else {
                echo "Error updating profile picture.";
            }
        
            $stmt->close();
        } else {
            echo "Error uploading file.";
        }
    }
}


$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_footer.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        
        #content {
            flex-grow: 5; /* This ensures content takes up remaining space */
            display: flex;
            justify-content: center;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
        }

        .container {
            width: clamp(420px, 50%, 800px);
            margin: 20px auto;
            padding: 20px 20px 10px; /* Reduced the padding-bottom from 20px to 10px */
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: clamp(18px, 3vw, 22px);

        }

        .container-short-bottom {
            margin-bottom: 5px; /* Reduced from 10px to 5px */
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contact-info h1 {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            margin: 20px 0;
        }

        .contact-info p {
            margin: 10px 24px;
            text-align: center;
        }

        .contact-info a {
            text-decoration: none;
            color: #4CAF50;
            margin: 10px 0;
            transition: color 0.3s;
        }
        .contact-info a:hover {
            color: #388E3C;
        }
        .icon {
            margin-right: 10px;
        }

    
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
            <?php
            if (isset($_SESSION['username'])) {
                $profile_picture_path = htmlspecialchars($profile_picture_path);
                $username = htmlspecialchars($username);
                echo "<div id='login'>
                <img src='$profile_picture_path'>
                <p>$username</p>";
            } else {
                echo "<div id='login' onclick='redirectToLogin()'>
                <p>Login</p>
                </div>";
            }
            ?>
        </div>
    </div>

    <div id="hamburger-menu">
        <div class="close-btn" onclick="toggleMenu()">×</div>
        <div class="profile-container">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" id="profilePicture" onclick="showPopup('profilePopup')"> <!-- Display the profile picture -->
            <img src="Icon/pencil.png" alt="Edit" class="edit-icon" style="width: 60px; height: 60px;" onclick="showPopup('profilePopup')">
        </div>
        <div class="username" id="username"><?php echo htmlspecialchars($username); ?></div>
        <div class="menu-item" onclick="directToHistory()">History</div>
        <div class="menu-item" onclick="showPopup('usernamePopup')">Change Username</div>
        <div class="menu-item" onclick="showPopup('passwordPopup')">Change Password</div>
        <div class="menu-item" onclick="toggleSubmenu('settings-submenu')">Settings</div>
        <div id="settings-submenu" class="submenu">
            <div class="submenu-item" onclick="toggleVolumeControl()">Volume</div>
            <div class="volume-control" id="volumeControl">
                <input type="range" min="0" max="100" value="50" id="volumeSlider">
            </div>
            <div class="submenu-item">Dark Mode</div>
        </div>
        <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
    </div>

    <!-- Profile Popup -->
    <div id="profilePopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('profilePopup')">&times;</span>
            <h2>Change Profile Image</h2>
            <form action="" method="post" enctype="multipart/form-data">
                <input type="file" name="ProfilePicture" id="profileImageInput">
                <button type="submit" name="update_profile">Save</button>
            </form>
        </div>
    </div>

    <!-- Username Popup -->
    <div id="usernamePopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('usernamePopup')">&times;</span>
            <h2>Change Username</h2>
            <form onsubmit="return validateNewUsername()" action="" method="post">
                <input type="text" name="newUsername" id="usernameInput" placeholder="Enter new username">
                <button type="submit" name="update_username">Save</button>
            </form>
        </div>
    </div>

    <!-- Password Popup -->
    <div id="passwordPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('passwordPopup')">&times;</span>
            <h2>Change Password</h2>
            <form onsubmit="return validateNewPassword()" action="" method="post">
                <div style="position: relative;">
                    <input type="password" name="newPassword" id="newPasswordInput" placeholder="Enter new password" required>
                    <span class="toggle-password"  style="position: absolute; right: 15px; object-fit: contain; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <img src="Icon/hide.png" alt="Show Password" onclick="togglePasswordVisibility('newPasswordInput',this)" style="width: 20px; height: 20px;">
                    </span>
                </div>
                <div style="position: relative;">
                    <input type="password" name="confirmPassword" id="confirmPasswordInput" placeholder="Confirm new password" required>
                    <span class="toggle-password"  style="position: absolute; right: 15px; object-fit: contain; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <img src="Icon/hide.png" name alt="Show Password" onclick="togglePasswordVisibility('confirmPasswordInput',this)" style="width: 20px; height: 20px;">
                    </span>
                </div>
                <button type="submit" name="update_password">Save</button>
            </form>
        </div>
    </div>

    <!-- Warning Popup -->
    <div id="warningPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closeWarningPopup()">&times;</span>
            <p id="warningMessage"></p>
        </div>
    </div>

   <div id="content">
        <div class="container">
            <div class="contact-info">
                <h1>Contact Us</h1>
                <p>We'd love to hear from you! Connect with us through any of the following channels:</p>
                <a href="https://www.instagram.com" target="_blank">
                    <span class="icon">📸</span> Follow us on Instagram
                </a>
                <a href="https://www.facebook.com" target="_blank">
                    <span class="icon">📘</span> Like us on Facebook
                </a>
                <a href="mailto:namethattune@gmail.com">
                    <span class="icon">✉️</span> Email us at namethattune@gmail.com
                </a>
                <a href="tel:019-782-6732">
                    <span class="icon">📞</span> Call us at 019-782-6732
                </a>
            </div>
        </div>
    </div>

    <div id="logoutOverlay" class="overlay">
        <div class="popup" id="logoutPopup">
            <p>Do you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeLogoutPopup()">No</button>
        </div>
    </div>

    <div id="footer">
        <ul class="nav">
            <li><a href="user_about_us.php">About Us</a></li>
            <li><a href="user_terms_and_conditions.php">Terms and Conditions</a></li>
            <li><a href="user_privacy_policy.php">Privacy Policy</a></li>
            <li><a href="user_contact_us.php">Contact Us</a></li>
        </ul>
        
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>
</body>

<script>
    function redirectToLogin() {
    window.location.href = "user_login.php";
    }

    function validateNewUsername() {
            const username = document.getElementById('usernameInput').value;
            
            if (username === "") {
                showWarning('Username cannot be empty.');
                return false;
            }

            return true;
        }

        function validateNewPassword() {
            const password = document.getElementById('newPasswordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%#*?&])[A-Za-z\d@$!%#*?&]{8,14}$/;

            if (password === "") {
                showWarning('New Password cannot be empty.');
                return false;
            }

            if (confirmPassword === "") {
                showWarning('Confirm Password cannot be empty.');
                return false;
            }

            if (password !== confirmPassword) {
                showWarning('Passwords do not match.');
                return false;
            }

            if (!passwordRegex.test(password)) {
                showWarning('Password must be 8-14 characters long, include at least one capital letter, one number, and one special character.');
                return false;
            }

            return true;
        }

        document.getElementById('login').addEventListener('click', function() {
            document.getElementById('hamburger-menu').classList.toggle('open');
        });

        function showPopup(popupId) {
            document.getElementById(popupId).classList.add('show');
        }

        function closePopup(popupId) {
            document.getElementById(popupId).classList.remove('show');
        }

        function togglePasswordVisibility(fieldId, iconElement) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconElement);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                iconElement.src = 'Icon/show.png'; // Change to the open eye icon
            } else {
                passwordField.type = 'password';
                iconElement.src = 'Icon/hide.png'; // Change to the closed eye icon
            }
        }

        function toggleMenu() {
            document.getElementById('hamburger-menu').classList.toggle('open');
        }

        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
        }

        function toggleVolumeControl() {
            const volumeControl = document.getElementById('volumeControl');
            volumeControl.style.display = volumeControl.style.display === 'block' ? 'none' : 'block';
        }

        function confirmLogout() {
            document.getElementById('logoutOverlay').classList.add('show');
        }

        function closeLogoutPopup() {
            document.getElementById('logoutOverlay').classList.remove('show');
        }

        function logout() {
            window.location.href = 'user_login.php'; // Redirect to login page
            session_unset();
            session_destroy();
        }

        document.getElementById('volumeSlider').addEventListener('input', function() {
            const volume = this.value;
            console.log('Volume:', volume); // Replace with actual volume control logic
        });

        function showWarning(message) {
            const warningPopup = document.getElementById('warningPopup');
            const warningMessage = document.getElementById('warningMessage');
            warningMessage.textContent = message;
            warningPopup.classList.add('show');
        }
        
        function closeWarningPopup() {
            const warningPopup = document.getElementById('warningPopup');
            warningPopup.classList.remove('show');
        }
</script>

</html>
