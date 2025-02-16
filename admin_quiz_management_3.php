<?php
// Database connection
    session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$server = "localhost";
$user = "root";
$password = "";
$database = "namethattune";

// Create connection
$conn = new mysqli($server, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];


// Fetch user data from the database
$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    // Handle case where user data is not found
    $profile_picture_path = 'Icon/account.png'; // Default profile picture
}

if (isset($_GET['question_id'])) {
    $question_id = $_GET['question_id'];

    $stmt = $conn->prepare("SELECT * FROM question WHERE QuestionID = ?");
    $stmt->bind_param("s", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $question = $result->fetch_assoc();
    } else {
        die("Question not found.");
    }
} else {
    die("Question ID not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management</title>
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <link rel="stylesheet" href="user_header.css">
    <style>
        #container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 20px;
        }

        #form-container {
            width: 500px;
            padding: 20px;
            background-color: white;
            border-radius: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #form-container h2 {
            text-align: center;
            font-family: "Lalezar", system-ui;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="file"] {
            width: 90%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 15px;
            font-size: 14px;
        }

        .options {
            margin-bottom: 20px;
        }

        .option-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-item input[type="text"] {
            flex: 1;
            margin-right: 10px;
            border-radius: 10px;
        }

        .option-item img {
            cursor: pointer;
            width: 30px;
            height: 30px;
            margin-left: 10px;
        }

        .option-item img.selected {
            border: 2px solid green;
            border-radius: 50%;
        }

        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #4B006E;
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-size: 18px;
        }

        .submit-button:hover {
            background-color: #CBC3E3;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <!-- Main Content Section -->
    <div id="container">
        <div id="form-container">
            <h2>Edit Quiz</h2>
            <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question['QuestionID']); ?>">
            <form id="quiz-form">

            <?php
            // Fetch question data based on question_id
            $question_id = $_GET['question_id'];
            // Fetch data from the database

            // Return only the dynamic content
            ?>
            <div class="form-group">
                <label for="song-name">Song Name</label>
                <input type="text" id="song-name" name="song_name" value="<?php echo htmlspecialchars($question['SongName'] ?? ''); ?>">
            </div>

            <div class="form-group">
            <label for="song-photo">Song Photo</label>
            <input type="file" id="song-photo" name="song_photo" accept="image/*" onchange="handleImageSelection(this)">
            <div id="song-photo-display" style="margin-top: 10px;">
                <?php if (!empty($question['SongPhoto'])): ?>
                    <img src="<?php echo htmlspecialchars($question['SongPhoto']); ?>" alt="Song Photo" style="max-width: 200px;">
                    <button type="button" onclick="deleteImage()">Delete</button>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="song-mp3">Song MP3</label>
                <input type="file" id="song-mp3" name="song_mp3" accept=".mp3">
                <?php if (!empty($question['SongMP3'])): ?>
                    <audio controls src="<?php echo htmlspecialchars($question['SongMP3']); ?>"></audio>
                <?php endif; ?>
            </div>

            <div class="form-group options">
                <label>Options</label>
                <?php
                $options = json_decode($question['Options'] ?? '[]', true);
                for ($i = 0; $i < 4; $i++):
                    $option = $options[$i] ?? '';
                    $isCorrect = ($option === $question['CorrectAnswer']);
                ?>
                    <div class="option-item">
                        <input type="text" name="option_<?php echo $i + 1; ?>" placeholder="Option <?php echo $i + 1; ?>" value="<?php echo htmlspecialchars($option); ?>">
                        <img src="Icon/<?php echo $isCorrect ? 'select.png' : 'no_select.png'; ?>" alt="Correct Icon" class="correct-icon" onclick="toggleCorrect(this)">
                    </div>
                <?php endfor; ?>
</div>
                        <div id="loading" style="display: none;">Loading...</div>
                    </div>
            </form>
        </div>
    </div>

    <script>
        function toggleCorrect(icon) {
            // Remove the 'selected' class from all icons
            const correctIcons = document.querySelectorAll('.correct-icon');
            correctIcons.forEach(icon => icon.classList.remove('selected'));

            // Add the 'selected' class to the clicked icon
            icon.classList.add('selected');

            // Update the hidden input field with the correct answer
            const selectedOption = icon.previousElementSibling.value;
            document.getElementById('correct-answer').value = selectedOption;
        }
    </script>

    <!-- Overlay -->
    <div id="overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000;"></div>

    <!-- Pop-up Container -->
    <div id="editQuizPopup" style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 10px; z-index: 1001;">
        <!-- Your existing form content goes here -->
        <h2>Edit Quiz</h2>
        <form id="quiz-form">
            <!-- Form fields -->
            <button type="button" id="closePopupButton" onclick="closePopup()">Close</button>
            <!-- Rest of the form -->

            <input type="file" id="song-photo" name="song_photo" accept="image/*" onchange="handleImageSelection(this)">
        </form>
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
            <form action="update_profile.php" method="post" enctype="multipart/form-data">
                <input type="file" name="ProfilePicture" id="profileImageInput">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Username Popup -->
    <div id="usernamePopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('usernamePopup')">&times;</span>
            <h2>Change Username</h2>
            <form onsubmit="return validateNewUsername()" action="update_username.php" method="post">
                <input type="text" name="newUsername" id="usernameInput" placeholder="Enter new username">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Password Popup -->
    <div id="passwordPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('passwordPopup')">&times;</span>
            <h2>Change Password</h2>
            <form onsubmit="return validateNewPassword()" action="update_password.php" method="post">
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
                <button type="submit">Save</button>
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
        function closePopup() {
            document.getElementById('editQuizPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }
    </script>

    <script>
        const songPhotoInput = document.getElementById('song-photo');
        const songPhotoDisplay = document.getElementById('song-photo-display');
        const songMp3Input = document.getElementById('song-mp3');
        const mp3Display = document.getElementById('mp3-display');
        const correctIcons = document.querySelectorAll('.correct-icon');

        // Handle song photo selection
        songPhotoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    songPhotoDisplay.innerHTML = `
                        <img src="${e.target.result}" alt="Song Photo" style="max-width: 200px;">
                        <button id="delete-photo">Delete Photo</button>
                    `;
                    document.getElementById('delete-photo').addEventListener('click', () => {
                        songPhotoDisplay.innerHTML = '';
                        songPhotoInput.value = '';
                    });
                };
                reader.readAsDataURL(file);
            } else {
                alert("Please select a valid image file.");
            }
        });

        // Handle MP3 file selection
        songMp3Input.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && file.type === "audio/mpeg") {
                const fileURL = URL.createObjectURL(file);
                mp3Display.innerHTML = `
                    <p>Selected MP3: ${file.name}</p>
                    <audio controls src="${fileURL}"></audio>
                    <button id="delete-mp3">Delete</button>
                `;
                document.getElementById('delete-mp3').addEventListener('click', () => {
                    mp3Display.innerHTML = '';
                    songMp3Input.value = '';
                });
            } else {
                alert("Please select a valid MP3 file.");
            }
        });

        // Handle selecting the correct answer option
        correctIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                correctIcons.forEach(icon => icon.classList.remove('selected'));
                icon.classList.add('selected');
            });
        });
    </script>

    <script>
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
            window.location.href = 'admin_login.php'; // Redirect to login page
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

<script>
    function validateForm() {
    const songName = document.getElementById('song-name').value;
    const songPhoto = document.getElementById('song-photo').files[0];
    const songMp3 = document.getElementById('song-mp3').files[0];
    const options = document.querySelectorAll('.option-item input[type="text"]');

    if (!songName) {
        alert('Please enter a song name.');
        return false;
    }

    if (!songPhoto) {
        alert('Please select a song photo.');
        return false;
    }

    if (!songMp3) {
        alert('Please select a song MP3.');
        return false;
    }

    for (let i = 0; i < options.length; i++) {
        if (!options[i].value) {
            alert('Please fill in all options.');
            return false;
        }
    }

    return true;
}
</script>

<script>
document.getElementById('quiz-form').addEventListener('submit', function (event) {
    event.preventDefault(); // Prevent the default form submission

    // Validate the form before submission
    if (!validateForm()) {
        return;
    }

    // Show loading indicator
    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    // Prepare form data
    const formData = new FormData(this);

    // Send the form data to the server
    fetch('update_question.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json()) // Parse the JSON response
    .then(data => {
        // Hide loading indicator
        loadingIndicator.style.display = 'none';

        // Show success or error message
        alert(data.message);

        // Close the popup if the update is successful
        if (data.status === 'success') {
            closePopup();
        }
    })
    .catch(error => {
        // Hide loading indicator and show error message
        loadingIndicator.style.display = 'none';
        console.error('Error:', error);
        alert('An error occurred while updating the question.');
    });
});
</script>

<script>
    document.getElementById('quiz-form').addEventListener('submit', function (event) {
    event.preventDefault();

    const loadingIndicator = document.getElementById('loading');
    loadingIndicator.style.display = 'block';

    const formData = new FormData(this);

    fetch('update_question.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        loadingIndicator.style.display = 'none';
        alert(data.message);
        if (data.status === 'success') {
            closePopup();
        }
    })
    .catch(error => {
        loadingIndicator.style.display = 'none';
        console.error('Error:', error);
        alert('An error occurred while updating the question.');
    });
});
</script>
</body>
</html>
