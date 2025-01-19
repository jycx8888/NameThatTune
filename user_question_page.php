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

    </style>
</head>
<body>
    <div id="loading">3</div>

    <div id="header">
        <h1>NameThatTune</h1>
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
                <span id="current-question">1/15</span>
            </div>
            <img id="question-image" src="Korean Song Photo/Blackpink.png" alt="Question Image" class="question-image">
            <h2 id="question">What song is this?</h2>

            <!-- Add the audio bar -->
            <audio id="question-audio" controls autoplay>
                <source id="audio-source" src="Background Audio/HARU HARU.mp3" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>

            <div class="options">
                <button class="option-button" onclick="selectOption('A')">A. Berlin</button>
                <button class="option-button" onclick="selectOption('B')">B. Madrid</button>
                <button class="option-button" onclick="selectOption('C')">C. Paris</button>
                <button class="option-button" onclick="selectOption('D')">D. Rome</button>
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

        function goBack() {
            if (window.history.length > 1) {
                // Go back if there's history
                window.history.back();
            } else {
                // Fallback to a default page if no history exists
                window.location.href = 'user_choose_category_page.php'; // Replace with your fallback URL
            }
        }

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

        document.addEventListener("DOMContentLoaded", () => {
        const questions = [
            {
                number: 1,
                questionText: "What song is this?",
                image: "Korean Song Photo/Blackpink.png",
                audio: "Background Audio/HARU HARU.mp3",
                options: ["A. Berlin", "B. Madrid", "C. Paris", "D. Rome"],
                correctAnswer: "C",
            },
            {
                number: 2,
                questionText: "Who is the artist of this song?",
                image: "Korean Song Photo/BTS.png",
                audio: "Background Audio/Dynamite.mp3",
                options: ["A. BTS", "B. BLACKPINK", "C. EXO", "D. TWICE"],
                correctAnswer: "A",
            },
        ];

        let currentQuestionIndex = 0;

        // Load the next question
        function loadNextQuestion() {
            if (currentQuestionIndex >= questions.length) {
                alert("Quiz completed!");
                return;
            }

            const currentQuestion = questions[currentQuestionIndex];

            // Update question number and text
            document.getElementById("question-number").textContent = currentQuestion.number;
            document.getElementById("current-question").textContent = `${currentQuestion.number}/${questions.length}`;
            document.getElementById("question").textContent = currentQuestion.questionText;

            // Update question image
            document.getElementById("question-image").src = currentQuestion.image;

            // Update audio source
            const audioSource = document.getElementById("audio-source");
            audioSource.src = currentQuestion.audio;
            document.getElementById("question-audio").load();

            // Update option buttons
            const optionButtons = document.querySelectorAll(".option-button");
            optionButtons.forEach((button, index) => {
                button.textContent = currentQuestion.options[index];
                button.style.backgroundColor = "rgb(77, 72, 144)";
                button.disabled = false;
            });

            currentQuestionIndex++;
        }

        // Handle option selection
        function selectOption(selectedOption) {
            const currentQuestion = questions[currentQuestionIndex - 1]; // Get the last loaded question
            const buttons = document.querySelectorAll(".option-button");

            buttons.forEach((button) => {
                button.disabled = true;

                // Highlight the correct answer
                if (button.textContent.trim().startsWith(currentQuestion.correctAnswer)) {
                    button.style.backgroundColor = "green";
                }

                // Highlight the incorrect selection
                if (button.textContent.trim().startsWith(selectedOption) && selectedOption !== currentQuestion.correctAnswer) {
                    button.style.backgroundColor = "red";
                }
            });

            // Delay before loading the next question
            setTimeout(loadNextQuestion, 2000);
        }

        // Initialize the quiz
        loadNextQuestion();

        // Adjust volume
        const volumeSlider = document.getElementById("volume-slider");
        const questionAudio = document.getElementById("question-audio");
        volumeSlider.addEventListener("input", (e) => {
            questionAudio.volume = e.target.value / 100;
        });

        // Hover sound logic (optional)
        const hoverSound = new Audio('Sound Effect/hover_sound_effect.mp3');
        document.querySelectorAll(".option-button").forEach((button) => {
            button.addEventListener("mouseover", () => {
                hoverSound.play().catch(() => {
                    // Ignore autoplay restrictions
                });
            });
        });
    });

    </script>
</body>
</html>
