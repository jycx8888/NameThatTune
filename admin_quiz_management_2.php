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
            width: 1200px;
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

    <?php include 'admin_hamburger_menu.php'; ?>

    <!-- Main Content Section -->
    <main>
    <form method="POST" action="admin_quiz_management.php?quiz_id=<?php echo $quiz['QuizID']; ?>">
    <h2 class="edit-quiz-header"><?php echo isset($quiz) ? 'Edit Quiz' : 'Add Song'; ?></h2>

    <!-- Modal -->
    <div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Question</h2>
        <form id="addQuestionForm">
            <label for="songUpload">Correct Song Upload (8 secs):</label>
            <input type="file" id="songUpload" name="songUpload" accept="audio/mp3">
            <br><br>
            <label for="songPhoto">Correct Song Photo (Photos related to Options only):</label>
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
            
            <!-- Replace the existing buttons section -->
            <button type="submit">Edit Question</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
        </div>
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
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Song Audio</th>
                <th>Song Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result_questions->num_rows > 0): ?>
                <?php while ($question = $result_questions->fetch_assoc()): ?>
                    <?php
                        // Fetch options for the current question
                        $stmt_options = $conn->prepare("SELECT OptionName FROM option WHERE QuestionID = ?");
                        $stmt_options->bind_param("s", $question['QuestionID']);
                        $stmt_options->execute();
                        $result_options = $stmt_options->get_result();
                        $options = [];
                        while ($option = $result_options->fetch_assoc()) {
                            $options[] = htmlspecialchars($option['OptionName']);
                        }
                        $options_text = implode(", ", $options);
                    ?>
                    <?php
                        // Fetch song for the current question
                        $stmt_song = $conn->prepare("SELECT SongAudio, SongImage FROM song WHERE QuestionID = ?");
                        $stmt_song->bind_param("s", $question['QuestionID']);
                        $stmt_song->execute();
                        $result_song = $stmt_song->get_result();
                        $song = $result_song->fetch_assoc();
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($question['QuestionID']); ?></td>
                        <td><?php echo $options_text; ?></td>
                        <td><?php echo htmlspecialchars($question['CorrectAnswer']); ?></td>
                        <td>
                            <?php if (!empty($song['SongAudio'])): ?>
                                <audio controls>
                                <source src="data:audio/mpeg;base64,<?php echo base64_encode($song['SongAudio']); ?>" type="audio/mpeg">
                                Your browser does not support the audio element.
                                </audio>
                            <?php else: ?>
                                No audio available
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($song['SongImage'])): ?>
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($song['SongImage']); ?>" alt="Song Image" style="max-width: 100px;">
                            <?php else: ?>
                                No image available
                            <?php endif; ?>
                        </td>
                        <td class="actions">
                            <button type="button" onclick="openModal()" style="align-items:center;">Edit</button>
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
        <button type="button" onclick="openModal()">Add Question</button>
        <button type="button" class="cancel" onclick="window.location.href='admin_quiz_management.php';">Cancel</button>
        <button type="submit" class="confirm"><?php echo isset($quiz) ? 'Save Changes' : 'Add Song'; ?></button>
    </div>
</form>
    </main>


    <script>
        function openModal() {
            document.getElementById("quizModal").style.display = "block";

            // Reset form
            document.getElementById("addQuestionForm").reset();

            // Reset all checkmarks
            document.querySelectorAll('.checkmark').forEach(check => {
                check.classList.remove('selected');
                check.textContent = "";
            });
        
            // Reset correct answer input field
            document.getElementById('correctOption').value = "";

            // Remove existing file labels if they exist
            const existingSongLabel = document.getElementById("existingSongFile");
            if (existingSongLabel) {
                existingSongLabel.remove();
            }

            const existingPhotoLabel = document.getElementById("existingPhotoFile");
            if (existingPhotoLabel) {
                existingPhotoLabel.remove();
            }
        }
        
        // Add this function to your <script> section
        function closeModal() {
            document.getElementById("quizModal").style.display = "none";
            document.getElementById("addQuestionForm").reset();
            
            // Reset all checkmarks
            document.querySelectorAll('.checkmark').forEach(check => {
                check.classList.remove('selected');
                check.textContent = "";
            });
            
            // Reset correct answer input field
            document.getElementById('correctOption').value = "";
            
            // Remove existing file labels
            const existingSongLabel = document.getElementById("existingSongFile");
            if (existingSongLabel) {
                existingSongLabel.remove();
            }
            
            const existingPhotoLabel = document.getElementById("existingPhotoFile");
            if (existingPhotoLabel) {
                existingPhotoLabel.remove();
            }
        }

        // Function to update quiz numbers after deleting a row
        function updateQuizNumbers() {
            const rows = document.querySelectorAll("table tbody tr");
            rows.forEach((row, index) => {
                row.cells[0].textContent = index + 1;
            });
        }

        // Function to edit a question 
        function editQuestion(link) {
            const row = link.closest("tr");
            row.setAttribute('data-editing', 'true'); // Mark the row being edited
            const cells = row.getElementsByTagName("td");

            const questionId = cells[0].textContent.trim();
            const optionsText = cells[1].textContent.split(", ").map(option => option.trim());
            const correctAnswer = cells[2].textContent.trim();
            const existingSongFileName = cells[3].querySelector("audio source") ? cells[3].querySelector("audio source").src : "";
            const existingPhotoFileName = cells[4].querySelector("img") ? cells[4].querySelector("img").src : "";
            openModal();

            // Populate option fields
            const options = document.querySelectorAll('.option-container input[type="text"]');
            options.forEach((option, index) => {
                option.value = optionsText[index] || "";
            });
        
            // Select the correct answer
            document.querySelectorAll('.checkmark').forEach(check => {
                check.classList.remove('selected');
                check.textContent = "";
                if (check.previousElementSibling.value === correctAnswer) {
                    check.classList.add('selected');
                    check.textContent = "\u2714";
                    document.getElementById('correctOption').value = check.getAttribute('data-value');
                }
            });
        
            // Display existing file names
            document.getElementById("songUpload").value = ""; // Clear file input
            document.getElementById("songPhoto").value = ""; // Clear file input
        
            const songFileLabel = document.getElementById("existingSongFile");
            if (!songFileLabel) {
                const newLabel = document.createElement("p");
                newLabel.id = "existingSongFile";
                newLabel.textContent = "Current MP3: " + existingSongFileName;
                document.getElementById("songUpload").insertAdjacentElement("afterend", newLabel);
            } else {
                songFileLabel.textContent = "Current MP3: " + existingSongFileName;
            }
        
            const photoFileLabel = document.getElementById("existingPhotoFile");
            if (!photoFileLabel) {
                const newLabel = document.createElement("p");
                newLabel.id = "existingPhotoFile";
                newLabel.textContent = "Current Photo: " + existingPhotoFileName;
                document.getElementById("songPhoto").insertAdjacentElement("afterend", newLabel);
            } else {
                photoFileLabel.textContent = "Current Photo: " + existingPhotoFileName;
            }
        }

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

        document.getElementById("addQuestionForm").onsubmit = function(e) {
            e.preventDefault();
                
            const tableBody = document.querySelector("table tbody");
            const editingRow = document.querySelector('[data-editing="true"]');
            const currentRowCount = tableBody.getElementsByTagName("tr").length;
            const options = document.querySelectorAll('.option-container input[type="text"]');
            const correctOption = document.getElementById("correctOption").value;
            const songUpload = document.getElementById("songUpload").files[0];
            const songPhoto = document.getElementById("songPhoto").files[0];
                
            // Only check row limit for new additions, not for edits
            if (!editingRow && currentRowCount >= 5) {
                alert("You can only add up to 5 quizzes.");
                return;
            }
        
            // Check if editing or adding new
            if (!editingRow) {
                // Validation for new entries
                if (!songUpload) {
                    alert("Please upload an MP3 file.");
                    return;
                }
            
                if (!songPhoto) {
                    alert("Please upload a song photo.");
                    return;
                }
            
                // Audio duration check
                const audio = new Audio(URL.createObjectURL(songUpload));
                audio.onloadedmetadata = function() {
                    if (audio.duration > 9) {
                        alert("The uploaded MP3 must be 8 seconds or less.");
                        return;
                    }
                    proceedWithSubmission();
                };
            } else {
                // If editing, proceed directly
                proceedWithSubmission();
            }
        
            function proceedWithSubmission() {
                if (!correctOption) {
                    alert("Please select the correct answer.");
                    return;
                }
            
                const optionTexts = [];
                options.forEach(option => optionTexts.push(option.value));
                
                const correctAnswerText = document.querySelector('.checkmark.selected').previousElementSibling.value;
                
                if (editingRow) {
                    // Update existing row
                    editingRow.cells[1].textContent = optionTexts.join(", ");
                    editingRow.cells[2].textContent = correctAnswerText;
                    if (songUpload) editingRow.cells[3].textContent = songUpload.name;
                    if (songPhoto) editingRow.cells[4].textContent = songPhoto.name;
                    editingRow.removeAttribute('data-editing');
                } else {
                    // Create new row
                    const newRow = document.createElement("tr");
                    newRow.innerHTML = `
                        <td>${currentRowCount + 1}</td>
                        <td>${optionTexts.join(", ")}</td>
                        <td>${correctAnswerText}</td>
                        <td>${songUpload.name}</td>
                        <td>${songPhoto.name}</td>
                        <td class="actions">
                            <a href="#" onclick="editQuestion(this)">Edit</a>
                        </td>
                    `;
                    tableBody.appendChild(newRow);
                }
                
                closeModal();
            }
        };
    </script>

<?php 
    // Close database connection at the very end of the page
    mysqli_close($conn);
    ?>

</body>
</html>
