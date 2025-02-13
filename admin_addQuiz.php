<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "namethattune");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch genres from the database
$sql = "SELECT GenreName FROM genre";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Add New Quiz</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        body {
            background-color: #d1a3ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1000px;
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .actions a {
            background-color: #ACD7EC;
            color: black;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }
        .actions a:hover {
            background-color: #89CFF0;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div class="container">
        <h2>Add New Quiz</h2>
        <form action="submit_quiz.php" method="post">
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <label for="quiz_name">Quiz Name:</label>
                <input type="text" id="quiz_name" name="quiz_name" required>
            </div>
            <br>

            <!-- Dynamically Fetched Category Dropdown (Kept) -->
            <div style="display: flex; flex-direction: column; align-items: flex-start;">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <?php
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . htmlspecialchars($row['GenreName']) . "'>" . htmlspecialchars($row['GenreName']) . "</option>";
                        }
                    } else {
                        echo "<option value=''>No categories found</option>";
                    }
                    ?>
                </select>
            </div>
            <br>

            <table border="1">
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
                            <a href="edit_question.php?question_id=Q001">Edit</a> |
                            <a href="delete_question.php?question_id=Q001">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Q002</td>
                        <td>Humble, Not Like Us, Godzilla, Rap God</td>
                        <td>Not Like Us</td>
                        <td class="actions">
                            <a href="edit_question.php?question_id=Q002">Edit</a> |
                            <a href="delete_question.php?question_id=Q002">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Q003</td>
                        <td>Humble, Not Like Us, Godzilla, Rap God</td>
                        <td>Rap God</td>
                        <td class="actions">
                            <a href="edit_question.php?question_id=Q003">Edit</a> |
                            <a href="delete_question.php?question_id=Q003">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td>Q004</td>
                        <td>Humble, Not Like Us, Godzilla, Rap God</td>
                        <td>Humble</td>
                        <td class="actions">
                            <a href="edit_question.php?question_id=Q004">Edit</a> |
                            <a href="delete_question.php?question_id=Q004">Delete</a>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br><br>

            <button type="button" style="margin-right: 10px;">Add Question</button>
            <button type="reset" style="margin-right: 10px;">Cancel</button>
            <button type="submit">Confirm</button>
        </form>
    </div>
</body>
</html>
