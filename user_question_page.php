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
    <link rel="stylesheet" href="user_header_footer.css">
    <style>

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgb(77, 72, 144);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Lalezar", system-ui;
            font-size: 3rem;
            font-weight: 1000;
            z-index: 1000;
        }

        #loginOrRegister {
            width: auto;
            height: 60px;
            background-color: white;
            border-radius: 15px;
            display: flex;
            align-items: center;
            padding: 0 10px;
            cursor: pointer;
        }

        #loginOrRegister img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 10px;
        }
        
        #username {
            font-size: 16px;
            color: black;
            font-weight: bold;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-style: normal;
        }

        #main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .question-box {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .question-box h2 {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            color: rgb(77, 72, 144);
        }

        .options {
            margin-top: 20px;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .option-button {
            background-color: rgb(77, 72, 144);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .option-button:hover {
            background-color: rgb(104, 99, 174);
            transform: scale(1.05);
        }

        .option-button:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .option-button.disabled-hover:hover {
            background-color: rgb(77, 72, 144);
            transform: none;
            cursor: not-allowed;
        }

    </style>
</head>
<body>
    <div id="loading">3</div>

    <div id="header">
        <h1>NameThatTune</h1>
        <div id="loginOrRegister" onclick="">
            <img src="icon/account.png" alt="avatar">
            <span id="username">Username</span>
        </div>
    </div>

    <div id="main">
        <div class="question-box">
            <img id="question-image" src="images/question1.jpg" alt="Question Image" style="max-width: 100%; border-radius: 15px;">
            <h2 id="question">What is the capital of France?</h2>
            <div class="options">
                <button class="option-button" onclick="selectOption('A')">A. Berlin</button>
                <button class="option-button" onclick="selectOption('B')">B. Madrid</button>
                <button class="option-button" onclick="selectOption('C')">C. Paris</button>
                <button class="option-button" onclick="selectOption('D')">D. Rome</button>
            </div>
        </div>
    </div>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="icon/facebook.png" alt="facebook" id="facebook">&nbsp;
                <img src="icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

    <script>
        // Add the sound effect for hovering over the buttons
        const hoverSound = new Audio('Sound Effect/hover_sound_effect.mp3');  // Replace with the actual path to your sound file

        // Add event listener to play sound on hover
        document.querySelectorAll('.option-button').forEach(button => {
            button.addEventListener('mouseover', () => {
                hoverSound.play();
            });
        });

        let countdown = 3;

        function updateCountdown() {
            document.getElementById('loading').textContent = countdown;
            if (countdown > 1) {
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                setTimeout(() => {
                    document.getElementById('loading').style.display = 'none';
                    document.getElementById('header').style.display = 'flex';
                    document.getElementById('main').style.display = 'flex';
                    document.getElementById('footer').style.display = 'flex';
                }, 1000);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            updateCountdown();
        });

        function selectOption(answer) {
            const correctAnswer = "C"; // Set the correct answer
            const buttons = document.querySelectorAll(".option-button");

            // Disable all buttons after selecting an answer and disable hover effect
            buttons.forEach(button => {
                button.disabled = true;
                button.classList.add('disabled-hover');
            });

            // Highlight the selected answer and indicate if it is correct or wrong
            buttons.forEach(button => {
                if (button.textContent.startsWith(answer)) {
                    if (answer === correctAnswer) {
                        button.style.backgroundColor = "green";
                    } else {
                        button.style.backgroundColor = "red";
                    }
                }
            });

            // Highlight the correct answer
            buttons.forEach(button => {
                if (button.textContent.startsWith(correctAnswer)) {
                    button.style.backgroundColor = "green";
                }
            });

            // Add a delay and then load the next question
            setTimeout(loadNextQuestion, 2000);
        }


        function loadNextQuestion() {
            // Simulate loading a new question (you can replace this with real question loading logic)
            const questionElement = document.getElementById("question");
            const buttons = document.querySelectorAll(".option-button");

            questionElement.textContent = "What is 2 + 2?";
            buttons[0].textContent = "A. 3";
            buttons[1].textContent = "B. 4";
            buttons[2].textContent = "C. 5";
            buttons[3].textContent = "D. 6";

            // Reset button styles and enable them
            buttons.forEach(button => {
                button.style.backgroundColor = "rgb(77, 72, 144)";
                button.disabled = false;
            });
        }

        let currentQuestionIndex = 0;
        const questions = [
            {
                question: "What is the capital of France?",
                options: ["A. Berlin", "B. Madrid", "C. Paris", "D. Rome"],
                correct: "C",
                image: "images/question1.jpg"
            },
            {
                question: "What is 2 + 2?",
                options: ["A. 3", "B. 4", "C. 5", "D. 6"],
                correct: "B",
                image: "images/question2.jpg"
            }
            // Add more questions as needed
        ];

        function loadNextQuestion() {
            currentQuestionIndex++;
            if (currentQuestionIndex < questions.length) {
                const currentQuestion = questions[currentQuestionIndex];
                document.getElementById("question").textContent = currentQuestion.question;
                document.getElementById("question-number").textContent = `Question ${currentQuestionIndex + 1}/${questions.length}`;
                document.getElementById("question-image").src = currentQuestion.image;
                const buttons = document.querySelectorAll(".option-button");

                buttons.forEach((button, index) => {
                    button.textContent = currentQuestion.options[index];
                    button.style.backgroundColor = "rgb(77, 72, 144)";
                    button.disabled = false;
                    button.classList.remove('disabled-hover');
                });
            } else {
                alert("Quiz Finished!");
            }
        }

        // Update selectOption function to use the current question's correct answer
        function selectOption(answer) {
            const currentQuestion = questions[currentQuestionIndex];
            const correctAnswer = currentQuestion.correct;
            // Your existing selectOption code
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
