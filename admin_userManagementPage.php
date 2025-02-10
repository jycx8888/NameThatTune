<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost"; // Or your database server IP
$dbusername = "root";      // MySQL username
$dbpassword = "";          // MySQL password
$dbname = "namethattune";  // Database name

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
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

// Handle deletion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];

    // Delete related records in record_question table
    $delete_related_stmt = $conn->prepare("DELETE FROM record_question WHERE RecordID IN (SELECT RecordID FROM record WHERE UserID = ?)");
    $delete_related_stmt->bind_param("s", $delete_user_id);
    $delete_related_stmt->execute();
    $delete_related_stmt->close();

    // Delete related records in record table
    $delete_record_stmt = $conn->prepare("DELETE FROM record WHERE UserID = ?");
    $delete_record_stmt->bind_param("s", $delete_user_id);
    $delete_record_stmt->execute();
    $delete_record_stmt->close();

    // Delete user
    $delete_stmt = $conn->prepare("DELETE FROM user WHERE UserID = ?");
    $delete_stmt->bind_param("s", $delete_user_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('User deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete user.');</script>";
    }
    $delete_stmt->close();
    // Refresh the page
    echo "<script>window.location.href = window.location.href;</script>";
    exit();
}

// Handle edit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user_id'])) {
    $edit_user_id = $_POST['edit_user_id'];
    $edit_username = $_POST['edit_username'];
    $edit_profile_picture = $_FILES['edit_profile_picture'];

    // Handle profile picture upload
    if ($edit_profile_picture['error'] == UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($edit_profile_picture["name"]);
        move_uploaded_file($edit_profile_picture["tmp_name"], $target_file);

        $edit_stmt = $conn->prepare("UPDATE user SET Username = ?, ProfilePicture = ? WHERE UserID = ?");
        $edit_stmt->bind_param("sss", $edit_username, $target_file, $edit_user_id);
    } else {
        $edit_stmt = $conn->prepare("UPDATE user SET Username = ? WHERE UserID = ?");
        $edit_stmt->bind_param("ss", $edit_username, $edit_user_id);
    }

    if ($edit_stmt->execute()) {
        echo "<script>alert('User updated successfully!');</script>";
    } else {
        echo "<script>alert('Failed to update user.');</script>";
    }
    $edit_stmt->close();
    // Refresh the page
    echo "<script>window.location.href = window.location.href;</script>";
    exit();
}

// Handle search
$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - User Information</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
    main {
        font-family: "Lalezar", system-ui;
        font-size: 20px;
        font-weight: 1000;
        font-style: normal;
        margin: 20px;
        margin-right: auto;
        margin-left: auto;
        padding: 20px;
        background-color: #e7e3e2;
        border-radius: 8px;
        width: 80%;
        box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        min-height: 82vh; /* Ensures proper spacing */
    }

    .table {
        font-family: "Lalezar", system-ui;
        font-size: 18px;
        font-weight: 700;
        font-style: normal;
        margin: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: left;
    }

    .table th {
        background-color: #bfb5b3;
    }

    .action-button {
        padding: 6px 12px;
        background-color: #ff5c5c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .action-button:hover {
        background-color: #e04a4a;
    }

    .edit-button {
        padding: 6px 12px;
        background-color: #5c85ff;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-button:hover {
        background-color: #4a6fe0;
    }

    .back-button {
        display: inline-block;
        margin-top: 20px;
        padding: 12px 24px; /* Increased padding */
        font-size: 16px; /* Increased font size */
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* Remove borders */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor */
        text-decoration: none; /* No underline */
        display: inline-block; /* Inline-block for button-like behavior */
        box-shadow: 0 2px #999; /* Subtle shadow effect */
        width: 250px; /* Increased width */
        text-align: center; /* Center text */
    }

    .back-button:hover {
        background-color: #45a049; /* Slightly darker green on hover */
    }

    .back-button:active {
        background-color: #3e8e41; /* Even darker green on click */
        box-shadow: 0 3px #666; /* Adjust shadow on click */
        transform: translateY(2px); /* Slight button press effect */
    }

    .search-bar {
        margin-bottom: 30px;
        width: 100%; /* Make the search bar larger */
        max-width: 1000px; /* Set a maximum width */
        padding: 10px; /* Add padding for better appearance */
        font-size: 16px; /* Increase font size */
        border: 2px solid #ccc; /* Add border */
        border-radius: 10px; /* Add border radius */
    }

    .search-bar input[type="text"] {
        width: 80%; /* Increase width */
        padding: 15px; /* Increase padding */
        font-size: 18px; /* Increase font size */
        border: 1px solid #ccc; /* Add border */
        border-radius: 4px; /* Add border radius */
    }

    .search-bar button {
        padding: 15px 20px; /* Increase padding */
        font-size: 18px; /* Increase font size */
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* Remove borders */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor */
    }

    .search-bar button:hover {
        background-color: #45a049; /* Slightly darker green on hover */
    }

    /* Pop-Up styles */
    .editPopUpPage {
        display: none;
        position: fixed;
        z-index: 1;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
        padding-top: 60px;
    }

    .editPopUpPage-content {
        background-color: #fefefe;
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #888;
        width: 80%;
        max-width: 500px;
        border-radius: 10px;
    }

    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }

    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }

    .center-align {
        text-align: center;
        margin-top: 20px;
    }

    .pagination-button {
        padding: 15px 30px; /* Increase padding */
        font-size: 18px; /* Increase font size */
        background-color: #4CAF50; /* Green background */
        color: white; /* White text */
        border: none; /* Remove borders */
        border-radius: 4px; /* Rounded corners */
        cursor: pointer; /* Pointer cursor */
        margin: 5px; /* Add margin for spacing */
    }

    .pagination-button:hover {
        background-color: #45a049; /* Slightly darker green on hover */
    }
</style>
</head>
<body>

    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <main>
    <h2>User Information</h2>
    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by username or user ID" value="<?php echo htmlspecialchars($search_query); ?>" style="width: 500px;">
            <button type="submit">Search</button>
        </form>
    </div>
    <div id="table-container" style="overflow-y: auto; max-height: 400px;">
    <table class="table" id="user-table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Date Joined</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody id="table-body">
            <!-- Rows will be dynamically generated here -->
        </tbody>
    </table>
</div>
<div id="pagination-controls" style="margin-top: 20px; text-align: center;">
    <button onclick="previousPage()" id="prev-btn" class="pagination-button" style="display: none;">Previous</button>
    <button onclick="nextPage()" id="next-btn" class="pagination-button" style="display: none;">Next</button>
</div>

<!-- Back to First Page Button -->
<div id="back-to-first-page" style="text-align: center; margin-top: 20px; display: none;">
    <button onclick="resetSearch()" class="back-button">Back to Full List</button>
</div>

<!-- Back button -->
<div class="center-align">
    <button onclick="goBackToDashboard()" class="back-button">Back to Dashboard</button>
</div>
</main>

    <!-- edit pop-up page-->
    <div id="editPopUpPage" class="editPopUpPage">
        <div class="editPopUpPage-content">
            <span class="close" onclick="closeEditPopUpPage()">&times;</span>
            <h2>Edit User</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" id="edit_user_id" name="edit_user_id">
                <label for="edit_username">Enter New Username:</label>
                <input type="text" id="edit_username" name="edit_username" required>
                <br><br><label for="edit_profile_picture">Profile Picture:</label></br></br>
                <br><img id="current_profile_picture" src="" alt="Current Profile Picture" style="width:50px; height:50px; border-radius:50%; margin-top:-100px;"></br>
                <input type="file" id="edit_profile_picture" name="edit_profile_picture" accept="image/*">
                <br><br><button type="submit" class="edit-button">Save Changes</button></br></br>
            </form>
        </div>
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

    <script>
    function openEditPopUpPage(userId, username, profilePicture) {
        // Set values in the pop-up
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_username').value = username;
        document.getElementById('current_profile_picture').src = profilePicture;
    
        // Show the pop-up
        document.getElementById('editPopUpPage').style.display = "block";
    }
    
    function closeEditPopUpPage() {
        // Hide the pop-up
        document.getElementById('editPopUpPage').style.display = "none";
    }

    function goBackToDashboard() {
        window.location.href = 'admin_adminDashboard.php'; 
    }
    </script>


<script>
    const rowsPerPage = 10; // Number of rows to display per page
    let currentPage = 1; // Current page
    const users = <?php
        // Fetch user data from the database
        $sql = "SELECT UserID, Username, DateJoined, ProfilePicture FROM user WHERE UserID LIKE ? OR Username LIKE ?";
        $stmt = $conn->prepare($sql);
        $search_param = "%" . $search_query . "%";
        $stmt->bind_param("ss", $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        echo json_encode($users);
    ?>;

    // Function to render the table and manage button visibility
    function renderTable() {
    const tableBody = document.getElementById('table-body');
    const backToFirstPageButton = document.getElementById('back-to-first-page');

    tableBody.innerHTML = ''; // Clear previous content
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedUsers = users.slice(start, end);

    // Populate the table with paginated data
    paginatedUsers.forEach(user => {
        const row = `
            <tr>
                <td>${user.UserID}</td>
                <td>${user.Username}</td>
                <td>${user.DateJoined}</td>
                <td><img src="${user.ProfilePicture}" alt="Profile Picture" style="width:50px; height:50px; border-radius:50%;"></td>
                <td>
                    <form method='POST' style='display: inline;'>
                        <input type='hidden' name='delete_user_id' value='${user.UserID}' />
                        <button type='submit' class='action-button'>Delete</button>
                    </form>
                    <button class='edit-button' onclick='openEditPopUpPage("${user.UserID}", "${user.Username}", "${user.ProfilePicture}")'>Edit</button>
                </td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });

    // Update pagination controls
    document.getElementById('prev-btn').style.display = currentPage > 1 ? 'inline-block' : 'none';
    document.getElementById('next-btn').style.display = currentPage * rowsPerPage < users.length ? 'inline-block' : 'none';

    // Show or hide the back-to-first-page button based on search query
    const searchQuery = document.querySelector('input[name="search"]').value.trim();
    backToFirstPageButton.style.display = searchQuery ? 'block' : 'none';
}


    // Function to reset the table to the original unfiltered list
    function resetSearch() {
            window.location.href = 'admin_userManagementPage.php';
        }


    // Function to fetch the original users without any search filter
    async function fetchOriginalUsers() {
        const response = await fetch(window.location.href.split('?')[0] + "?reset=1");
        if (!response.ok) {
            throw new Error("Failed to fetch the original user list.");
        }
        return response.json();
    }



    function nextPage() {
    if (currentPage * rowsPerPage < users.length) {
        currentPage++;
        renderTable();
    }
}

    function previousPage() {
    if (currentPage > 1) {
        currentPage--;
        renderTable();
    }
}

    // Initial rendering
    renderTable();
</script>
</body>
</html>