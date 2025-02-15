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

// Fetch genres from database
$sql = "SELECT GenreID, GenreName FROM genre";
$result = $conn->query($sql);

// Store genres in an array
$genres = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $genres[] = $row;
    }
}
$stmt->close();
$conn->close();
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
            margin-top: 20px;
            text-align: left; /* Change from center to left */
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
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            min-width: 120px;
            margin: 10px;
            transition: all 0.3s ease;
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
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto; /* Add this to enable vertical scrolling */
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: 5% auto; /* Changed from 15% to 5% to position higher */
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            max-height: 90vh; /* Set maximum height */
            overflow-y: auto; /* Enable scrolling within the modal content */
        }

        .modal-content input {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-content select {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }
        
        .modal-content select:focus {
            outline: none;
            border-color: #007bff;
        }

        .option-container div {
            display: flex;
            align-items: center;
            background-color: rgb(194, 194, 194);
            border-radius: 20px;
            padding: 10px;
            margin-bottom: 10px;
            position: relative;
            border: 2px solid rgb(130, 185, 229);
        }

        .option-container input[type="text"] {
            flex-grow: 1;
            border: none;
            background: transparent;
            font-size: 16px;
            color: black;
            outline: none;
        }

        .checkmark {
            width: 30px;
            height: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            border: 2px solid #aaa;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            font-size: 20px;
            color: transparent;
            margin-left: 10px;
        }

        .checkmark:hover {
            background-color: #ddd;
        }

        .checkmark.selected {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
            content: "\25CF"; /* Unicode for a filled circle */
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>

    <div class="container">
    <h2 style="text-align: left;">Add New Quiz</h2>

    <div style="margin: 15px; text-align: left;">
        <div style="max-width: 300px; margin-bottom: 20px;">  <!-- Changed from 600px to 300px -->
            <label for="questionId" style="display: block; text-align: left; margin-bottom: 5px;">Add New Quiz Name:</label>
            <input type="text" id="questionId" name="questionId" required style="width: 100%;">
        </div>
        <div style="max-width: 300px;">  <!-- Changed from 600px to 300px -->
            <label for="options" style="display: block; text-align: left; margin-bottom: 5px;">Add New Quiz Category:</label>
            <select id="options" name="options" required style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;">
                <option value="">Select a category</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre['GenreID']); ?>">
                        <?php echo htmlspecialchars($genre['GenreName']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

        <table>
            <thead>
                <tr>
                    <th>New Quiz No</th>
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
            </tbody>
        </table>
        <br><br>

        <button type="button" onclick="openModal()">Add Quiz</button>
        <button type="button" onclick="window.location.href='admin_quiz_management.php'">Cancel</button>
    </div>

    <!-- Modal -->
    <div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Add New Quiz</h2>
        <form id="addQuestionForm">
            <label for="songUpload">Correct Song Upload (8 secs):</label>
            <input type="file" id="songUpload" name="songUpload" accept="audio/mp3">
            <br><br>
            <label for="songPhoto">Song Photo (Photos related to Options only):</label>
            <input type="file" id="songPhoto" name="songPhoto" accept="image/*">
            <br><br>
            <label>Options:</label>
                <div class="option-container">
            <div>
                <input type="text" name="option1" placeholder="Enter option 1" required>
                <span class="checkmark" data-value="option1"></span>
            </div>
            <div>
                <input type="text" name="option2" placeholder="Enter option 2" required>
                <span class="checkmark" data-value="option2"></span>
            </div>
            <div>
                <input type="text" name="option3" placeholder="Enter option 3" required>
                <span class="checkmark" data-value="option3"></span>
            </div>
            <div>
                <input type="text" name="option4" placeholder="Enter option 4" required>
                <span class="checkmark" data-value="option4"></span>
            </div>
            <input type="hidden" id="correctOption" name="correctOption">
        </div>
            
            <button type="submit">Add Question</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("quizModal").style.display = "block";
        }
        
        function closeModal() {
            document.getElementById("quizModal").style.display = "none";
            document.getElementById("addQuestionForm").reset();
        }
        
        document.getElementById("addQuestionForm").onsubmit = function(e) {
            e.preventDefault();
            // Here you would typically add AJAX call to save the question
            alert("Question Added Successfully!");
            closeModal();
        };

        document.querySelectorAll('.checkmark').forEach(check => {
            check.addEventListener('click', function() {
                document.querySelectorAll('.checkmark').forEach(c => {
                    c.classList.remove('selected');
                    c.textContent = ""; // Reset all
                });
                this.classList.add('selected');
                this.textContent = "\u2714"; // Unicode for check mark
                document.getElementById('correctOption').value = this.getAttribute('data-value');
            });
        });
    </script>
</body>
</html>
