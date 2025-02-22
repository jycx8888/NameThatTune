<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
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


$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    
    $profile_picture_path = 'Icon/account.png'; 
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    

    if (isset($_POST['options']) && isset($_POST['quizName'])) {
        $genreID = $_POST['options'];
        $quizName = $_POST['quizName'];
    }



$result = $conn->query("SELECT QuizID FROM quiz ORDER BY QuizID DESC LIMIT 1");
$lastQuizID = $result->fetch_assoc()['QuizID'];
$newQuizID = 'Q' . str_pad((int)substr($lastQuizID, 1) + 1, 3, '0', STR_PAD_LEFT);


$sql = "INSERT INTO quiz (QuizID, GenreID, CreatedTime, QuizName) VALUES ('$newQuizID', '$genreID', NOW(),'$quizName')";

if ($conn->query($sql) == TRUE) {
    echo "<script>alert('New quiz added successfully.');</script>";
} else {
    echo "<script>alert('Error: " . $sql . "<br>" . $conn->error . "');</script>";
}

$adminID = $_SESSION['adminID'];
$sql = "INSERT INTO admin_quiz (AdminID, QuizID) VALUES ('$adminID', '$newQuizID')";
$conn->query($sql);


$result = $conn->query("SELECT QuestionID FROM question ORDER BY QuestionID DESC LIMIT 1");
$lastQuestionID = $result->fetch_assoc()['QuestionID'];
$lastQuestionNo = (int)substr($lastQuestionID, 1);


for ($question = 1; $question <= 5; $question++) {
    $newQuestionID = 'T' . str_pad(++$lastQuestionNo, 3, '0', STR_PAD_LEFT);
    $songName = $_COOKIE['question' . $question . 'SongName'];

    $sql = "INSERT INTO question (QuestionID, CorrectRate, QuizID, CorrectAnswer, TotalAttempts) VALUES ('$newQuestionID', 0, '$newQuizID', '$songName', 0)";
    $conn->query($sql);

    $songID = $_COOKIE['question' . $question . 'SongID'];
    $sql = "UPDATE song SET QuestionID = '$newQuestionID' WHERE SongID = '$songID'";
    $conn->query($sql);

    
    $options = $_COOKIE['question' . $question . 'Options'];
    $options = explode(', ', $options);

    $result = $conn->query("SELECT OptionID FROM option ORDER BY OptionID DESC LIMIT 1");
    $lastOptionID = $result->fetch_assoc()['OptionID'];
    $lastOptionNo = (int)substr($lastOptionID, 1);

    for ($i = 0; $i < count($options); $i++) {
        $option = $options[$i];
        $newOptionID = 'O' . str_pad(++$lastOptionNo, 3, '0', STR_PAD_LEFT);
        $sql = "INSERT INTO option (OptionID, OptionName, QuestionID) VALUES ('$newOptionID', '$option', '$newQuestionID')";
        $conn->query($sql);
    }
}
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
            padding: clamp(2px, 1vw, 6px);
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
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            display: none;
            position: fixed;
            background-color: white;
            z-index: 1000;
            width: 40vw;
            min-width: 380px;
            height: 50vh;
            padding: 12px;
            border-radius: 15px;
            align-items: center;
            justify-content: center;
            box-shadow: 0px 0px 8px rgba(0, 0, 0, 0.5);
        }

        #edit-question-container h2 {
            font-size: clamp(20px, 2vw, 28px);
            margin: 12px 0;
        }

        #edit-question-container select {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            padding: 4px;
            margin: 6px 0;
            width: 80%;
        }

        #edit-question-container select option {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
        }

        #edit-question-container label {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            align-items: center;
            width: 100%;
        }

        #edit-question-container input[type='text'] {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            margin: 4px 0;
            padding: 2px;
        }

        #edit-question-container input[type='button'] {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            padding: 8px 16px;
            border-radius: 5px;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 12px;
        }

        #save-edit-button {
            background-color: #4CAF50;
        }

        #save-edit-button:hover {
            background-color: #45a049;
        }

        #cancel-edit-button {
            background-color: red;
        }

        #cancel-edit-button:hover {
            background-color: darkred;
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
            <form action="admin_addQuizNew.php" method="post" enctype="multipart/form-data" onsubmit="return validateQuizForm()">
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
                        <th>Actions</th>
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
                        $sql = "SELECT SongID, SongName FROM song WHERE QuestionID IS NULL";
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

                <input id="save-edit-button" type="button" value="Save" onclick="saveQuestion()">
                <input id="cancel-edit-button" type="button" value="Cancel" onclick="document.getElementById('edit-question-container').style.display = 'none'">
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
            return false;
        }

        return true;
    }

    function checkAnswer() {
        const songName = document.getElementById('select-song').options[document.getElementById('select-song').selectedIndex].text;
        const option1 = document.getElementById('option1').value.trim();
        const option2 = document.getElementById('option2').value.trim();
        const option3 = document.getElementById('option3').value.trim();
        const option4 = document.getElementById('option4').value.trim();

        if (songName !== option1 && songName !== option2 && songName !== option3 && songName !== option4) {
            alert('The song name must match one of the options.');
            return false;
        }

        return true;
    }


    function saveQuestion() {
        if (!validateEditQuestionForm() || !checkAnswer()) {
            return;
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

        setCookie('selectedSongs', selectedSongs.join(','), 0.1);
        setCookie('quizName', document.getElementById('quizName').value, 0.1);
        setCookie('quizCategory', document.getElementById('options').value, 0.1);
        setCookie('question' + rowIndex + 'SongID', songID, 0.1);
        setCookie('question' + rowIndex + 'SongName', songName, 0.1);
        setCookie('question' + rowIndex + 'Options', options, 0.1);
    }

    function validateQuizForm() {
        const table = document.getElementById('questions-table');
        const rows = table.getElementsByTagName('tr');
        let allRowsFilled = true;

        
        for (let i = 1; i <rows.length; i++) {
            const cells = rows[i].getElementsByTagName('td');
            if (cells[1].textContent.trim() === '' || cells[2].textContent.trim() === '' || cells[3].textContent.trim() === '') {
                allRowsFilled = false;
                break;
            }
        }

        if (!allRowsFilled) {
            alert('Please ensure all questions are filled out.');
            return false; 
        }

        return true; 
    }

    function setCookie(name, value, days) {
        const date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        let expires = "expires=" + date.toUTCString();
        document.cookie = `${name}=${value}; ${expires}; path=/`;
    }
    
</script>
</html>