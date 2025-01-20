<?php
// database connection 
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
echo"Connected successfully";
?>

<?php
// Fetch quiz data if 'quiz_id' is provided
if (isset($_GET['quiz_id'])) {
    $quiz_id = $_GET['quiz_id'];
    $sql = "SELECT * FROM quizzes WHERE quiz_id='$quiz_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc(); // Fetch data into an array
    } else {
        echo "Quiz not found!";
        exit(); // Exit if no quiz is found
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

        <!-- Table Section -->
        <table class="question-table">
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Options</th>
                    <th>Answer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Q001</td>
                    <td>Never Gonna Give You Up, Niggas in Paris, Blank Space, YMCA</td>
                    <td>Never Gonna Give You Up</td>
                    <td class="actions">
                        <a href="#">Edit</a> | <a href="#">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Buttons -->
        <div class="button-container">
            <button class="cancel">Cancel</button>
            <button class="confirm">Confirm</button>
        </div>
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
