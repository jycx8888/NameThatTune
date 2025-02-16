<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$server = "localhost";
$user = "root";
$password = "";
$database = "namethattune";

$conn = new mysqli($server, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    $profile_picture_path = 'Icon/account.png';
}

if (isset($_GET['quiz_id'])) {
    //echo "Debug: Raw quiz_id = " . $_GET['quiz_id'] . "<br>";
    $quiz_id = $_GET['quiz_id']; // Keep it as a string
    //echo "Debug: Processed quiz_id = " . $quiz_id . "<br>";
} else {
    die("Error: quiz_id not provided in the URL.");
}

// Validate if quiz_id exists in the database
$query = $conn->prepare("SELECT QuizID FROM quiz WHERE QuizID = ?");
$query->bind_param("s", $quiz_id); // Use "s" for string
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Error: quiz_id does not exist in the database.");
}

$stmt = $conn->prepare("SELECT QuizID, GenreID, CreatedTime FROM quiz WHERE QuizID = ?");
$stmt->bind_param("s", $quiz_id); // Use "s" for string
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
} else {
    die("Error: Quiz not found.");
}

if (isset($quiz_id)) {
    $stmt_questions = $conn->prepare("SELECT QuestionID, CorrectAnswer FROM question WHERE QuizID = ?");
    $stmt_questions->bind_param("s", $quiz_id); // Use "s" for string
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();

    //if ($result_questions->num_rows > 0) {
        //echo "Debug: Questions Found = " . $result_questions->num_rows . "<br>";
    //} else {
        //echo "Debug: No Questions Found for Quiz ID = " . $quiz_id . "<br>";
   // }
} else {
    $quiz = null;
    $result_questions = null;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $genre_id = $conn->real_escape_string($_POST['genre_id']);
    $created_time = $conn->real_escape_string($_POST['created_time']);

    // Validate GenreID
    $validate_genre_sql = "SELECT GenreID FROM genre WHERE GenreID = '$genre_id'";
    $validate_result = $conn->query($validate_genre_sql);

    if ($validate_result->num_rows === 0) {
        die("Error: Invalid GenreID. The selected genre does not exist in the database.");
    }

    // Proceed with the update if GenreID is valid
    $update_sql = "UPDATE quiz SET GenreID='$genre_id', CreatedTime='$created_time' WHERE QuizID='$quiz_id'";

    if ($conn->query($update_sql) === TRUE) {
        echo "Quiz updated successfully!<br>";
        header("Location: admin_quiz_management.php?quiz_id=$quiz_id");
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
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        main {
            flex: 1;
            margin: 40px;
            padding: 20px;
            width: 1100px;
            align-self: center;
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
       /* Overlay Styles */
        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        /* Popup Styles */
        #editQuizPopup {
            display: none;
            position: fixed;
            top: 10%; /* Adjust this value to leave space for the header */
            left: 50%;
            transform: translateX(-50%);
            width: 600px; /* Increased width */
            max-height: 80vh; /* Maximum height */
            overflow-y: auto; /* Enable scrolling */
            padding: 20px;
            background-color: white;
            border-radius: 30px; /* Keep your existing border-radius */
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Keep your existing box-shadow */
            z-index: 1000;
        }
        #closePopupButton {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #4B006E;
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
        }

        #closePopupButton:hover {
            background-color: #CBC3E3;
        }

        #overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="file"] {
            width: 90%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 15px;
            font-size: 14px;
        }

        .options {
            margin-bottom: 20px;
        }

        .option-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-item input[type="text"] {
            flex: 1;
            margin-right: 10px;
            border-radius: 10px;
        }

        .option-item img {
            cursor: pointer;
            width: 30px;
            height: 30px;
            margin-left: 10px;
        }

        .option-item img.selected {
            border: 2px solid green;
            border-radius: 50%;
        }

        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #4B006E;
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-size: 18px;
        }

        .submit-button:hover {
            background-color: #CBC3E3;
        }
    </style>
    
<script>
function deleteQuestion(questionId) {
    if (confirm("Are you sure you want to delete this question?")) {
        // Create an AJAX request
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "admin_quiz_management_3.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        // Define what happens on successful data submission
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert("Question deleted successfully!");
                location.reload(); // Reload the page to reflect changes
            } else {
                alert("Error deleting question: " + xhr.responseText);
            }
        };

        // Define what happens in case of an error
        xhr.onerror = function () {
            alert("Request failed. Please try again.");
        };

        // Send the request with the question ID
        xhr.send("action=delete&question_id=" + questionId);
    }
}

// Function to open the popup and load page3 content
function openEditQuizPopup(questionId) {
    // Fetch the content of page3 with the question ID as a parameter
    fetch('admin_quiz_management_3.php?question_id=' + questionId)
        .then(response => response.text())
        .then(data => {
            // Insert the content into the popup
            document.getElementById('popupContent').innerHTML = data;
            // Show the popup and overlay
            document.getElementById('editQuizPopup').style.display = 'block';
            document.getElementById('overlay').style.display = 'block';
        })
        .catch(error => console.error('Error loading page3:', error));
}

// Function to close the popup

function closeEditQuizPopup() {
            document.getElementById('editQuizPopup').style.display = 'none';
            document.getElementById('overlay').style.display = 'none';
        }

        document.getElementById('closePopupButton').addEventListener('click', closeEditQuizPopup);
        document.getElementById('overlay').addEventListener('click', closeEditQuizPopup);
</script>

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

    <?php include 'admin_hamburger_menu.php'; ?>

    <!-- Main Content Section -->
    <main>
    <form method="POST" action="admin_quiz_management.php?quiz_id=<?php echo $quiz['QuizID']; ?>">
    <h2 class="edit-quiz-header"><?php echo isset($quiz) ? 'Edit Quiz' : 'Add Song'; ?></h2>

    <!-- Overlay and Popup -->
<div id="overlay"></div>
<div id="editQuizPopup">
    <h2>Edit Quiz</h2>
    <form id="quiz-form" action="admin_quiz_management_3.php" method="POST">
        <div id="popupContent">
            <!-- Dynamic content from admin_quiz_management_3.php will be inserted here -->
        </div>
        <button type="submit" class="submit-button">Confirm</button>
    </form>
    <button id="closePopupButton" onclick="closeEditQuizPopup()">Close</button>
</div>

     <!-- Display Quiz ID -->
     <p>Quiz ID: <?= isset($quiz['QuizID']) && !empty($quiz['QuizID']) ? htmlspecialchars($quiz['QuizID']) : 'No Quiz Found'; ?></p>
    
    <?php if (isset($quiz)): ?>
        <input type="hidden" name="quiz_id" value="<?php echo $quiz['QuizID']; ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="genre-id">Genre</label>
        <select id="genre-id" name="genre_id">
            <option value="1" <?php if (isset($quiz) && $quiz['GenreID'] == 'G001') echo 'selected'; ?>>English</option>
            <option value="2" <?php if (isset($quiz) && $quiz['GenreID'] == 'G002') echo 'selected'; ?>>Japanese</option>
            <option value="3" <?php if (isset($quiz) && $quiz['GenreID'] == 'G003') echo 'selected'; ?>>Korean</option>
        </select>
    </div>

    <div class="form-group">
        <label for="created-time">Created Time</label>
        <input type="datetime-local" id="created-time" name="created_time" 
            value="<?php echo isset($quiz) ? date('Y-m-d\TH:i:s', strtotime($quiz['CreatedTime'])) : ''; ?>">
    </div>

    <!-- Table Section for Questions (only in edit mode) -->
<?php if (isset($quiz)): ?>
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
            <?php if ($result_questions->num_rows > 0): ?>
                <?php while ($question = $result_questions->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($question['QuestionID']); ?></td>
                        <td><?php echo htmlspecialchars($question['CorrectAnswer']); ?></td>
                        <td class="actions">
                        <button type="button" onclick="openEditQuizPopup('<?php echo $question['QuestionID']; ?>')">Edit</button> |
                            <button onclick="deleteQuestion('<?php echo $question['QuestionID']; ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="3">No questions found for this quiz.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

    <!-- Buttons -->
    <div class="button-container">
        <button type="button" class="cancel" onclick="window.location.href='admin_quiz_management.php';">Cancel</button>
        <button type="submit" class="confirm"><?php echo isset($quiz) ? 'Save Changes' : 'Add Song'; ?></button>
    </div>
</form>
    </main>

    <!-- Script for Search Functionality -->
    <script>
        function performSearch() {
    const searchInput = document.getElementById("search");
    if (!searchInput) {
        console.error("Error: Search input not found!");
        return;
    }
    console.log("Searching for: " + searchInput.value);

    const filter = document.getElementById("filter").value;
    const searchTerm = searchInput.value.toLowerCase();
    const table = document.getElementById("quizTable");
    if (!table) {
        console.error("Error: Quiz table not found!");
        return;
    }

    const rows = table.getElementsByTagName("tr");
    let found = false;

    for (let row of rows) {
        const cells = row.getElementsByTagName("td");
        if (cells.length === 0) continue;

        let shouldDisplay = false;
        if (filter === "id" && cells[0] && cells[0].textContent.toLowerCase().includes(searchTerm)) {
            shouldDisplay = true;
        } else if (filter === "genre" && cells[1] && cells[1].textContent.toLowerCase().includes(searchTerm)) {
            shouldDisplay = true;
        } else if (filter === "time" && cells[2] && cells[2].textContent.toLowerCase().includes(searchTerm)) {
            shouldDisplay = true;
        }

        row.style.display = shouldDisplay ? "" : "none";
        if (shouldDisplay) found = true;
    }

    let noResultsRow = document.getElementById("noResultsRow");
    if (!found) {
        if (!noResultsRow) {
            noResultsRow = document.createElement("tr");
            noResultsRow.id = "noResultsRow";
            noResultsRow.innerHTML = `<td colspan="4" style="text-align:center;">No matching quizzes found.</td>`;
            table.appendChild(noResultsRow);
        }
    } else if (noResultsRow) {
        noResultsRow.remove();
    }
}

// Handle song photo selection
const songPhotoInput = document.getElementById('song-photo');
const songPhotoDisplay = document.getElementById('song-photo-display');

songPhotoInput?.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = (e) => {
            songPhotoDisplay.innerHTML = `
                <img src="${e.target.result}" alt="Song Photo" style="max-width: 200px;">
                <button id="delete-photo">Delete Photo</button>
            `;
            document.getElementById('delete-photo').addEventListener('click', () => {
                songPhotoDisplay.innerHTML = '';
                songPhotoInput.value = '';
            });
        };
        reader.readAsDataURL(file);
    } else {
        alert("Please select a valid image file.");
    }
});

// Handle MP3 file selection
const songMp3Input = document.getElementById('song-mp3');
const mp3Display = document.getElementById('mp3-display');

songMp3Input?.addEventListener('change', (event) => {
    const file = event.target.files[0];
    if (file && file.type === "audio/mpeg") {
        const fileURL = URL.createObjectURL(file);
        mp3Display.innerHTML = `
            <p>Selected MP3: ${file.name}</p>
            <audio controls src="${fileURL}"></audio>
            <button id="delete-mp3">Delete</button>
        `;
        document.getElementById('delete-mp3').addEventListener('click', () => {
            mp3Display.innerHTML = '';
            songMp3Input.value = '';
        });
    } else {
        alert("Please select a valid MP3 file.");
    }
});

// Handle selecting the correct answer option
const correctIcons = document.querySelectorAll('.correct-icon');
correctIcons.forEach(icon => {
    icon.addEventListener('click', () => {
        correctIcons.forEach(icon => icon.classList.remove('selected'));
        icon.classList.add('selected');
    });
});
    </script>

<?php 
    // Close database connection at the very end of the page
    mysqli_close($conn);
    ?>

</body>
</html>
