<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
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
            display: flex;
            justify-content: center;
        }

        #add-quiz-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 58px;
            width: 80vw;
            height: fit-content;
            min-height: 50vh;
            background-color: white;
        }

        #questions-table {
            width: 80%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        #questions-table th, #questions-table td { 
            background-color: white;
            border: 1px solid black;
            text-align: left;
            padding: 8px;
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
                            echo "<td><input type='button' value='Edit' onclick='editQuestion(this.parentElement.parentElement)'><br>
                            <input type='button' value='Delete' onclick='removeQuestion(this.parentElement.parentElement)'><br>
                            </td>";
                            echo "</tr>";
                        }
                    ?>
                </table>
                <input type="submit" value="Add Quiz">
                <input type="button" value="Cancel" onclick="window.location.href='admin_quiz_management.php'">
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

        
    }
</script>
</html>