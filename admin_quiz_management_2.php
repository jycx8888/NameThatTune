<?php
// Database connection
    session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$server = "localhost";
$user = "root";
$password = "";
$database = "namethattune";

// Create connection
$conn = new mysqli($server, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];


// Fetch user data from the database
$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $_SESSION['username']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    // Handle case where user data is not found
    $profile_picture_path = 'Icon/account.png'; // Default profile picture
}

// Check if 'quiz_id' is provided for editing
if (isset($_GET['quiz_id'])) {
    $quiz_id = intval($_GET['quiz_id']); // Get from URL

    // Fetch quiz data for editing
    $stmt = $conn->prepare("SELECT QuizID, GenreID, CreatedTime FROM quiz WHERE QuizID = ?");
    $stmt->bind_param("i", $quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $quiz = $result->fetch_assoc(); // Fetch data into an array
    } else {
        die("Error: Quiz not found.");
    }

    // Fetch questions related to this quiz
    $sql_questions = "SELECT * FROM question WHERE QuizID = '$quiz_id'";
    $result_questions = $conn->query($sql_questions);
} else {
    // No quiz_id provided, meaning the user is adding a new song
    $quiz = null;
    $result_questions = null;
}

$stmt = $conn->prepare("SELECT QuizID, GenreID, CreatedTime FROM quiz WHERE QuizID=?");
$stmt->bind_param("i", $quiz_id); // "i" means integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $quiz = $result->fetch_assoc(); // Fetch data into an array
} else {
    die("Error: Quiz not found.");
}

// Fetch questions related to this quiz
$sql_questions = "SELECT * FROM question WHERE QuizID='$quiz_id'";
$result_questions = $conn->query($sql_questions);

// Handle form submission for updating quiz details
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve updated data from the form
    $genre_id = $conn->real_escape_string($_POST['genre_id']);
    $created_time = $conn->real_escape_string($_POST['created_time']);

    // Update query
    $update_sql = "UPDATE quiz SET GenreID='$genre_id', CreatedTime='$created_time' WHERE QuizID='$quiz_id'";

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
    <link rel="stylesheet" href="user_header.css">
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
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <!-- Main Content Section -->
    <main>
    <form method="POST" action="admin_quiz_management_2.php">
    <h2 class="edit-quiz-header"><?php echo isset($quiz) ? 'Edit Quiz' : 'Add Song'; ?></h2>
    
    <?php if (isset($quiz)): ?>
        <input type="hidden" name="quiz_id" value="<?php echo $quiz['QuizID']; ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="genre-id">Genre</label>
        <select id="genre-id" name="genre_id">
            <option value="1" <?php if (isset($quiz) && $quiz['GenreID'] == '1') echo 'selected'; ?>>English</option>
            <option value="2" <?php if (isset($quiz) && $quiz['GenreID'] == '2') echo 'selected'; ?>>Korean</option>
            <option value="3" <?php if (isset($quiz) && $quiz['GenreID'] == '3') echo 'selected'; ?>>Japanese</option>
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
                            <td><?php echo $question['question_id']; ?></td>
                            <td><?php echo isset($question['question_text']) ? htmlspecialchars($question['question_text']) : 'No question text'; ?></td>
                            <td class="actions">
                                <a href="edit_question.php?question_id=<?php echo $question['question_id']; ?>">Edit</a> |
                                <a href="delete_question.php?question_id=<?php echo $question['question_id']; ?>">Delete</a>
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
        <button type="button" class="cancel" onclick="window.location.href='quizzes.php';">Cancel</button>
        <button type="submit" class="confirm"><?php echo isset($quiz) ? 'Save Changes' : 'Add Song'; ?></button>
    </div>
</form>
    </main>

    <div id="hamburger-menu">
        <div class="close-btn" onclick="toggleMenu()">×</div>
        <div class="profile-container">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>" alt="Profile Picture" id="profilePicture" onclick="showPopup('profilePopup')"> <!-- Display the profile picture -->
            <img src="Icon/pencil.png" alt="Edit" class="edit-icon" style="width: 60px; height: 60px;" onclick="showPopup('profilePopup')">
        </div>
        <div class="username" id="username"><?php echo htmlspecialchars($username); ?></div>
        <div class="menu-item" onclick="showPopup('usernamePopup')">Change Username</div>
        <div class="menu-item" onclick="showPopup('passwordPopup')">Change Password</div>
        <div class="menu-item" onclick="toggleSubmenu('settings-submenu')">Settings</div>
        <div id="settings-submenu" class="submenu">
            <div class="submenu-item" onclick="toggleVolumeControl()">Volume</div>
            <div class="volume-control" id="volumeControl">
                <input type="range" min="0" max="100" value="50" id="volumeSlider">
            </div>
            <div class="submenu-item">Dark Mode</div>
        </div>
        <button class="logout-btn" onclick="confirmLogout()">Log Out</button>
    </div>

    <!-- Profile Popup -->
    <div id="profilePopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('profilePopup')">&times;</span>
            <h2>Change Profile Image</h2>
            <form action="update_profile.php" method="post" enctype="multipart/form-data">
                <input type="file" name="ProfilePicture" id="profileImageInput">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Username Popup -->
    <div id="usernamePopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('usernamePopup')">&times;</span>
            <h2>Change Username</h2>
            <form onsubmit="return validateNewUsername()" action="update_username.php" method="post">
                <input type="text" name="newUsername" id="usernameInput" placeholder="Enter new username">
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Password Popup -->
    <div id="passwordPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closePopup('passwordPopup')">&times;</span>
            <h2>Change Password</h2>
            <form onsubmit="return validateNewPassword()" action="update_password.php" method="post">
                <div style="position: relative;">
                    <input type="password" name="newPassword" id="newPasswordInput" placeholder="Enter new password" required>
                    <span class="toggle-password"  style="position: absolute; right: 15px; object-fit: contain; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <img src="Icon/hide.png" alt="Show Password" onclick="togglePasswordVisibility('newPasswordInput',this)" style="width: 20px; height: 20px;">
                    </span>
                </div>
                <div style="position: relative;">
                    <input type="password" name="confirmPassword" id="confirmPasswordInput" placeholder="Confirm new password" required>
                    <span class="toggle-password"  style="position: absolute; right: 15px; object-fit: contain; top: 50%; transform: translateY(-50%); cursor: pointer;">
                        <img src="Icon/hide.png" name alt="Show Password" onclick="togglePasswordVisibility('confirmPasswordInput',this)" style="width: 20px; height: 20px;">
                    </span>
                </div>
                <button type="submit">Save</button>
            </form>
        </div>
    </div>

    <!-- Warning Popup -->
    <div id="warningPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closeWarningPopup()">&times;</span>
            <p id="warningMessage"></p>
        </div>
    </div>

    <div id="logoutOverlay" class="overlay">
        <div class="popup" id="logoutPopup">
            <p>Do you want to log out?</p>
            <button class="yes" onclick="logout()">Yes</button>
            <button class="no" onclick="closeLogoutPopup()">No</button>
        </div>
    </div>

    <script>
        function validateNewUsername() {
            const username = document.getElementById('usernameInput').value;
            
            if (username === "") {
                showWarning('Username cannot be empty.');
                return false;
            }

            return true;
        }

        function validateNewPassword() {
            const password = document.getElementById('newPasswordInput').value;
            const confirmPassword = document.getElementById('confirmPasswordInput').value;
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%#*?&])[A-Za-z\d@$!%#*?&]{8,14}$/;

            if (password === "") {
                showWarning('New Password cannot be empty.');
                return false;
            }

            if (confirmPassword === "") {
                showWarning('Confirm Password cannot be empty.');
                return false;
            }

            if (password !== confirmPassword) {
                showWarning('Passwords do not match.');
                return false;
            }

            if (!passwordRegex.test(password)) {
                showWarning('Password must be 8-14 characters long, include at least one capital letter, one number, and one special character.');
                return false;
            }

            return true;
        }

        document.getElementById('login').addEventListener('click', function() {
            document.getElementById('hamburger-menu').classList.toggle('open');
        });

        function showPopup(popupId) {
            document.getElementById(popupId).classList.add('show');
        }

        function closePopup(popupId) {
            document.getElementById(popupId).classList.remove('show');
        }

        function togglePasswordVisibility(fieldId, iconElement) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconElement);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                iconElement.src = 'Icon/show.png'; // Change to the open eye icon
            } else {
                passwordField.type = 'password';
                iconElement.src = 'Icon/hide.png'; // Change to the closed eye icon
            }
        }

        function toggleMenu() {
            document.getElementById('hamburger-menu').classList.toggle('open');
        }

        function toggleSubmenu(submenuId) {
            const submenu = document.getElementById(submenuId);
            submenu.style.display = submenu.style.display === 'flex' ? 'none' : 'flex';
        }

        function toggleVolumeControl() {
            const volumeControl = document.getElementById('volumeControl');
            volumeControl.style.display = volumeControl.style.display === 'block' ? 'none' : 'block';
        }

        function confirmLogout() {
            document.getElementById('logoutOverlay').classList.add('show');
        }

        function closeLogoutPopup() {
            document.getElementById('logoutOverlay').classList.remove('show');
        }

        function logout() {
            window.location.href = 'admin_login.php'; // Redirect to login page
        }

        document.getElementById('volumeSlider').addEventListener('input', function() {
            const volume = this.value;
            console.log('Volume:', volume); // Replace with actual volume control logic
        });

        function showWarning(message) {
            const warningPopup = document.getElementById('warningPopup');
            const warningMessage = document.getElementById('warningMessage');
            warningMessage.textContent = message;
            warningPopup.classList.add('show');
        }
        
        function closeWarningPopup() {
            const warningPopup = document.getElementById('warningPopup');
            warningPopup.classList.remove('show');
        }
    </script>

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
    </script>

<?php 
    // Close database connection at the very end of the page
    mysqli_close($conn);
    ?>

</body>
</html>
