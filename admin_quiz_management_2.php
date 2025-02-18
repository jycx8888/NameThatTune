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

if (isset($_GET['action']) && $_GET['action'] === 'updateQuestion') {
    header('Content-Type: application/json');
    
    // Debug log
    error_log("Received update request: " . print_r($_POST, true));
    error_log("Files: " . print_r($_FILES, true));
    
    try {
        $conn->begin_transaction();
        
        $questionId = $_POST['question_id'];
        $correctAnswer = $_POST['correctOption'];
        
        // Update correct answer in question table
        $stmt = $conn->prepare("UPDATE question SET CorrectAnswer = ? WHERE QuestionID = ?");
        $stmt->bind_param("ss", $correctAnswer, $questionId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update correct answer: " . $stmt->error);
        }
        
        // Update options
        for ($i = 1; $i <= 4; $i++) {
            $optionName = $_POST["option$i"];
            $stmt = $conn->prepare("UPDATE `option` SET OptionName = ? WHERE QuestionID = ? AND OptionOrder = ?");
            $stmt->bind_param("ssi", $optionName, $questionId, $i);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update option $i: " . $stmt->error);
            }
        }
        
        // Handle song audio upload
        if (!empty($_FILES['songUpload']['tmp_name'])) {
            $songAudioPath = 'Question Songs/' . basename($_FILES['songUpload']['name']);
            move_uploaded_file($_FILES['songUpload']['tmp_name'], $songAudioPath);
            $stmt = $conn->prepare("UPDATE song SET SongAudio = ? WHERE QuestionID = ?");
            $stmt->bind_param("ss", $songAudioPath, $questionId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update song audio: " . $stmt->error);
            }
        }
        
        // Handle song image upload
        if (!empty($_FILES['songPhoto']['tmp_name'])) {
            $songImagePath = 'Question Images/' . basename($_FILES['songPhoto']['name']);
            move_uploaded_file($_FILES['songPhoto']['tmp_name'], $songImagePath);
            $stmt = $conn->prepare("UPDATE song SET SongImage = ? WHERE QuestionID = ?");
            $stmt->bind_param("ss", $songImagePath, $questionId);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update song image: " . $stmt->error);
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Update successful']);
        
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in update: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_song'])) {
    $song_id = $_POST['song_id'];
    $song_audio = null;
    $song_image = null;

    if (!empty($_FILES['song_audio']['tmp_name'])) {
        $song_audio = file_get_contents($_FILES['song_audio']['tmp_name']);
    }
    if (!empty($_FILES['song_image']['tmp_name'])) {
        $song_image = file_get_contents($_FILES['song_image']['tmp_name']);
    }

    $stmt = $conn->prepare("UPDATE song SET SongAudio = COALESCE(?, SongAudio), SongImage = COALESCE(?, SongImage) WHERE SongID = ?");
    $stmt->bind_param("bbs", $song_audio, $song_image, $song_id);
    
    if ($stmt->execute()) {
        echo "Song updated successfully.";
    } else {
        echo "Error updating song: " . $stmt->error;
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $questionID = $_POST['questionID'];
        $options = [
            $_POST['option1'],
            $_POST['option2'],
            $_POST['option3'],
            $_POST['option4']
        ];
    
        // Fetch existing options for the given question
        $sql = "SELECT OptionID FROM `option` WHERE QuestionID = ? LIMIT 4";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $questionID);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $optionIDs = [];
        while ($row = $result->fetch_assoc()) {
            $optionIDs[] = $row['OptionID'];
        }
    
        // Update options if they exist
        for ($i = 0; $i < count($options); $i++) {
            if (isset($optionIDs[$i])) {
                $updateSQL = "UPDATE `option` SET OptionName = ? WHERE OptionID = ?";
                $updateStmt = $conn->prepare($updateSQL);
                $updateStmt->bind_param("ss", $options[$i], $optionIDs[$i]);
                $updateStmt->execute();
            }
        }
        
        echo "Options updated successfully!";
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

        .preview-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin: 10px 0;
        }

        .preview-container p {
            margin: 0 0 10px 0;
            font-weight: bold;
        }

        .preview-element {
            display: block;
            margin: 10px 0;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>

    <!-- Main Content Section -->
    <main>
    <form method="POST" action="admin_quiz_management_2.php?quiz_id=<?php echo $quiz['QuizID']; ?>">
    <h2 class="edit-quiz-header"><?php echo isset($quiz) ? 'Edit Quiz' : 'Add Song'; ?></h2>

    <!-- Modal -->
    <div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Question</h2>
        <form id="addQuestionForm" enctype="multipart/form-data">
            <input type="hidden" name="question_id" id="question_id">
            <input type="hidden" id="correctOption" name="correctOption">
            
            <!-- Audio Section -->
            <div class="form-group">
                <label for="songUpload">Song Audio (8 secs):</label>
                <input type="file" id="songUpload" name="songUpload" accept="audio/mp3">
                <div id="audioPreview"></div>
            </div>
            
            <!-- Image Section -->
            <div class="form-group">
                <label for="songPhoto">Song Photo:</label>
                <input type="file" id="songPhoto" name="songPhoto" accept="image/*">
                <div id="imagePreview"></div>
            </div>
            
            <!-- Options Section -->
            <div class="option-container">
                <label>Options:</label>
                <div>
                    <input type="text" name="option1" placeholder="Option 1" required>
                    <span class="checkmark" data-value="1"></span>
                </div>
                <div>
                    <input type="text" name="option2" placeholder="Option 2" required>
                    <span class="checkmark" data-value="2"></span>
                </div>
                <div>
                    <input type="text" name="option3" placeholder="Option 3" required>
                    <span class="checkmark" data-value="3"></span>
                </div>
                <div>
                    <input type="text" name="option4" placeholder="Option 4" required>
                    <span class="checkmark" data-value="4"></span>
                </div>
            </div>
            
            <div class="button-group">
                <button type="submit">Save Changes</button>
                <button type="button" onclick="closeModal()">Cancel</button>
            </div>
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
                            // Add html_entity_decode here
                            $options[] = html_entity_decode(htmlspecialchars($option['OptionName']), ENT_QUOTES);
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
                    <tr data-question-id="<?php echo htmlspecialchars($question['QuestionID']); ?>">
                    <td><?php echo htmlspecialchars($question['QuestionID']); ?></td>
                    <td data-options="<?php echo htmlspecialchars($options_text); ?>"><?php echo $options_text; ?></td>
                    <td data-correct="<?php echo htmlspecialchars($question['CorrectAnswer']); ?>"><?php echo htmlspecialchars($question['CorrectAnswer']); ?></td>
                    <td>
                        <?php if (!empty($song['SongAudio'])): ?>
                            <audio controls>
                                <source src="<?php echo htmlspecialchars($song['SongAudio']); ?>" type="audio/mpeg">
                            </audio>
                        <?php else: ?>
                            No audio available
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if (!empty($song['SongImage'])): ?>
                                <img src="<?php echo htmlspecialchars($song['SongImage']); ?>" alt="Song Image" style="max-width: 100px;">
                        <?php else: ?>
                            No image available
                        <?php endif; ?>
                    </td>
                    <td class="actions">
                        <button type="button" onclick="editQuestion('<?php echo htmlspecialchars($question['QuestionID']); ?>')" style="align-items:center;">Edit</button>
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
        function editQuestion(questionId) {
    const modal = document.getElementById("quizModal");
    modal.style.display = "block";
    
    // Find the question row
    const questionRow = document.querySelector(`tr[data-question-id="${questionId}"]`);
    if (!questionRow) return;
    
    // Set question ID
    document.getElementById('question_id').value = questionId;
    
    // Get data from the row
    const cells = questionRow.getElementsByTagName('td');
    const options = cells[1].getAttribute('data-options').split(',').map(opt => opt.trim());
    const correctAnswer = cells[2].getAttribute('data-correct');
    
    // Set options and mark correct answer
    options.forEach((option, index) => {
        const input = document.querySelector(`input[name="option${index + 1}"]`);
        const checkmark = document.querySelector(`span[data-value="${index + 1}"]`);
        
        if (input && checkmark) {
            input.value = option;
            if (option === correctAnswer) {
                checkmark.classList.add('selected');
                checkmark.textContent = '✓';
                document.getElementById('correctOption').value = option;
            } else {
                checkmark.classList.remove('selected');
                checkmark.textContent = '';
            }
        }
    });

    // Show current audio/image with preview
    const audioCell = cells[3];
    const imageCell = cells[4];
    
    // Display current audio
    const currentAudio = audioCell.querySelector('audio');
    if (currentAudio) {
        const audioPreview = document.getElementById('audioPreview');
        audioPreview.innerHTML = `
            <div class="preview-container">
                <p>Current Audio:</p>
                ${currentAudio.outerHTML}
            </div>
        `;
    }
    
    // Display current image
    const currentImage = imageCell.querySelector('img');
    if (currentImage) {
        const imagePreview = document.getElementById('imagePreview');
        imagePreview.innerHTML = `
            <div class="preview-container">
                <p>Current Image:</p>
                <img src="${currentImage.src}" alt="Current Image" style="max-width: 200px;">
            </div>
        `;
    }
}
        
                document.querySelectorAll('.checkmark').forEach(check => {
                check.addEventListener('click', function() {
                    // Remove selection from all checkmarks
                    document.querySelectorAll('.checkmark').forEach(c => {
                        c.classList.remove('selected');
                        c.textContent = '';
                    });
                    
                    // Select clicked checkmark
                    this.classList.add('selected');
                    this.textContent = '✓';
                    
                    // Update hidden input
                    document.getElementById('correctOption').value = this.dataset.value;
                });
            });
        
            document.getElementById('addQuestionForm').addEventListener('submit', function(e) {
                e.preventDefault();

                if (!document.querySelector('.checkmark.selected')) {
                    alert('Please select a correct answer');
                    return;
                }
            
                const formData = new FormData(this);

                fetch('admin_quiz_management_2.php?action=updateQuestion', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Question updated successfully!');
                        location.reload();
                    } else {
                        throw new Error(data.error || 'Update failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error updating question: ' + error.message);
                });
            });
    </script>

<?php 
    // Close database connection at the very end of the page
    mysqli_close($conn);
    ?>

</body>
</html>
