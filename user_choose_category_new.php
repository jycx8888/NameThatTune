<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
    exit();
}

$servername = "localhost";
$dbusername = "root"; 
$dbpassword = "";
$dbname = "namethattune";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

include 'user_fetch_profile.php';

if (isset($_GET['genreID'])) {
    $genreID = $_GET['genreID'];

    $sql = "SELECT QuizID, QuizName FROM quiz WHERE GenreID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $genreID);
    $stmt->execute();
    $result = $stmt->get_result();

    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }

    $stmt->close();
    echo json_encode($quizzes);
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
    <style>

        #content {
            display: flex;
            flex-direction: column;
            align-items: center;
            height: fit-content;
            min-height: 100vh;
        }

        .category-dropdown {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: 16px;
            padding: 10px;
            margin-top: 96px;
            border: 2px solid black;
            border-radius: 10px;
            background-color: white;
            color: black;
            background-image: url('icon/music.png');
            background-repeat: no-repeat;
            background-position: right 25px center;
            background-size: 20px;
            padding-right: 40px;
        }

        .category-dropdown option {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: 16px;
            color: black;
            background-color: white;
            padding: 5px;
        }

        @media (max-width: 1024px) {
            .category-dropdown {
                font-size: 14px;
                padding: 8px;
                margin-top: 48px;
                background-position: right 25px center;
                background-size: 20px;
                padding-right: 36px;
            }

            .category-dropdown option {
                font-size: 14px;
                padding: 4px;
            }
        }

        #quiz-gallery-container {
            display: flex;
            width: 80vw;
            height: fit-content;
            min-height: 30vh;
            margin: 24px 0;
            justify-content: center;
        }

        #quiz-gallery {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-evenly;
        }

        @keyframes scroll-left {
            from {
                transform: translateX(0);
            }
            to {
                transform: translateX(-50%);
            }
        }

        .quiz-box {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: small;
            width: 200px;
            height: 200px;
            margin: 12px;
            background-color: rgb(72, 87, 227);
            color: white;
            text-align: center;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
            cursor: pointer;
            overflow: hidden;
            transition: transform 0.3s ease, width 0.3s ease, height 0.3s ease, box-shadow 0.3s ease;
        }

        .quiz-box div {
            display: block;
            padding: 8px 5px;
            font-size: 1.1rem;
            background-color: rgba(0, 0, 0, 0.6);
            position: relative;
            top: 5px;
        }

        @media (max-width: 1024px) {
            #quiz-gallery{
                padding: 0 12px;
                margin-bottom: 12px;
            }

            .quiz-box div {
                font-size: 0.8rem;
                padding: 5px;
            }
        }

        .quiz-box:hover {
            transform: scale(1.1);
        }

        .quiz-box.selected {
            transform: scale(1.1);
        }

        #start-button {
            margin: 24px 0 96px 0;
            padding: 20px 15px;
            font-size: 25px;
            border-radius: 50px;
            border: none;
            background: linear-gradient(to left, #E09385, #B959DD, #7CB4E1);
            color: white;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            align-self: center;
            transition: transform 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease; 
        }

        #start-button:hover {
            background-color: #1a0573;
            transform: scale(1.1); 
            box-shadow: 0 8px 15px rgba(26, 5, 115, 0.5);
        }

        @media (max-width: 1024px) {
            #start-button {
                padding: 15px 10px;
                font-size: 20px;
            }
        }

        #controls {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-self: end;
            align-content: center;
            margin-bottom: 48px;
            background-color: transparent; 
        }

        #volume-control {
            display: flex;
            align-items: center;
            margin-left: 24px;
        }

        #volume-icon {
            width: 40px;
            height: 40px;
            cursor: pointer;
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
            position: sticky; 
            margin-right: 24px;
        }

        #fullscreen-icon {
            width: 70px;
            height: 70px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <audio id="background-audio" autoplay loop>
        <source src="Background Audio/Hotel lobby music.mp3" type="audio/mpeg">
    </audio> 

    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <div id="login">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'user_hamburger_menu.php'; ?>

    <div id="content">
        <select class="category-dropdown" id="category-dropdown">
            <option value="Category List">Select a Category...</option>
            <option value="G001">English</option>
            <option value="G002">Japanese</option>
            <option value="G003">Korean</option>
        </select>

        <div id="quiz-gallery-container">
            <div id="quiz-gallery">
            </div>
        </div>

        <button id="start-button">Click to Start</button>

        <div id="controls">
            <div id="volume-control">
                <img id="volume-icon" src="icon/volume.png" alt="Volume Icon" onclick="toggleVolumeSlider()">
                <input id="volume-slider" type="range" min="0" max="100" value="50" onchange="adjustVolume(this.value)">
            </div>
            <div id="fullscreen-control">
                <img id="fullscreen-icon" src="icon/fullscreen.png" alt="Fullscreen Icon" onclick="toggleFullscreen()">
            </div>
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

    <script>
        document.getElementById('login').addEventListener('click', function() {
            document.getElementById('hamburger-menu').classList.toggle('open');
        });


        function toggleVolumeSlider() {
            const volumeSlider = document.getElementById('volume-slider');
            volumeSlider.style.display = volumeSlider.style.display === 'block' ? 'none' : 'block';
        }

        function adjustVolume(value) {
            const audioElement = document.getElementById('background-audio');
            if (audioElement) {
                audioElement.volume = value / 100;
                audioElement.muted = value == 0;
                console.log("Volume set to:", audioElement.volume, "Muted:", audioElement.muted);
            } else {
                console.log("Error: Audio element not found.");
            }
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

        const audioElement = document.getElementById('background-audio');

        const audioSources = {
            G001: 'Background Audio/Glad You Came.mp3',
            G002: 'Background Audio/SPECIALZ.mp3',
            G003: 'Background Audio/HARU HARU.mp3',
        };

        function changeAudio(category) {
            if (audioSources[category]) {
                audioElement.src = audioSources[category];
                audioElement.play();
            }
        }

        document.getElementById('category-dropdown').addEventListener('change', function () {
            const selectedCategory = this.value;
            if (selectedCategory === 'Category List') {
                const gallery = document.getElementById('quiz-gallery');
                gallery.innerHTML = ''; 
                const audioElement = document.getElementById('background-audio');
                audioElement.pause(); 
                audioElement.src = ''; 
                return;
            }
        
            fetchQuizzes(selectedCategory);
            changeAudio(selectedCategory);
        });

        function fetchQuizzes(genreID) {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', `user_choose_category_new.php?genreID=${genreID}`, true);
            xhr.onload = function () {
                if (xhr.status === 200) {
                    const quizzes = JSON.parse(xhr.responseText);
                    const gallery = document.getElementById('quiz-gallery');
                    gallery.innerHTML = ''; 
        
                    quizzes.forEach(quiz => {
                        const box = document.createElement('div');
                        box.classList.add('quiz-box');
                        box.innerHTML = `
                            <div>${quiz.QuizName}</div>
                        `;
                        box.addEventListener('click', function () {
                            document.querySelectorAll('.quiz-box').forEach(b => b.classList.remove('selected'));
                            box.classList.add('selected');
                            box.dataset.quizId = quiz.QuizID;
                        });
                        gallery.appendChild(box);
                    });
                }
            };
            xhr.send();
        }
        
        document.getElementById('start-button').addEventListener('click', function () {
            const selectedBox = document.querySelector('.quiz-box.selected');
            if (selectedBox) {
                const quizId = selectedBox.dataset.quizId;
                window.location.href = `countdown.php?quizId=${quizId}`;
            }
        });

        function setupContinuousScrolling() {
            const gallery = document.getElementById('quiz-gallery');
            const boxes = Array.from(gallery.children);
        
            const galleryContainer = document.getElementById('quiz-gallery-container');
            let totalWidth = 0;
        
            while (totalWidth < galleryContainer.offsetWidth * 2) {
                boxes.forEach(box => {
                    const clone = box.cloneNode(true);
                    gallery.appendChild(clone);
                    totalWidth += box.offsetWidth + parseFloat(getComputedStyle(box).marginRight);
                });
            }
        
            gallery.style.width = `${totalWidth}px`;
        
            gallery.style.animation = 'none'; 
            gallery.offsetHeight; 
            gallery.style.animation = `scroll-left ${totalWidth / 100}s linear infinite`; 
        }

        document.addEventListener("DOMContentLoaded", function () {
            const startButton = document.getElementById("start-button");
            const hoverSound = new Audio("Sound Effect/hover_sound_effect.mp3"); 
            const clickSound = new Audio("Sound Effect/click_sound_effect.wav");

            hoverSound.preload = "auto";
            clickSound.preload = "auto";

            startButton.addEventListener("mouseover", () => {
                hoverSound.currentTime = 0; 
                hoverSound.play().catch(error => console.error("Hover sound error:", error));
            });

            startButton.addEventListener("click", () => {
                clickSound.currentTime = 0; 
                clickSound.play().catch(error => console.error("Click sound error:", error));

                const selectedBox = document.querySelector('.quiz-box.selected');
                if (selectedBox) {
                    const quizId = selectedBox.dataset.quizId;
                    window.location.href = `countdown.php?quizId=${quizId}`;
                } else {
                    showWarning('Please select a quiz first!');
                }
            });

            fetchQuizzes(selectedCategory);
            changeAudio(selectedCategory);
        });

        document.addEventListener("DOMContentLoaded", function () {
            const dropdown = document.getElementById("category-dropdown");

            dropdown.value = "Category List";
            sessionStorage.removeItem("selectedCategory"); 

            dropdown.addEventListener("change", function () {
                sessionStorage.setItem("selectedCategory", this.value);
            });

            window.addEventListener("pageshow", function (event) {
                if (event.persisted || performance.navigation.type === 2) {
                    dropdown.value = "Category List"; 
                    sessionStorage.removeItem("selectedCategory"); 
                }
            });
        });

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
                iconElement.src = 'Icon/show.png'; 
            } else {
                passwordField.type = 'password';
                iconElement.src = 'Icon/hide.png';
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
            window.location.href = 'user_login.php'; 
        }

        document.getElementById('volumeSlider').addEventListener('input', function() {
            const volume = this.value;
            console.log('Volume:', volume);
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
