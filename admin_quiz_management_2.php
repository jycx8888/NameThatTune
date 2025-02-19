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

$genreMapping = [
    'G001' => 'English',
    'G002' => 'Japanese',
    'G003' => 'Korean'
];

if (isset($_GET['action']) && $_GET['action'] === 'updateQuestion') {
    header('Content-Type: application/json');
    
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
        
        // Update SongName in song table based on the correct answer
        $stmt = $conn->prepare("UPDATE song SET SongName = ? WHERE QuestionID = ?");
        $stmt->bind_param("ss", $correctAnswer, $questionId);
        if (!$stmt->execute()) {
            throw new Exception("Failed to update song name: " . $stmt->error);
        }
        
        // Fetch existing options for the question
        $stmt = $conn->prepare("SELECT OptionID FROM `option` WHERE QuestionID = ?");
        $stmt->bind_param("s", $questionId);
        $stmt->execute();
        $result = $stmt->get_result();
        $options = [];
        while ($row = $result->fetch_assoc()) {
            $options[] = $row['OptionID'];
        }
        
        // Update options
        foreach ($options as $index => $optionID) {
            $optionName = $_POST["option" . ($index + 1)];
            $stmt = $conn->prepare("UPDATE `option` SET OptionName = ? WHERE OptionID = ?");
            $stmt->bind_param("ss", $optionName, $optionID);
            if (!$stmt->execute()) {
                throw new Exception("Failed to update option " . ($index + 1) . ": " . $stmt->error);
            }
        }
        
        // Handle song audio upload
        if (isset($_FILES['songUpload']) && $_FILES['songUpload']['error'] === UPLOAD_ERR_OK) {
            $songAudioPath = 'Question Songs/' . basename($_FILES['songUpload']['name']);
            if (!file_exists('Question Songs/')) {
                mkdir('Question Songs/', 0777, true);
            }
            if (move_uploaded_file($_FILES['songUpload']['tmp_name'], $songAudioPath)) {
                $stmt = $conn->prepare("UPDATE song SET SongAudio = ? WHERE QuestionID = ?");
                $stmt->bind_param("ss", $songAudioPath, $questionId);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update song audio in database");
                }
            } else {
                throw new Exception("Failed to move uploaded audio file");
            }
        }
        
        // Handle song image upload
        if (isset($_FILES['songPhoto']) && $_FILES['songPhoto']['error'] === UPLOAD_ERR_OK) {
            $songImagePath = 'Question Images/' . basename($_FILES['songPhoto']['name']);
            if (!file_exists('Question Images/')) {
                mkdir('Question Images/', 0777, true);
            }
            if (move_uploaded_file($_FILES['songPhoto']['tmp_name'], $songImagePath)) {
                $stmt = $conn->prepare("UPDATE song SET SongImage = ? WHERE QuestionID = ?");
                $stmt->bind_param("ss", $songImagePath, $questionId);
                if (!$stmt->execute()) {
                    throw new Exception("Failed to update song image in database");
                }
            } else {
                throw new Exception("Failed to move uploaded image file");
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
    $quiz_id = $_GET['quiz_id'];
} else {
    die("Error: quiz_id not provided in the URL.");
}

// Validate if quiz_id exists in the database
$query = $conn->prepare("SELECT QuizID FROM quiz WHERE QuizID = ?");
$query->bind_param("s", $quiz_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    die("Error: quiz_id does not exist in the database.");
}

$stmt = $conn->prepare("SELECT QuizID, QuizName, GenreID, CreatedTime FROM quiz WHERE QuizID = ?");
$stmt->bind_param("s", $quiz_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc();
} else {
    die("Error: Quiz not found.");
}

if (isset($quiz_id)) {
    $stmt_questions = $conn->prepare("SELECT QuestionID, CorrectAnswer FROM question WHERE QuizID = ?");
    $stmt_questions->bind_param("s", $quiz_id);
    $stmt_questions->execute();
    $result_questions = $stmt_questions->get_result();
} else {
    $quiz = null;
    $result_questions = null;
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
            <h2 class="edit-quiz-header"><?php echo isset($quiz) ? 'Edit Quiz' : 'Add Song'; ?></h2>
        
            <!-- Modal -->
            <div id="quizModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <h2>Edit Question</h2>
        <form id="addQuestionForm" enctype="multipart/form-data">
            <input type="hidden" id="question_id" name="question_id">
            <input type="hidden" id="correctOption" name="correctOption">
            
            <div class="form-group">
                <label>Song Audio:</label>
                <input type="file" name="songUpload" accept="audio/*">
                <div id="audioPreview"></div>
            </div>

            <div class="form-group">
                <label>Song Image:</label>
                <input type="file" name="songPhoto" accept="image/*">
                <div id="imagePreview"></div>
            </div>

            <div class="form-group">
                <label>Options:</label>
                <div class="option-container">
                    <div>
                        <input type="text" name="option1" required>
                        <span class="checkmark" data-value="1"></span>
                    </div>
                    <div>
                        <input type="text" name="option2" required>
                        <span class="checkmark" data-value="2"></span>
                    </div>
                    <div>
                        <input type="text" name="option3" required>
                        <span class="checkmark" data-value="3"></span>
                    </div>
                    <div>
                        <input type="text" name="option4" required>
                        <span class="checkmark" data-value="4"></span>
                    </div>
                </div>
            </div>

            <button type="submit" class="submit-button">Update Question</button>
        </form>
    </div>
</div>
        
            <!-- Display Quiz ID -->
            <h3>Quiz ID: <?= isset($quiz['QuizID']) && !empty($quiz['QuizID']) ? htmlspecialchars($quiz['QuizID']) : 'No Quiz Found'; ?></h3>
            <h3>Quiz Name: <?= isset($quiz['QuizName']) && !empty($quiz['QuizName']) ? htmlspecialchars($quiz['QuizName']) : 'No Quiz Name Found'; ?></h3>
            <h3>Genre: <?= isset($quiz['GenreID']) && !empty($quiz['GenreID']) ? htmlspecialchars($genreMapping[$quiz['GenreID']]) : 'No Genre Found'; ?></h3>
            
            <?php if (isset($quiz)): ?>
                <input type="hidden" name="quiz_id" value="<?php echo $quiz['QuizID']; ?>">
            <?php endif; ?>
        
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
    </main>
    
    <script>

        function validateAudioDuration(file) {
            return new Promise((resolve, reject) => {
                const audio = new Audio();
                const objectUrl = URL.createObjectURL(file);
                
                audio.addEventListener('loadedmetadata', () => {
                    URL.revokeObjectURL(objectUrl);
                    if (audio.duration > 9) {
                        reject('Audio file must not be longer than 9 seconds');
                    } else {
                        resolve();
                    }
                });
            
                audio.addEventListener('error', () => {
                    URL.revokeObjectURL(objectUrl);
                    reject('Error loading audio file');
                });
            
                audio.src = objectUrl;
            });
        }

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
    // Get the modal
    const modal = document.getElementById("quizModal");
    
    // Get the current question row
    const questionId = document.getElementById('question_id').value;
    const questionRow = document.querySelector(`tr[data-question-id="${questionId}"]`);
    
    if (questionRow) {
        // Restore original values from data attributes
        const cells = questionRow.getElementsByTagName('td');
        const originalOptions = cells[1].getAttribute('data-options');
        const originalCorrect = cells[2].getAttribute('data-correct');
        
        // Reset the table display to original values
        cells[1].textContent = originalOptions;
        cells[2].textContent = originalCorrect;
    }
    
    // Reset the form
    document.getElementById("addQuestionForm").reset();
    
    // Clear all input values
    document.querySelectorAll('.option-container input[type="text"]').forEach(input => {
        input.value = '';
    });
    
    // Reset all checkmarks
    document.querySelectorAll('.checkmark').forEach(check => {
        check.classList.remove('selected');
        check.textContent = "";
    });
    
    // Reset file inputs
    document.querySelector('input[name="songUpload"]').value = '';
    document.querySelector('input[name="songPhoto"]').value = '';
    
    // Clear preview areas
    document.getElementById('audioPreview').innerHTML = '';
    document.getElementById('imagePreview').innerHTML = '';
    
    // Clear hidden inputs
    document.getElementById('correctOption').value = "";
    document.getElementById('question_id').value = "";
    
    // Remove existing file labels
    const existingSongLabel = document.getElementById("existingSongFile");
    if (existingSongLabel) {
        existingSongLabel.remove();
    }
    
    const existingPhotoLabel = document.getElementById("existingPhotoFile");
    if (existingPhotoLabel) {
        existingPhotoLabel.remove();
    }
    
    // Hide the modal
    modal.style.display = "none";
}

        // Add event listeners for modal closing
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking the X button
            document.querySelector('.close').addEventListener('click', function(e) {
                e.preventDefault();
                closeModal();
            });
        
            // Close modal when clicking outside
            window.addEventListener('click', function(e) {
                const modal = document.getElementById("quizModal");
                if (e.target === modal) {
                    closeModal();
                }
            });
        
            // Close modal when pressing ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeModal();
                }
            });

            document.querySelector('input[name="songUpload"]').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                if (!file.type.includes('audio/mpeg')) {
                    alert('Please upload an MP3 file');
                    this.value = ''; // Clear the file input
                    return;
                }

                validateAudioDuration(file).catch(error => {
                    alert(error);
                    this.value = ''; // Clear the file input
                });
            }
        });
    });

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
        const optionsString = cells[1].getAttribute('data-options');
        const options = optionsString ? optionsString.split(',').map(opt => opt.trim()) : [];
        const correctAnswer = cells[2].getAttribute('data-correct');

        // Get current audio and image elements
        const audioElement = cells[3].querySelector('audio');
        const imageElement = cells[4].querySelector('img');

        // Show current audio preview
        const audioPreview = document.getElementById('audioPreview');
    if (audioElement) {
        const audioSource = audioElement.querySelector('source');
        if (audioSource) {
            const audioPath = audioSource.getAttribute('src');
            audioPreview.innerHTML = `
                <div class="preview-container">
                    <p>Current Audio: ${audioPath}</p>
                    <audio controls>
                        <source src="${audioPath}" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            `;
            // Ensure the new audio element is loaded and ready to play
            const newAudio = audioPreview.querySelector('audio');
            newAudio.load();
        } else {
            audioPreview.innerHTML = '<p>No current audio</p>';
        }
    } else {
        audioPreview.innerHTML = '<p>No current audio</p>';
    }

        // Show current image preview
        const imagePreview = document.getElementById('imagePreview');
        if (imageElement) {
            const imagePath = imageElement.getAttribute('src');
            imagePreview.innerHTML = `
                <div class="preview-container">
                    <p>Current Image: ${imagePath}</p>
                    <img src="${imagePath}" alt="Current Image" style="max-width: 200px;">
                </div>
            `;
        } else {
            imagePreview.innerHTML = '<p>No current image</p>';
        }

                // Set options and mark correct answer
                options.forEach((option, index) => {
    const input = document.querySelector(`input[name="option${index + 1}"]`);
    if (input) {
        // Set initial value
        input.value = option;
        
        const checkmark = input.parentElement.querySelector('.checkmark');
        if (checkmark) {
            // Reset checkmark first
            checkmark.classList.remove('selected');
            checkmark.textContent = '';
            
            // If this is the correct answer, mark it
            if (option === correctAnswer) {
                checkmark.classList.add('selected');
                checkmark.textContent = '✓';
                document.getElementById('correctOption').value = option;
            }

            // Add input event listener to update correct answer if this option is selected
            input.addEventListener('input', function() {
                if (checkmark.classList.contains('selected')) {
                    document.getElementById('correctOption').value = this.value;
                }
            });

            // Add click event listener for checkmark
            checkmark.onclick = function() {
                // Remove selection from all checkmarks
                document.querySelectorAll('.checkmark').forEach(c => {
                    c.classList.remove('selected');
                    c.textContent = '';
                });

                // Select this checkmark
                this.classList.add('selected');
                this.textContent = '✓';

                // Update the correct answer value
                document.getElementById('correctOption').value = input.value;
            };
        }
    }
});

    // Add input event listeners to option inputs
    options.forEach((option, index) => {
        const input = document.querySelector(`input[name="option${index + 1}"]`);
        if (input) {
            input.value = option;
            const checkmark = input.parentElement.querySelector('.checkmark');
            if (checkmark) {
                // Reset the checkmark first
                checkmark.classList.remove('selected');
                checkmark.textContent = '';
                
                // If this option is the correct answer, mark it
                if (option === correctAnswer) {
                    checkmark.classList.add('selected');
                    checkmark.textContent = '✓';
                    document.getElementById('correctOption').value = option;
                }

                // Add click event listener for the checkmark
                checkmark.onclick = function() {
                    // Remove selection from all checkmarks
                    document.querySelectorAll('.checkmark').forEach(c => {
                        c.classList.remove('selected');
                        c.textContent = '';
                    });

                    // Select this checkmark
                    this.classList.add('selected');
                    this.textContent = '✓';

                    // Update the correct answer value
                    document.getElementById('correctOption').value = input.value;
                };
            }
        }
    });

    // Show current audio/image previews
    const audioCell = cells[3];
    const imageCell = cells[4];

            // Display current audio
            const currentAudio = audioCell.querySelector('audio');
            if (currentAudio) {
                const audioSource = currentAudio.querySelector('source');
                if (audioSource) {
                    const audioPath = audioSource.getAttribute('src');
                    const audioPreview = document.getElementById('audioPreview');
                    audioPreview.innerHTML = `
                        <div class="preview-container">
                            <p>Current Audio:</p>
                            <audio controls>
                                <source src="${audioPath}" type="audio/mpeg">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    `;
                }
            }
        
            if (currentImage) {
                const imagePreview = document.getElementById('imagePreview');
                imagePreview.innerHTML = `
                    <div class="preview-container">
                        <p>Current Image:</p>
                        <img src="fetch_media.php?type=image&id=${questionId}" 
                             alt="Current Image" style="max-width: 200px;">
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

                // Get the associated input's value
                const optionInput = this.parentElement.querySelector('input[type="text"]');
                // Update hidden input with the actual option text
                document.getElementById('correctOption').value = optionInput.value;
            });
        });
        
        document.getElementById('addQuestionForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    try {
        // Check if correct answer is selected
        if (!document.querySelector('.checkmark.selected')) {
            alert('Please select a correct answer');
            return;
        }

        // Validate audio file if one is selected
        const audioFile = this.querySelector('input[name="songUpload"]').files[0];
        if (audioFile) {
            // Check file type
            if (!audioFile.type.includes('audio/mpeg')) {
                throw new Error('Please upload an MP3 file');
            }

            // Check file duration
            await validateAudioDuration(audioFile);
        }

        const formData = new FormData(this);
        const questionId = document.getElementById('question_id').value;

        // Add all options to formData with their current values
        const optionInputs = document.querySelectorAll('.option-container input[type="text"]');
        optionInputs.forEach((input, index) => {
            formData.append(`option${index + 1}`, input.value.trim());
        });

        // Add the correct answer
        formData.append('correctOption', document.getElementById('correctOption').value.trim());
        formData.append('question_id', questionId);

        const response = await fetch('admin_quiz_management_2.php?action=updateQuestion', {
            method: 'POST',
            body: formData
        });

        const data = await response.json();
        
        if (data.success) {
            // Update the table row immediately
            const questionRow = document.querySelector(`tr[data-question-id="${questionId}"]`);
            if (questionRow) {
                const cells = questionRow.getElementsByTagName('td');
                const optionsText = Array.from(optionInputs).map(input => input.value.trim()).join(', ');
                cells[1].textContent = optionsText;
                cells[1].setAttribute('data-options', optionsText);
                cells[2].textContent = document.getElementById('correctOption').value;
                cells[2].setAttribute('data-correct', document.getElementById('correctOption').value);
            }
            closeModal();
            alert('Question updated successfully!');
        } else {
            throw new Error(data.error || 'Update failed');
        }
    } catch (error) {
        console.error('Error:', error);
        alert(error.message || 'Error updating question');
    }
});
    </script>

<?php 
    // Close database connection at the very end of the page
    mysqli_close($conn);
    ?>

</body>
</html>
