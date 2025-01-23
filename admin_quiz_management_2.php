<?php
// Database connection
session_start();
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//echo "Connected successfully<br>";

// Check if 'quiz_id' is provided
if (!isset($_GET['quiz_id']) || empty($_GET['quiz_id'])) {
    die("Error: Quiz ID is required.");
}

// Sanitize and fetch the quiz ID
$quiz_id = $conn->real_escape_string($_GET['quiz_id']);

// Fetch quiz data
$sql = "SELECT * FROM quizzes WHERE quiz_id='$quiz_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc(); // Fetch data into an array
} else {
    die("Error: Quiz not found.");
}

// Fetch questions related to this quiz
$sql_questions = "SELECT * FROM questions WHERE quiz_id='$quiz_id'";
$result_questions = $conn->query($sql_questions);

// Handle form submission for updating quiz details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve updated data from the form
    $genre_id = $conn->real_escape_string($_POST['genre_id']);
    $created_time = $conn->real_escape_string($_POST['created_time']);

    // Update query
    $update_sql = "UPDATE quizzes SET genre_id='$genre_id', created_time='$created_time' WHERE quiz_id='$quiz_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Quiz updated successfully!<br>";
        // Reload the page to reflect changes
        header("Location: edit_quiz.php?quiz_id=$quiz_id");
        exit();
    } else {
        echo "Error updating quiz: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune</title>
    <link rel="stylesheet" href="user_header_footer.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        main {
            flex: 1;
            padding: 20px;
            background-color: #f0f0f0; /* Grey background for the form */
            border-radius: 10px;
        }

        .edit-quiz-header {
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input {
            width: 100%;
            padding: 8px;
            border-radius: 4px;
            border: none;
        }

        .question-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .question-table th, td {
            padding: 10px;
            text-align: left;
            border: solid black;
            color: black;
        }

        .question-table th {
            background-color: rgb(104, 99, 174);
            color: white;
        }

        .actions a {
            text-decoration: none;
            color: #ACD7EC;
            margin-right: 5px;
            font-weight: bold;
        }

        .actions a:hover {
            text-decoration: underline;
            color: blue;
        }

        .button-container {
            margin-top: 20px;
            text-align: right;
        }

        .button-container button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .button-container .cancel {
            background-color: gray;
            color: white;
        }

        .button-container .confirm {
            background-color: #98FB98;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Header Section -->
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="account.png" alt="avatar">
            <p>Username</p>
        </div>
    </div>

    <!-- Main Content Section -->
    <main>
        <form method="POST">
            <h2 class="edit-quiz-header">Edit Quiz</h2>
            <div class="form-group">
                <label for="quiz-id">Quiz ID</label>
                <input type="text" id="quiz-id" value="Q001">
            </div>
            <div class="form-group">
                <label for="quiz-name">Name</label>
                <input type="text" id="quiz-name" value="90s Classics">
            </div>
            <div class="form-group">
                <label for="category">Category</label>
                <input type="text" id="category" value="English">
            </div>

            <div class="form-group">
                <label for="genre-id">Genre</label>
                <select id="genre-id" name="genre_id">
                    <option value="1" <?php if ($quiz['genre_id'] == '1') echo 'selected'; ?>>English</option>
                    <option value="2" <?php if ($quiz['genre_id'] == '2') echo 'selected'; ?>>Korean</option>
                    <option value="3" <?php if ($quiz['genre_id'] == '3') echo 'selected'; ?>>Japanese</option>
                    <!-- Add more genres as needed -->
                </select>
            </div>

            <div class="form-group">
                <label for="created-time">Created Time</label>
                <input type="datetime-local" id="created-time" name="created_time" value="<?php echo date('Y-m-d\TH:i:s', strtotime($quiz['created_time'])); ?>">
            </div>

        <!-- Table Section -->
        <h3>Questions</h3>
<table class="question-table">
    <thead>
        <tr>
            <th>Question ID</th>
            <th>Question</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($question = $result_questions->fetch_assoc()) { ?>
            <tr>
                <td><?php echo $question['question_id']; ?></td>
                <td><?php echo $question['question_text']; ?></td>
                <td class="actions">
                    <a href="edit_question.php?question_id=<?php echo $question['question_id']; ?>">Edit</a> |
                    <a href="delete_question.php?question_id=<?php echo $question['question_id']; ?>">Delete</a>
                </td>
            </tr>
        <?php } ?>
    </tbody>
</table>

        <!-- Buttons -->
        <div class="button-container">
            <button type="button" class="cancel" onclick="window.location.href='quizzes.php';">Cancel</button>
            <button type="submit" class="confirm">Save Changes</button>
        </div>
        </form>
    </main>

    <!-- Footer Section -->
    <div id="footer">
        <ul>
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="Icon/facebook.png" alt="facebook" id="facebook">
                <img src="Icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

</body>
</html>
