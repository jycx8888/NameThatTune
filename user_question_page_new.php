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

if (!isset($_GET['quizId'])) {
    die("Quiz ID not provided.");
}

$quizId = $_GET['quizId'];

// Fetch questions for the selected quiz
$stmt = $conn->prepare("SELECT * FROM question WHERE QuizID = ?");
$stmt->bind_param("s", $quizId);
$stmt->execute();
$questionsResult = $stmt->get_result();

$questions = [];
while ($row = $questionsResult->fetch_assoc()) {
    // Fetch song details for each question
    $songStmt = $conn->prepare("SELECT * FROM song WHERE QuestionID = ?");
    $songStmt->bind_param("s", $row['QuestionID']);
    $songStmt->execute();
    $songResult = $songStmt->get_result();
    $song = $songResult->fetch_assoc();
    $songStmt->close();

    // Fetch options for each question
    $optionStmt = $conn->prepare("SELECT * FROM option WHERE QuestionID = ?");
    $optionStmt->bind_param("s", $row['QuestionID']);
    $optionStmt->execute();
    $optionsResult = $optionStmt->get_result();
    $options = [];
    while ($optionRow = $optionsResult->fetch_assoc()) {
        $options[] = $optionRow;
    }
    $optionStmt->close();

    $row['song'] = $song;
    $row['options'] = $options;
    $questions[] = $row;
}

$stmt->close();
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

        #loading {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #9370db;
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
            position: relative;
        }

        .question-image {
            max-width: 100%; /* Ensure the image doesn't overflow its container */
            height: auto; /* Maintain aspect ratio */
            border-radius: 20px; /* Adjust the radius as desired */
            margin-bottom: 20px; /* Add spacing between the image and the question text */
            display: block; /* Center the image horizontally */
            margin-left: auto;
            margin-right: auto;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5); /* Optional: Add shadow for better appearance */
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
            background-color: gray; /* Clear visual feedback for disabled state */
            color: white;
            cursor: not-allowed;
        }

        .option-button.disabled-hover:hover {
            background-color: rgb(77, 72, 144);
            transform: none;
            cursor: not-allowed;
        }

        #header {
            position: relative;
            z-index: 1000;
        }

        #backButton {
            background-color: rgb(77, 72, 144);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            cursor: pointer;
            position: absolute;
            top: 100px; /* Adjust this value to move the button down as needed */
            left: 50px; /* Adjust this value to move the button horizontally as needed */
            transition: background-color 0.3s, transform 0.2s;
            z-index: 1001; /* Ensure it appears above other elements */
        }

        #backButton:hover {
            background-color: rgb(104, 99, 174);
            transform: scale(1.05);
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

        audio {
            width: 100%; /* Makes the audio bar take the full width of the container */
            margin: 10px 0; /* Adds some space above and below the audio bar */
            outline: none; /* Removes the default outline */
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-family: "Lalezar", system-ui;
            font-weight: bold;
            color: rgb(77, 72, 144);
        }

        #question-number {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: 1.2rem;
            margin-left: 10px; /* Adjust spacing as needed */
        }

        #current-question {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: 1.2rem;
            margin-right: 10px; /* Adjust spacing as needed */
        }

        .next-button-container {
            display: flex;
            justify-content: center; /* Center horizontally */
            align-items: center; /* Center vertically */
            margin-top: 20px; /* Add spacing if needed */
            height: 100px; /* Adjust height as needed */
        }

        #next-button {
            background-color: gray;
            color: white;
            padding: 15px 30px; /* Increase padding for a larger button */
            font-size: 18px; /* Increase font size */
            border: none;
            border-radius: 10px; /* Slightly round edges */
            cursor: not-allowed;
            transition: background-color 0.3s;
            display: inline-block; /* Ensure button stays inline */
        }

        #next-button:enabled {
            background-color: rgb(77, 72, 144);
            cursor: pointer;
        }

        #next-button:hover:enabled {
            background-color: rgb(104, 99, 174);
        }

    </style>
</head>
<body>
    <div id="loading">3</div>

    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <button id="backButton" onclick="goBack()">Back</button>
        <div id="loginOrRegister" onclick="">
            <img src="icon/account.png" alt="avatar">
            <span id="username">Username</span>
        </div>
    </div>

    <div id="main">
        <div class="question-box">
            <div class="question-header">
                <span id="question-number">1</span>
                <span id="current-question">1/5</span>
            </div>
            <img id="question-image" src="" alt="Question Image" class="question-image">
            <h2 id="question">What song is this?</h2>

            <!-- Add the audio bar -->
            <audio id="question-audio" controls autoplay>
                <source id="audio-source" src="" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>

            <div class="options">
                <button class="option-button" onclick="selectOption('A')"></button>
                <button class="option-button" onclick="selectOption('B')"></button>
                <button class="option-button" onclick="selectOption('C')"></button>
                <button class="option-button" onclick="selectOption('D')"></button>
            </div>
        </div>
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

    <!-- Add the Next button -->
    <div class="next-button-container">
        <button id="next-button" onclick="loadNextQuestion()" disabled>Next</button>
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                // Go back if there's history
                window.history.back();
            } else {
                // Fallback to a default page if no history exists
                window.location.href = 'user_choose_category_new.php'; // Replace with your fallback URL
            }
        }

        const questions = <?php echo json_encode($questions); ?>;
        let currentQuestionIndex = 0;

        function loadNextQuestion() {
            if (currentQuestionIndex >= questions.length) {
                alert("Quiz completed!");
                return;
            }

            const currentQuestion = questions[currentQuestionIndex];

            document.getElementById("question-number").textContent = currentQuestionIndex + 1;
            document.getElementById("current-question").textContent = `${currentQuestionIndex + 1}/${questions.length}`;
            document.getElementById("question").textContent = currentQuestion.QuestionText;
            document.getElementById("question-image").src = currentQuestion.song.images;
            document.getElementById("audio-source").src = currentQuestion.song.SongAudio;
            document.getElementById("question-audio").load();

            const optionButtons = document.querySelectorAll(".option-button");
            optionButtons.forEach((button, index) => {
                button.textContent = currentQuestion.options[index].OptionName;
                button.style.backgroundColor = "rgb(77, 72, 144)";
                button.disabled = false;
            });

            const nextButton = document.getElementById("next-button");
            nextButton.disabled = true;
            nextButton.style.backgroundColor = "black";

            currentQuestionIndex++;
        }

        // Preload sound effects
        const clickSound = new Audio('Sound Effect/click_sound_effect.wav'); 
        const correctSound = new Audio('Sound Effect/Correct_Answer.mp3'); 
        const incorrectSound = new Audio('Sound Effect/Incorrect_Answer.mp3'); 
        const hoverSound = new Audio('Sound Effect/hover_sound_effect.mp3');  // Replace with the actual path to your sound file

        // Ensure sounds are loaded properly before playing
        clickSound.load();
        correctSound.load();
        incorrectSound.load();
        hoverSound.load();

        // Add click sound to option buttons
        document.querySelectorAll('.option-button').forEach(button => {
            button.addEventListener('click', () => {
                clickSound.currentTime = 0; // Reset so it plays every time
                clickSound.play().catch(error => console.log("Audio playback error:", error));
            });

            // Add event listener to play sound on hover
            button.addEventListener('mouseover', () => {
                hoverSound.play();
            });
        });

        // Function to handle selecting an option
        function selectOption(selectedOption) {
            const currentQuestion = questions[currentQuestionIndex - 1];
            const isCorrect = selectedOption === currentQuestion.CorrectAnswer;

            // Play correct or incorrect sound
            if (isCorrect) {
                correctSound.currentTime = 0;
                correctSound.play().catch(error => console.log("Audio playback error:", error));
            } else {
                incorrectSound.currentTime = 0;
                incorrectSound.play().catch(error => console.log("Audio playback error:", error));
            }

            // Disable all buttons after selection
            document.querySelectorAll('.option-button').forEach(button => {
                button.disabled = true;
                if (button.textContent.trim().startsWith(currentQuestion.CorrectAnswer)) {
                    button.style.backgroundColor = "green";
                }
                if (button.textContent.trim().startsWith(selectedOption) && !isCorrect) {
                    button.style.backgroundColor = "red";
                }
            });

            // Enable next button
            document.getElementById("next-button").disabled = false;
            document.getElementById("next-button").style.backgroundColor = "rgb(77, 72, 144)";
        }

        function updateCountdown() {
            const loadingElement = document.getElementById('loading');
            loadingElement.textContent = countdown;
            if (countdown > 0) {
                countdown--;
                setTimeout(updateCountdown, 1000);
            } else {
                loadingElement.style.display = 'none';
                document.getElementById('header').style.display = 'flex';
                document.getElementById('main').style.display = 'flex';
                document.getElementById('controls').style.display = 'flex';
                document.querySelector('.next-button-container').style.display = 'flex';
                document.getElementById('footer').style.display = 'flex';
            }
        }
        
        let countdown = 3;
        document.addEventListener("DOMContentLoaded", () => {
            updateCountdown();
            loadNextQuestion();
        });

        const volumeSlider = document.getElementById("volume-slider");
        const questionAudio = document.getElementById("question-audio");

        volumeSlider.addEventListener("input", (e) => {
            questionAudio.volume = e.target.value / 100;
        });

        function adjustVolume(value) {
            questionAudio.volume = value / 100; // Adjust the audio volume
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
    </script>
</body>
</html>