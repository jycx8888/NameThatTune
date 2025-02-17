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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quizName = $_POST['quizName'];
    $genreID = $_POST['genreID'];
    $questions = json_decode($_POST['questions'], true);

    if (empty($quizName) || empty($genreID) || empty($questions)) {
        echo "<script>alert('Please fill in all required fields.'); window.history.back();</script>";
        exit();
    }

    // Create connection
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Generate new Quiz ID
    $result = $conn->query("SELECT QuizID FROM quiz ORDER BY QuizID DESC LIMIT 1");
    $lastQuizID = $result->fetch_assoc()['QuizID'];
    $newQuizID = 'Q' . str_pad((int)substr($lastQuizID, 1) + 1, 3, '0', STR_PAD_LEFT);

    // Insert into quiz table
    $stmt = $conn->prepare("INSERT INTO quiz (QuizID, GenreID, CreatedTime, QuizName) VALUES (?, ?, NOW(), ?)");
    $stmt->bind_param("sss", $newQuizID, $genreID, $quizName);
    $stmt->execute();

    // Generate new Question IDs and insert into question table
    $result = $conn->query("SELECT QuestionID FROM question ORDER BY QuestionID DESC LIMIT 1");
    $lastQuestionID = $result->fetch_assoc()['QuestionID'];
    $newQuestionID = (int)substr($lastQuestionID, 1);

    // Generate new Song IDs and insert into song table
    $result = $conn->query("SELECT SongID FROM song ORDER BY SongID DESC LIMIT 1");
    $lastSongID = $result->fetch_assoc()['SongID'];
    $newSongID = (int)substr($lastSongID, 1);

    // Generate new Option IDs and insert into option table
    $result = $conn->query("SELECT OptionID FROM option ORDER BY OptionID DESC LIMIT 1");
    $lastOptionID = $result->fetch_assoc()['OptionID'];
    $newOptionID = (int)substr($lastOptionID, 1);

    foreach ($questions as $index => $question) {
        $newQuestionID++;
        $questionID = 'T' . str_pad($newQuestionID, 3, '0', STR_PAD_LEFT);
        $correctAnswer = $question['correctAnswer'];
        $options = $question['options'];
        $songName = $question['correctAnswer'];
        $songAudio = $question['songAudio'];
        $songImage = $question['songImage'];


        // Insert into question table
        $stmt = $conn->prepare("INSERT INTO question (QuestionID, QuizID, CorrectRate, CorrectAnswer, TotalAttempts) VALUES (?, ?, 0, ?, 0)");
        $stmt->bind_param("sss", $questionID, $newQuizID, $correctAnswer);
        $stmt->execute();

        // Insert into song table
        $newSongID++;
        $songID = 'S' . str_pad($newSongID, 3, '0', STR_PAD_LEFT);
        $stmt = $conn->prepare("INSERT INTO song (SongID, QuestionID, SongName, SongAudio, SongImage) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $songID, $questionID, $songName, $songAudio, $songImage);
        $stmt->execute();

        // Insert into option table
        foreach ($options as $option) {
            $newOptionID++;
            $optionID = 'O' . str_pad($newOptionID, 3, '0', STR_PAD_LEFT);
            $stmt = $conn->prepare("INSERT INTO option (OptionID, QuestionID, OptionName) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $optionID, $questionID, $option);
            $stmt->execute();
        }
    }
    echo "<script>alert('Quiz saved successfully!'); window.location.href='admin_quiz_management.php';</script>";
    exit();
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
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            align-self: center;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1000px;
            margin-top: 60px;
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
                    <th>New Question No</th>
                    <th>Options</th>
                    <th>Answer</th>
                    <th>MP3 File</th>
                    <th>Photo File</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
        <br><br>

        <button type="button" onclick="openModal()">Add Question</button>
        <button type="button" onclick="window.location.href='admin_quiz_management.php'">Cancel</button>
        <button type="button"onclick="uploadQuiz()">Upload</button>
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
            <button type="submit">Add Question</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
        </div>
    </div>

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
                            <a href="#" onclick="editQuestion(this)">Edit</a> |
                            <a href="#" onclick="deleteQuestion(this)">Delete</a>
                        </td>
                    `;
                    tableBody.appendChild(newRow);
                }
                
                closeModal();
            }
        };

        // Replace the existing deleteQuestion function with this:

        function deleteQuestion(link) {
            if (confirm("Are you sure you want to delete this question?")) {
                const row = link.closest("tr");
                row.parentNode.removeChild(row);
                updateQuizNumbers();
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

            const quizNo = cells[0].textContent;
            const optionsText = cells[1].textContent.split(", ");
            const correctAnswer = cells[2].textContent;
            const existingSongFileName = cells[3].textContent;
            const existingPhotoFileName = cells[4].textContent;

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

        function uploadQuiz() {
            const quizName = document.getElementById('questionId').value;
            const genreID = document.getElementById('options').value;
            const questions = [];

            document.querySelectorAll('table tbody tr').forEach(row => {
                const options = row.cells[1].textContent.split(', ');
                const correctAnswer = row.cells[2].textContent;
                const songName = row.cells[3].textContent;
                const songAudio = row.cells[4].textContent;
                const songImage = row.cells[5].textContent;

                questions.push({
                    options,
                    correctAnswer,
                    songName,
                    songAudio,
                    songImage
                });
            });

            if (!quizName || !genreID || questions.length === 0) {
                alert('Please fill in all required fields.');
                return;
            }

            const formData = new FormData();
            formData.append('quizName', quizName);
            formData.append('genreID', genreID);
            formData.append('questions', JSON.stringify(questions));

            fetch('admin_addQuiz.php', {
                method: 'POST',
                body: formData
            }).then(response => {
                if (response.ok) {
                    window.location.href = 'admin_quiz_management.php';
                } else {
                    alert('Failed to upload quiz.');
                }
            });
        }
    </script>
</body>
</html>
