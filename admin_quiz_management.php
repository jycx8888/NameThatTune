<?php
// Database connection
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$server = 'localhost';
$user = 'root';
$password = '';
$database = 'namethattune';

$connection = mysqli_connect($server, $user, $password, $database);

if (!$connection) {
    die("Connection failed: " . mysqli_connect_error());
}

$username = $_SESSION['username'];

// Fetch user data from the database
$stmt = $connection->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
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

// Query to fetch quiz data
$query = "SELECT QuizID AS quiz_id, GenreID AS genre_id, CreatedTime AS created_time FROM quiz";
$results = mysqli_query($connection, $query);

if (!$results) {
    die("Query failed: " . mysqli_error($connection));
}


// Check if delete action is requested
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $quiz_id = $_GET['id'];

    // Validate input: Ensure it's a numeric value
    if (!is_numeric($quiz_id)) {
        die("Invalid Quiz ID.");
    }

    // Use a prepared statement to prevent SQL injection
    $stmt = $connection->prepare("DELETE FROM quiz WHERE QuizID = ?");
    $stmt->bind_param("i", $quiz_id);

    if ($stmt->execute()) {
        echo "<script>alert('Quiz deleted successfully!'); window.location.href='admin_quiz_management.php';</script>";
    } else {
        echo "<script>alert('Error deleting quiz.');</script>";
    }

    $stmt->close();
    exit(); // Prevent further script execution
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        #content {
            background-color: #f4f4f4;
            width: 80%;
            max-width: 600px;
            margin: 1rem auto;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
        }

        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .search-bar select, .search-bar input {
            padding: 0.5rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(50% - 0.5rem);
            min-width: 120px;
        }

        .search-bar button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            background-color: rgb(104, 99, 174);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        th, td {
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: rgb(104, 99, 174);
            color: white;
        }

        .actions a {
            font-size: 0.85rem;
            color: #ACD7EC;
            text-decoration: none;
        }

        .actions a:hover {
            text-decoration: underline;
            color: blue;
        }

        .back-button {
            display: block;
            width: fit-content;
            padding: 0.6rem 1rem;
            background-color: blueviolet;
            color: white;
            text-align: center;
            border-radius: 5px;
            margin: 1rem auto 0;
            font-size: 0.9rem;
        }
        .back-button:hover{
            text-decoration: underline;
            background-color: #E39FF6;
        }

    </style>
    <script>
        function addSong() {
    console.log("Add Song button clicked!"); // Debugging
    window.location.href = 'admin_quiz_management_2.php';
}
    </script>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <!-- Content -->
    <div id="content">
        <div class="section-title">Edit Quiz</div>

        <!-- Search Filter Section -->
    <div class="search-bar">
        <label for="filter">Search by:</label>
        <select id="filter">
            <option value="id">Quiz ID</option>
            <option value="genre">Genre ID</option>
            <option value="time">Created Time</option>
        </select>
        <input type="text" id="search" placeholder="Search....">
        <button onclick="performSearch()">Search</button>
        <button id="addSongBtn" onclick="moveToAddSong()" style="background-color: grey; color: white;">Add Song</button>

    </div>
        <!-- Table Section -->
        <table>
    <thead>
        <tr>
            <th>Quiz ID</th>
            <th>Genre ID</th>
            <th>Created Time</th>
            <th>Action</th>
        </tr>
        
<script>
        document.addEventListener("DOMContentLoaded", function() {
    var button = document.getElementById("addSong");
    if (button) {
        button.addEventListener("click", function() {
            console.log("Redirecting to admin_quiz_management_2.php");
            window.location.href = 'admin_quiz_management_2.php';
        });
    } else {
        console.log("Button not found.");
    }
});
</script>

    </thead>
    <tbody id="quizTable">
        <?php if (mysqli_num_rows($results) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($results)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['genre_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_time']); ?></td>
                    <td class="actions">
                    <a href="admin_quiz_management_2.php?quiz_id=<?php echo urlencode($row['quiz_id']); ?>">Edit</a>
                        <a href="admin_quiz_management.php?action=delete&id=<?php echo urlencode($row['quiz_id']); ?>" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No quizzes found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

        <!-- Back Button -->
      <!-- Back Button -->
    <button onclick="window.history.back()" class="back-button">Back</button>

    </div>

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

        function moveToAddSong() {
            window.location.href = 'admin_addQuiz.php';
        }
    </script>
    
     <?php 
    // Close database connection at the very end of the page
    mysqli_close($connection);
    ?>
</body>
</html>