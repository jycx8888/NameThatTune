<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

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

    // Redirect to avoid form resubmission
    header("Location: user_mainPage.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune</title>
    <link rel="icon" href="icon/logo.jpg" type="image/png">
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_footer.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        #main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            height: calc(99vh - 100px); 
        }

        .category-dropdown {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: small;
            padding: 10px;
            border: 2px solid #000000;
            border-radius: 10px;
            background-color: #ffffff;
            color: #000000;
            background-image: url('icon/music.png');
            background-repeat: no-repeat;
            background-position: right 25px center;
            background-size: 20px;
            padding-right: 40px;
            margin-top: -100px;
        }

        .category-dropdown option {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: small;
            color: #000; /* Text color */
            background-color: #fff; /* Background color */
            padding: 5px; /* Option padding */
        }

        #quiz-gallery-container {
            overflow: hidden;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(50vh - 100px);
            
        }

        #quiz-gallery {
            display: flex;
            gap: 10px;
            animation: scroll-left 20s linear infinite;
            white-space: nowrap;
        }

        @keyframes scroll-left {
            from {
                transform: translateX(0); /* Start from the original position */
            }
            to {
                transform: translateX(-50%); /* Move to the end of the first loop */
            }
        }

        .quiz-card {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: small;
            display: inline-block;
            min-width: 180px;
            height: 240px;
            background-color: #444;
            color: white;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            overflow: hidden;
            margin: 10px;
            transition: transform 0.3s ease;
        }

        .quiz-card img {
            width: 100%;
            height: 75%;
            object-fit: cover;
        }

        .quiz-card span {
            display: block;
            padding: 10px 5px; /* Increase padding at the top */
            font-size: 1.3rem;
            background-color: rgba(0, 0, 0, 0.6);
            position: relative;
            top: 5px; /* Move the text down */
        }

        .quiz-card:hover {
            transform: scale(1.1);
        }

        #start-button {
            margin-top: 20px;
            padding: 20px 15px;
            font-size: 25px;
            border-radius: 50px;
            border: none;
            background: linear-gradient(to left, #E09385, #B959DD, #7CB4E1);
            color: white;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 1000; /* Change the font family */
            align-self: center;
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transitions */
        }

        #start-button:hover {
            background-color: #1a0573;
            transform: scale(1.1); /* Slightly enlarge the button */
            box-shadow: 0 8px 15px rgba(26, 5, 115, 0.5); /* Add a shadow effect */
        }

        /* Volume and Fullscreen Controls */
        #controls {
            position: sticky; /* Fixes the controls to the viewport */
            width: 100%; /* Full width of the screen */
            bottom: 20px; /* Stays 20px from the bottom */
            display: flex; /* Align buttons */
            justify-content: space-between; /* Align one button to the left and the other to the right */
            align-items: center;
            padding: 0 20px;
            z-index: 9999; /* Ensures the controls stay above other elements */
            background-color: transparent; /* Optional: Makes the background transparent */
        }

        #volume-control {
            display: flex;
            align-items: center;
            left: 20px; /* Optional: Fine-tune button spacing */
        }

        #volume-icon {
            width: 40px;
            height: 40px;
            cursor: pointer;
            margin-right: 10px;
        }

        #volume-slider {
            display: none;
            width: 150px;
            height: 5px;
            border-radius: 5px;
            cursor: pointer;
        }

        #fullscreen-control {
            display: flex;
            align-items: center;
            position: sticky; /* Ensures it stays in place */
            bottom: 20px; /* Keep it at the bottom */
            right: 10px; /* Adjust this value to move it left */
            z-index: 9999; /* Keeps it above other content */
        }

        #fullscreen-icon {
            width: 70px;
            height: 70px;
            cursor: pointer;
        }

        /* Snow container */
        #snow {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none; /* Ensure it doesn't interfere with user interactions */
            z-index: 9999; /* Keep it above all other elements */
            overflow: hidden;
        }

        /* Snowflake */
        .snowflake {
            position: absolute;
            top: -10px;
            color: white; /* Snowflake color */
            font-size: 1rem; /* Adjust size of snowflake */
            animation-name: fall;
            animation-timing-function: linear;
            animation-iteration-count: infinite;
        }

        /* Animation for falling */
        @keyframes fall {
            0% {
                transform: translateY(0) rotate(0deg);
            }
            100% {
                transform: translateY(100vh) rotate(360deg);
            }
        }

    </style>
</head>
<body>
    <audio id="background-audio" autoplay loop>
        <source src="Background Audio/Hotel lobby music.mp3" type="audio/mpeg">
    </audio> 

    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div id="snow"></div>

    <div id="main">
        <select class="category-dropdown" id="category-dropdown">
            <option value="Category List">Select a Category...</option>
            <option value="English">English</option>
            <option value="Japanese">Japanese</option>
            <option value="Korean">Korean</option>
        </select>

        <div id="quiz-gallery-container">
            <div id="quiz-gallery"></div>
        </div>

        <button id="start-button">Click to Start</button>
    </div>

    <div id="controls">
        <div id="volume-control">
            <img id="volume-icon" src="icon/volume.png" alt="Volume Icon" onclick="toggleVolumeSlider()">
            <input id="volume-slider" type="range" min="0" max="100" value="50" onchange="adjustVolume(this.value)">
        </div>
        <div id="fullscreen-control">
            <img id="fullscreen-icon" src="icon/fullscreen.png" alt="Fullscreen Icon" onclick="toggleFullscreen()">
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
        
    <div id="hamburger-menu">
        <div class="close-btn" onclick="toggleMenu()">×</div>
        <div class="profile-container">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" id="profilePicture" onclick="showPopup('profilePopup')"> <!-- Display the profile picture -->
            <img src="Icon/pencil.png" alt="Edit" class="edit-icon" style="width: 60px; height: 60px;" onclick="showPopup('profilePopup')">
        </div>
        <div class="username" id="username"><?php echo htmlspecialchars($username); ?></div>
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

    <div id="logoutOverlay" class="overlay">
        <div class="popup" id="logoutPopup">
            <p>Do you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeLogoutPopup()">No</button>
        </div>
    </div>


    <script>
        function createSnowflakes() {
            const snowContainer = document.getElementById('snow');
            const snowflakeCount = 20; // Decreased number of snowflakes per interval

            for (let i = 0; i < snowflakeCount; i++) {
                const snowflake = document.createElement('div');
                snowflake.classList.add('snowflake');
                snowflake.innerHTML = '&#10052;'; // Unicode for a snowflake

                // Random position and animation duration
                snowflake.style.left = Math.random() * 100 + 'vw';
                snowflake.style.animationDuration = Math.random() * 5 + 3 + 's'; // Slower fall (3s to 8s)
                snowflake.style.fontSize = Math.random() * 10 + 8 + 'px'; // Smaller snowflakes (8px to 18px)
                snowflake.style.opacity = Math.random() * 0.8 + 0.2; // Lighter snowflakes

                snowContainer.appendChild(snowflake);

                // Remove the snowflake after animation ends
                snowflake.addEventListener('animationend', () => {
                    snowflake.remove();
                });
            }
        }

        // Decrease frequency of snowflake generation (every 1 second)
        setInterval(createSnowflakes, 1000);

        const AudioElement = document.getElementById('background-audio');
        const volumeSlider = document.getElementById('volume-slider');

        function toggleVolumeSlider() {
            volumeSlider.style.display = volumeSlider.style.display === 'block' ? 'none' : 'block';
        }

        function adjustVolume(value) {
            AudioElement.volume = value / 100; // Adjust the audio volume
        }

        // Fullscreen Toggle
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        }

        const englishSongs = [
            { image: 'English Song Photo/Doja.jpg', name: 'Doja' },
            { image: 'English Song Photo/Rewrite The Stars.jpg', name: 'Rewrite The Stars' },
            { image: 'English Song Photo/Viva La Vida.jpg', name: 'Viva La Vida' },
            { image: 'English Song Photo/We Dont Talk Anymore.jpg', name: 'We Dont Talk Anymore' },
            { image: "English Song Photo/I'm Yours.png", name: "I'm Yours" }
        ];

        const japaneseSongs = [
            { image: 'Japanese Song Photo/BLUE BIRD.jpg', name: 'BLUE BIRD' },
            { image: 'Japanese Song Photo/CRY FOR ME.jpeg', name: 'CRY FOR ME' },
            { image: 'Japanese Song Photo/Flamingo.png', name: 'Flamingo' },
            { image: 'Japanese Song Photo/Gunjo.jpeg', name: 'Gunjo' },
            { image: 'Japanese Song Photo/Gurenge.jpg', name: 'Gurenge' },
        ];

        const koreanSongs = [
            { image: 'Korean Song Photo/Blackpink.png', name: 'How You Like That' },
            { image: 'Korean Song Photo/G-IDLE.jpg', name: 'Tomboy' },
            { image: 'Korean Song Photo/ILLIT.webp', name: 'Magnetic' },
            { image: 'Korean Song Photo/New Jeans.jpg', name: 'Ditto' },
            { image: 'Korean Song Photo/TWICE.jpg', name: 'I GOT YOU' },
        ];

        const audioElement = document.getElementById('background-audio');

        const audioSources = {
            English: 'Background Audio/Glad You Came.mp3',
            Japanese: 'Background Audio/SPECIALZ.mp3',
            Korean: 'Background Audio/HARU HARU.mp3',
        };

        function changeAudio(category) {
            if (audioSources[category]) {
                audioElement.src = audioSources[category];
                audioElement.play();
            }
        }

        document.getElementById('category-dropdown').addEventListener('change', function () {
            const selectedCategory = this.value;
            let songsToShow = [];

            if (selectedCategory === 'English') {
                songsToShow = englishSongs;
            } else if (selectedCategory === 'Japanese') {
                songsToShow = japaneseSongs;
            } else if (selectedCategory === 'Korean') {
                songsToShow = koreanSongs;
            }

            const gallery = document.getElementById('quiz-gallery');
            gallery.innerHTML = '';  // Clear the existing gallery

            songsToShow.forEach(song => {
                const card = document.createElement('div');
                card.classList.add('quiz-card');
                card.innerHTML = `
                    <img src="${song.image}" alt="${song.name}">
                    <span>${song.name}</span>
                `;
                gallery.appendChild(card);
            });

            setupContinuousScrolling();

            changeAudio(selectedCategory);
        });

        function setupContinuousScrolling() {
            const gallery = document.getElementById('quiz-gallery');
            const cards = Array.from(gallery.children);

            // Clone cards until the gallery width exceeds the container width
            const galleryContainer = document.getElementById('quiz-gallery-container');
            let totalWidth = 0;

            while (totalWidth < galleryContainer.offsetWidth * 2) {
                cards.forEach(card => {
                    const clone = card.cloneNode(true);
                    gallery.appendChild(clone);
                    totalWidth += card.offsetWidth + parseFloat(getComputedStyle(card).marginRight);
                });
            }

            // Set the gallery width dynamically
            gallery.style.width = `${totalWidth}px`;

            // Reset and reapply animation
            gallery.style.animation = 'none'; // Stop animation temporarily
            gallery.offsetHeight; // Trigger reflow
            gallery.style.animation = `scroll-left ${totalWidth / 100}s linear infinite`; // Set duration based on total width
        }


        document.getElementById('start-button').addEventListener('click', function () {
            const selectedCategory = document.getElementById('category-dropdown').value;

            if (selectedCategory === 'Category List') {
                showWarning('Please select a category first!');
            } else if (selectedCategory === 'English') {
                window.location.href = 'user_question_page_english.php';
            } else if (selectedCategory === 'Japanese') {
                window.location.href = 'user_question_page_japanese.php';
            } else if (selectedCategory === 'Korean') {
                window.location.href = 'user_question_page_korean.php';
            }
        });

        // Add a hover sound effect
        const startButton = document.getElementById('start-button');
        const hoverSound = new Audio('Sound Effect/hover_sound_effect.mp3'); // Replace with the actual path
        const clickSound = new Audio('Sound Effect/click_sound_effect.mp3'); // Replace with the actual path

        // Play hover sound
        startButton.addEventListener('mouseover', () => {
            hoverSound.play();
        });

        // Play click sound
        startButton.addEventListener('click', () => {
            clickSound.play();
        });
        
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

        function directToChooseCategory() {
            window.location.href = 'user_choose_category_page.php';
        }
    </script>
</body>
</html>
