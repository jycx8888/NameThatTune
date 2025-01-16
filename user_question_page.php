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

        #header {
            position: relative;
            z-index: 1000;
        }

        #backButton:hover {
            background-color: rgb(104, 99, 174);
            transform: scale(1.05);
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

        function goBack() {
            window.history.back();
        }

    </script>
</body>
</html>
