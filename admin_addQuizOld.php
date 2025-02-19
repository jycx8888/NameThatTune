<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
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
$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <style>
        #content {
            font-family: 'Lalezar', system-ui;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #add-quiz-container {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            align-items: center;
            margin-top: 48px;
            margin-bottom: 48px;
            width: 80vw;
            height: fit-content;
            min-height: 50vh;
            background-color: white;
            border-radius: 15px;
            padding: 24px;
        }

        #add-quiz-container h2 {
            font-size: clamp(24px, 2.5vw, 28px);
            margin: 12px 0 24px 0;
        }

        #add-quiz-container form {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            align-items: center;
            width: 100%;
        }

        #add-quiz-container input[type='text'] {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            margin: 6px 0;
            padding: 2px;
        }

        #options {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            padding: 4px;
            margin: 6px 0;
            width: clamp(120px, 40%, 200px);
    
        }

        #options option {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
        }

        #questions-table {
            justify-self: center;
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #questions-table th, #questions-table td {
            text-align: center;
            border: 1px solid black;
            border-collapse: collapse;
            padding: 8px;
            font-size: (14px, 1.5vw, 16px);
        }

        #questions-table th {
            font-weight: 700;
            background-color: #584cba;
            color: white;
        }

        #questions-table td {
            background-color: white;
            align-items: center;
            justify-content: center;
        }

        td input[type='button'] {
            display: inline-block;
            font-family: 'Lalezar', system-ui;
            font-size: clamp(12px, 1.5vw, 16px);
            font-weight: 500;
            padding: 6px;
            margin: 2px;
            border-radius: 5px;
            background-color: #584cba;
            color: white;
            border: none;
            cursor: pointer;
        }

        td input[type='button']:hover {
            background-color: #17066e;
        }

        #add-quiz-button {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            padding: clamp(10px, 1vw, 12px) 24px;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 12px;
        }

        #add-quiz-button:hover {
            background-color: #45a049;
        }

        #cancel-button {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            padding: clamp(10px, 1vw, 12px) 24px;
            border-radius: 5px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 12px;
        }

        #cancel-button:hover {
            background-color: darkred;
        }

        #edit-question-container {
            display: none;
            position: fixed;
            background-color: white;
            z-index: 1000;
            width: fit-content;
            height: fit-content;
            padding: 12px;
            border-radius: 15px;
        }

        

    </style>
</head>

<body>
    <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div id='content'>
        <div id='add-quiz-container'>
            <h2>Add New Quiz</h2>
            <form action="admin_addQuizNew.php" method="post" enctype="multipart/form-data" onsubmit="return addQuiz()">
                <label for="quizName">Quiz Name:</label>
                <input type="text" id="quizName" name="quizName" required><br>
                <label for="quizImage">Quiz Category:</label>
                <select id="options" name="options" required >
                    <option value="">Select a category</option>

                    <?php
                    $sql = "SELECT GenreID, GenreName FROM genre";
                    $result1 = mysqli_query($conn, $sql);
                    while ($row1 = mysqli_fetch_assoc($result1)) {
                        $genreID = $row1['GenreID'];
                        $genreName = $row1['GenreName'];
                        echo "<option value='$genreID'>$genreName</option>";
                    }
                    ?>

                </select><br>
                <table id='questions-table'>
                    <tr>
                        <th>No.</th>
                        <th>Song ID</th>
                        <th>Song Name</th>
                        <th>Options</th>
                        <th>Action</th>
                    </tr>
                    <?php
                        for ($question = 1; $question <= 5; $question++) {
                            echo "<tr>";
                            echo "<td>$question</td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td></td>";
                            echo "<td><input type='button' value='Edit' onclick='editQuestion(this.parentElement.parentElement)'>
                            <input type='button' value='Delete' onclick='removeQuestion(this.parentElement.parentElement)'><br>
                            </td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
                <input type="submit" value="Add Quiz" id="add-quiz-button">
                <input type="button" value="Cancel" onclick="window.location.href='admin_quiz_management.php'" id="cancel-button">
            </form>
        </div>

        <div id='edit-question-container'>
            <form id="edit-question-form" action="post" onsubmit="saveQuestion()">
                <h2>Edit Question</h2>
                <select id="select-song" name="select-song" required >
                    <option value="" id='select-song'>Select song</option>

                        <?php 
                        $sql = "SELECT SongID, SongName FROM song WHERE QuestionID IS  NULL";
                        $result1 = mysqli_query($conn, $sql);

                        while ($row1 = mysqli_fetch_assoc($result1)) {
                            $songID = $row1['SongID'];
                            $songName = $row1['SongName'];
                            echo "<option value='$songID'>$songName</option>";
                        }
                        ?>

                </select><br>
                    
                <label for="option1">Option 1:</label>
                <input type="text" id="option1" name="option1" required><br>
                <label for="option2">Option 2:</label>
                <input type="text" id="option2" name="option2" required><br>
                <label for="option3">Option 3:</label>
                <input type="text" id="option3" name="option3" required><br>
                <label for="option4">Option 4:</label>
                <input type="text" id="option4" name="option4" required><br>

                <input type="button" value="Save" onclick="saveQuestion()">
                <input type="button" value="Cancel" onclick="document.getElementById('edit-question-container').style.display = 'none'">
            </form>
        </div>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>
</body>

<script>
    selectedSongs = [];

    function editQuestion(row) {
        document.getElementById('edit-question-container').style.display = 'block';

        songID = row.cells[1].textContent;
        songName = row.cells[2].textContent;
        options = row.cells[3].textContent.split(', ');

        document.getElementById('select-song').value = songID;
        document.getElementById('option1').value = options[0] || '';
        document.getElementById('option2').value = options[1] || '';
        document.getElementById('option3').value = options[2] || '';
        document.getElementById('option4').value = options[3] || '';

        // Store the row index in a hidden input field
        document.getElementById('edit-question-container').dataset.rowIndex = row.rowIndex;
    }

    function updateSelectOptions() {
        const select = document.getElementById('select-song');
        const options = select.getElementsByTagName('option');

        for (let i = 0; i < options.length; i++) {
            const option = options[i];
            if (selectedSongs.includes(option.value)) {
                option.style.display = 'none';
            } else {
             option.style.display = 'block';
            }
        }
    }

    function removeQuestion(row) {
        const songID = row.cells[1].textContent.trim();
        if (songID) {
            const index = selectedSongs.indexOf(songID);
            if (index > -1) {
                selectedSongs.splice(index, 1);
            }
        }

        for (let i = 1; i < (row.cells.length - 1); i++) {
            row.cells[i].innerHTML = '';
        }

        updateSelectOptions();
    }

    function validateEditQuestionForm() {
        const songID = document.getElementById('select-song').value;
        const option1 = document.getElementById('option1').value.trim();
        const option2 = document.getElementById('option2').value.trim();
        const option3 = document.getElementById('option3').value.trim();
        const option4 = document.getElementById('option4').value.trim();

        if (songID === '' || option1 === '' || option2 === '' || option3 === '' || option4 === '') {
            alert('Please fill out all fields in the edit question form.');
            return false; // Prevent saving the question
        }

        return true; // Allow saving the question
    }

    function checkAnswer() {
        const songName = document.getElementById('select-song').options[document.getElementById('select-song').selectedIndex].text;
        const option1 = document.getElementById('option1').value.trim();
        const option2 = document.getElementById('option2').value.trim();
        const option3 = document.getElementById('option3').value.trim();
        const option4 = document.getElementById('option4').value.trim();

        if (songName !== option1 && songName !== option2 && songName !== option3 && songName !== option4) {
            alert('The song name must match one of the options.');
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }


    function saveQuestion() {
        if (!validateEditQuestionForm() || !checkAnswer()) {
            return; // Prevent saving if validation fails
        }

        const songID = document.getElementById('select-song').value;
        const songName = document.getElementById('select-song').options[document.getElementById('select-song').selectedIndex].text;

        const rowIndex = document.getElementById('edit-question-container').dataset.rowIndex;
        const row = document.getElementById('questions-table').rows[rowIndex];

        const previousSongID = row.cells[1].textContent.trim();
        if (previousSongID) {
            const index = selectedSongs.indexOf(previousSongID);
            if (index > -1) {
                selectedSongs.splice(index, 1);
            }
        }

        selectedSongs.push(songID);

        row.cells[1].innerHTML = songID;
        row.cells[2].innerHTML = songName;

        const option1 = document.getElementById('option1').value;
        const option2 = document.getElementById('option2').value;
        const option3 = document.getElementById('option3').value;
        const option4 = document.getElementById('option4').value;
        const options = option1 + ', ' + option2 + ', ' + option3 + ', ' + option4;

        row.cells[3].innerHTML = options;
        document.getElementById('edit-question-container').style.display = 'none';

        updateSelectOptions()
    }

    function validateQuizForm() {
        const table = document.getElementById('questions-table');
        const rows = table.getElementsByTagName('tr');
        let allRowsFilled = true;

        // Check if all rows (excluding the header row) contain data
        for (let i = 1; i < rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells[1].textContent.trim() === '' || cells[2].textContent.trim() === '' || cells[3].textContent.trim() === '') {
                allRowsFilled = false;
                break;
            }
        }

        if (!allRowsFilled) {
            alert('Please ensure all questions are filled out.');
            return false; // Prevent form submission
        }

        return true; // Allow form submission
    }









    function addQuiz() {
        if (!validateQuizForm()) {
            return;
        }

        // Add 1 quiz to quiz table
        // Create new QuizID
        // Link quiz to genre ID
        // Get created time
        // Set quiz name

        quizName = document.getElementById('quizName').value;
        genreID = document.getElementById('options').value;
        
        <?php
            // Add quiz to quiz table
            $stmt = $conn->prepare("INSERT INTO quiz (QuizID, GenreID, CreatedTime, QuizName) VALUES (?, ?, NOW(), ?)");
            $stmt->bind_param("sss", $newQuizID, $genreID, $quizName);

            // Create new QuizID
            $sql = "SELECT QuizID FROM quiz ORDER BY QuizID DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $lastQuizID = mysqli_fetch_assoc($result)['QuizID'];
            $newQuizNo = ((int)substr($lastQuizID, 1)) + 1;

            if ($newQuizNo < 10) {
                $newQuizID = 'Q00' . $newQuizNo;
            } else if ($newQuizNo < 100) {
                $newQuizID = 'Q0' . $newQuizNo;
            } else if ($newQuizNo < 1000) {
                $newQuizID = 'Q' . $newQuizNo;
            }     

            $quizName = $_POST['quizName'];
            $genreID = $_POST['options'];
            $stmt->execute();
        ?>


        // Create new QuestionID
        // Set song name as correct answer
            
        // Add 5 questions to question table
            
        table = document.getElementById('questions-table');
        rows = table.getElementsByTagName('tr');
        songNameList = [];
        optionsList = [];
        questionList = [];

        // Get each question from the table
        for (let i = 1; i < rows.length; i++) {
            row = rows[i];

            questionList.push(row);

        }
            
        // Loop through each question
        for (question in questionList) {
            songId = question.cells[1].textContent;
            songName = question.cells[2].textContent;
            options = question.cells[3].textContent.split(', ');
            correctAnswer = songName;

            // Add question to question table
            <?php
            $stmt = $conn->prepare("INSERT INTO question (QuestionID, CorrectRate, QuizID, CorrectAnswer, TotalAttempts) VALUES (?, 0, ?, ?, 0)");
            $stmt->bind_param("sss", $newQuestionID, $newQuizID, $correctAnswer);

            // Create new QuestionID
            $sql = "SELECT QuestionID FROM question ORDER BY QuestionID DESC LIMIT 1";
            $result = mysqli_query($conn, $sql);
            $lastQuestionID = mysqli_fetch_assoc($result)['QuestionID'];
            $newQuestionNo = ((int)substr($lastQuestionID, 1)) + 1;

            if ($newQuestionNo < 10) {
                $newQuestionID = 'T00' . $newQuestionNo;
            } else if ($newQuestionNo < 100) {
                $newQuestionID = 'T0' . $newQuestionNo;
            } else if ($newQuestionNo < 1000) {
                $newQuestionID = 'T' . $newQuestionNo;
            }

            $stmt->execute();
            ?>


            // Add options to option table
            for (option in options) {
                <?php
                $stmt = $conn->prepare("INSERT INTO option (OptionID, OptionName, QuestionID) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $newOptionID, $option, $newQuestionID);

                // Create new OptionID
                $sql = "SELECT OptionID FROM option ORDER BY OptionID DESC LIMIT 1";
                $result = mysqli_query($conn, $sql);
                $lastOptionID = mysqli_fetch_assoc($result)['OptionID'];
                $newOptionNo = (int)substr($lastOptionID, 1) + 1;

                if ($newOptionNo < 10) {
                    $newOptionID = 'O00' . $newOptionNo;
                } else if ($newOptionNo < 100) {
                    $newOptionID = 'O0' . $newOptionNo;
                } else if ($newOptionNo < 1000) {
                    $newOptionID = 'O' . $newOptionNo;
                }
                ?>
            }

            
            // Update song table to include question ID
            <?php
                $stmt = $conn->prepare("UPDATE song SET QuestionID = ? WHERE SongID = ?");
                $stmt->bind_param("ss", $newQuestionID, $songID);
                $stmt->execute();
            ?>
        
        }


    }
</script>
</html>