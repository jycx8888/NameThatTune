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

// Set the character set to UTF-8
$conn->set_charset("utf8");

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

    // Convert BLOB data to base64
    if ($song) {
        $song['SongImage'] = 'data:image/jpeg;base64,' . base64_encode($song['SongImage']);
        $song['SongAudio'] = 'data:audio/mpeg;base64,' . base64_encode($song['SongAudio']);
    }

    // Fetch options for each question
    $optionStmt = $conn->prepare("SELECT * FROM option WHERE QuestionID = ?");
    $optionStmt->bind_param("s", $row['QuestionID']);
    $optionStmt->execute();
    $optionsResult = $optionStmt->get_result();
    $options = [];
    while ($optionRow = $optionsResult->fetch_assoc()) {
        $options[] = $optionRow['OptionName'];
    }
    $optionStmt->close();

    $row['song'] = $song;
    $row['options'] = $options;
    $questions[] = $row;
}

$stmt->close();
$conn->close();

// Debugging: Check if $questions is populated
if (empty($questions)) {
    die("No questions found for the provided Quiz ID.");
}

// Convert data to UTF-8 using mb_convert_encoding
function convertToUtf8($data) {
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = convertToUtf8($value);
        }
    } else if (is_string($data)) {
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    return $data;
}

$questions = convertToUtf8($questions);

// Check for JSON encoding errors
$jsonQuestions = json_encode($questions,JSON_UNESCAPED_UNICODE);
if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON encoding error: " . json_last_error_msg());
}
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
            justify-content: center;
            height: 100%;
        }

        .question-box {
            background-color: white;
            border-radius: 15px;
            padding: 20px;
            max-width: 600px;
            width: 100%;
            text-align: center;
            align-self: center;
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

        #backButton {
            background-color: rgb(77, 72, 144);
            color: white;
            font-size: 1rem;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            margin: 24px;
            cursor: pointer;
            align-self: start;
            transition: background-color 0.3s, transform 0.2s;
            z-index: 1001; /* Ensure it appears above other elements */
        }

        #backButton:hover {
            background-color: rgb(104, 99, 174);
            transform: scale(1.05);
        }

        /* Volume and Fullscreen Controls */
        #controls {
            width: 100%; /* Full width of the screen */
            display: flex; /* Align buttons */
            justify-content: space-between; /* Align one button to the left and the other to the right */
            align-items: center;
            background-color: transparent; /* Optional: Makes the background transparent */
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
            position: sticky; /* Ensures it stays in place */
            margin-right: 24px;
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
            margin-top: -100px; /* Add spacing if needed */
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
            display: block; /* Ensure button stays inline */
            width: fit-content;
            justify-self: center;
        }

        #next-button:enabled {
            background-color: rgb(77, 72, 144);
            cursor: pointer;
        }

        #next-button:hover:enabled {
            background-color: rgb(104, 99, 174);
        }

        .blurred {
            filter: blur(10px);
        }

    </style>
</head>
<body>

    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <div id="login">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'user_hamburger_menu.php'; ?>

    <div id="content">
        <button id="backButton" onclick="goBack()">Back</button>
        <div class="question-box">
            <div class="question-header">
                <span id="question-number">1</span>
                <span id="current-question">1/5</span>
            </div>
            <img id="question-image" style='height: 200px; width:200px;' alt="Question Image" class="question-image blurred">
            <div><h2 id="question">What song is this?</h2></div>

            <!-- Add the audio bar -->
            <audio id="question-audio" autoplay>
                <source id="audio-source" src="" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            
            <div class="options">
                <button class="option-button" onclick="selectOption(this)"></button>
                <button class="option-button" onclick="selectOption(this)"></button>
                <button class="option-button" onclick="selectOption(this)"></button>
                <button class="option-button" onclick="selectOption(this)"></button>
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

        <button id="next-button" onclick="loadNextQuestion()" disabled>Next</button>
    </div>

    <script>
        function goBack() {
            if (window.history.length > 1) {
                window.location.href = 'user_choose_category_new.php'; // Replace with your fallback URL
            }
        }

        const questions = <?php echo $jsonQuestions; ?>;
        if (!questions || questions.length === 0) {
            alert("No questions found for the provided Quiz ID.");
        } 
        

        let currentQuestionIndex = 0;

        const quizId = <?php echo json_encode($quizId); ?>;
        const userId = <?php echo json_encode($_SESSION['user_id']); ?>;

        console.log("User ID:", userId); // Log the user ID to the console
        console.log("Quiz ID:", quizId); // Log the quiz ID to the console

        function loadNextQuestion() {
            if (currentQuestionIndex >= questions.length) {
                // Calculate the total time taken
                let endTime = new Date();
                let timeTaken = ((endTime - startTime) / 1000).toFixed(1); // Time in seconds

                // Save the record and record_question data to the database
                fetch('save_record.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        userId: userId,
                        quizId: quizId,
                        correctAnswersCount: correctAnswersCount,
                        totalQuestions: questions.length,
                        timeTaken: timeTaken,
                        userAnswers: userAnswers,
                        startTime: startTime.toISOString()
                    })
                })
                .then(response => response.text())
                .then(data => {
                    console.log(data); // Log the raw response
                    try {
                        const jsonData = JSON.parse(data); // Parse the JSON response
                        if (jsonData.success) {
                            // Redirect to the leaderboard page with the result and quiz ID
                            window.location.href = `user_leaderboard.php?result=${correctAnswersCount}&quizId=${quizId}`;
                        } else {
                            alert('Failed to save the record. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error parsing JSON:', error);
                        alert('An error occurred while saving the record. Please try again.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while saving the record.');
                });
        
                return;
            }

            document.getElementById("question-image").classList.add("blurred");

            const currentQuestion = questions[currentQuestionIndex];

            document.getElementById("question-number").textContent = currentQuestionIndex + 1;
            document.getElementById("current-question").textContent = `${currentQuestionIndex + 1}/${questions.length}`;
            document.getElementById("question").textContent = currentQuestion.QuestionText;
            document.getElementById("question-image").src = currentQuestion.song.SongImage;
            document.getElementById("audio-source").src = currentQuestion.song.SongAudio;
            document.getElementById("question-audio").load();

            // Shuffle the options
            shuffleArray(currentQuestion.options);

            const optionButtons = document.querySelectorAll(".option-button");
            optionButtons.forEach((button, index) => {
                button.textContent = currentQuestion.options[index];
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

        // Ensure sounds are loaded properly before playing
        clickSound.load();
        correctSound.load();
        incorrectSound.load();

        // Add click sound to option buttons
        document.querySelectorAll('.option-button').forEach(button => {
            button.addEventListener('click', () => {
                clickSound.currentTime = 0; // Reset so it plays every time
                clickSound.play().catch(error => console.log("Audio playback error:", error));
            });
        });

        function shuffleArray(array) {
            for (let i = array.length - 1; i > 0; i--) {
                const j = Math.floor(Math.random() * (i + 1));
                [array[i], array[j]] = [array[j], array[i]];
            }
        }

        let userAnswers = [];
        let correctAnswersCount = 0;
        let startTime = new Date();

        // Function to handle selecting an option
        function selectOption(button) {
            const currentQuestion = questions[currentQuestionIndex - 1];
            const selectedOption = button.textContent.trim();
            const isCorrect = selectedOption === currentQuestion.CorrectAnswer;

            // Track user's answer
            userAnswers.push({
                questionId: currentQuestion.QuestionID,
                userAnswer: selectedOption,
                isCorrect: isCorrect
            });
        
            // Increment correct answers count if the answer is correct
            if (isCorrect) {
                correctAnswersCount++;
            }

            // Play correct or incorrect sound
            if (isCorrect) {
                correctSound.currentTime = 0;
                correctSound.play().catch(error => console.log("Audio playback error:", error));
            } else {
                incorrectSound.currentTime = 0;
                incorrectSound.play().catch(error => console.log("Audio playback error:", error));
                
            }
            document.getElementById("question-image").classList.remove("blurred");
            

            // Disable all buttons after selection
            document.querySelectorAll('.option-button').forEach(button => {
                button.disabled = true;
                if (button.textContent.trim() === currentQuestion.CorrectAnswer) {
                    button.style.backgroundColor = "green";
                }
                if (button.textContent.trim() === selectedOption && !isCorrect) {
                    button.style.backgroundColor = "red";
                }
            });

            // Enable next button
            document.getElementById("next-button").disabled = false;
            document.getElementById("next-button").style.backgroundColor = "rgb(77, 72, 144)";
        }

        document.addEventListener("DOMContentLoaded", () => {
            loadNextQuestion();
        });

        const volumeSlider = document.getElementById("volume-slider");
        const questionAudio = document.getElementById("question-audio");

        volumeSlider.addEventListener("input", (e) => {
            questionAudio.volume = e.target.value / 100;
        });
        
        function adjustVolume(value) {
            let audioElement = document.getElementById("question-audio");
            if (audioElement) {
                audioElement.volume = value / 100;
                audioElement.muted = value == 0; // Mute if volume is 0
                console.log("Volume set to:", audioElement.volume, "Muted:", audioElement.muted);
            } else {
                console.log("Error: Audio element not found.");
            }
        }

        function toggleVolumeSlider() {
            let volumeSlider = document.getElementById("volume-slider");
            if (volumeSlider.style.display === "none" || volumeSlider.style.display === "") {
                volumeSlider.style.display = "block";
            } else {
                volumeSlider.style.display = "none";
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

        // Add the sound effect for hovering over the buttons
        const hoverSound = new Audio('Sound Effect/hover_sound_effect.mp3');  // Replace with the actual path to your sound file

        // Add event listener to play sound on hover
        document.querySelectorAll('.option-button').forEach(button => {
            button.addEventListener('mouseover', () => {
                hoverSound.play();
            });
        });

        document.addEventListener('DOMContentLoaded', () => {
            document.getElementById('question-audio').play();
        });

    </script>
</body>
</html>