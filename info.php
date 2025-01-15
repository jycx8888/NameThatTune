<?php
phpinfo();
?>

<div id="hamburger-menu">
        <div class="close-btn" onclick="toggleMenu()">×</div>
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" id="profilePicture"> <!-- Display the profile picture -->
        <div class="username" id="username"><?php echo htmlspecialchars($username); ?></div>
             
        <div class="edit-profile" id="editProfile">
            <input type="file" id="profilePictureInput" accept="image/*">
            <input type="text" id="usernameInput" placeholder="Enter new username">
            <button onclick="saveProfile()">Save</button>
        </div>
        <div class="menu-item" onclick="toggleSubmenu('settings-submenu')">Settings</div>
        <div id="settings-submenu" class="submenu">
            <div class="submenu-item" onclick="toggleVolumeControl()">Volume</div>
            <div class="volume-control" id="volumeControl">
                <input type="range" min="0" max="100" value="50" id="volumeSlider">
            </div>
            <div class="submenu-item">Dark Mode</div>
            <div class="submenu-item">Change Password</div>
        </div>
        <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
    </div>



    <div>
            <p>About Us</p>
            <div>
                <p style="width: 500px;">At NameThatTune, we bring the excitement and passion of music to life with engaging, interactive games crafted for music lovers of all kinds. Since our launch, we have dedicated ourselves to creating a platform where music fans can test their skills, learn new tunes, and share memorable experiences. In 2024, we are proud to introduce an upgraded gaming experience, complete with new challenges, modes, and ways to connect with friends. Whether you’re a seasoned music expert or a casual listener, you’ll find daily quizzes, song identification games, and challenges that push the limits of your musical knowledge. Dive into the world of music, compete with others, and explore your favorite songs like never before.</p>
            </div>
        </div>
        <div class="social-icons">
            <p>Connect with us:</p>
            <a href="https://www.facebook.com" target="_blank">
                <img src="facebookLogo.png" alt="Facebook"> <!-- Replace with the path to your Facebook logo image -->
            </a>
            <a href="https://www.instagram.com" target="_blank">
                <img src="instagramLogo.png" alt="Instagram"> <!-- Replace with the path to your Instagram logo image -->
            </a>
        </div>