<?php
session_start();

$servername = "localhost"; // Or your database server IP
$dbusername = "root";      // MySQL username
$dbpassword = "";          // MySQL password
$dbname = "namethattune";  // Database name

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
        $target_dir = "";
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
    <link rel="stylesheet" href="user_header_footer.css">
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
            padding: 8px 16px;
            background-color: #ccc;
            color: black;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-button:hover {
            background-color: #b0b0b0;
        }

        .search-bar {
            margin-bottom: 30px;
        }

        /* Modal styles */
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
    </style>
</head>
<body>

    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="Icon/account.png" alt="avatar">
            <p>Username</p>
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
    <table class="table">
        <thead>
            <tr>
                <th>User ID</th>
                <th>Username</th>
                <th>Date Joined</th>
                <th>Profile Picture</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Fetch user data based on search query
            $sql = "SELECT UserID, Username, DateJoined, ProfilePicture FROM user WHERE UserID LIKE ? OR Username LIKE ?";
            $stmt = $conn->prepare($sql);
            $search_param = "%" . $search_query . "%";
            $stmt->bind_param("ss", $search_param, $search_param);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $userId = htmlspecialchars($row['UserID']);
                    $username = htmlspecialchars($row['Username']);
                    $profilePicture = htmlspecialchars($row['ProfilePicture']);
                    echo "<tr>
                            <td>{$userId}</td>
                            <td>{$username}</td>
                            <td>{$row['DateJoined']}</td>
                            <td><img src='{$profilePicture}' alt='Profile Picture' style='width:50px; height:50px; border-radius:50%;'></td>
                            <td>
                                <form method='POST' style='display: inline;'>
                                    <input type='hidden' name='delete_user_id' value='{$userId}'>
                                    <button type='submit' class='action-button'>Delete</button>
                                </form>
                                <button class='edit-button' onclick='openEditPopUpPage(\"{$userId}\", \"{$username}\", \"{$profilePicture}\")'>Edit</button>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='5'>No users found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="Icon/facebook.png" alt="facebook" id="facebook">
                <img src="Icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

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


    <script>
    function openEditPopUpPage(userId, username, profilePicture) {
        // Set values in the modal
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_username').value = username;
        document.getElementById('current_profile_picture').src = profilePicture;
    
        // Show the modal
        document.getElementById('editPopUpPage').style.display = "block";
    }
    
    function closeEditPopUpPage() {
        // Hide the modal
        document.getElementById('editPopUpPage').style.display = "none";
    }
    
    </script>
</body>
</html>