<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost"; 
$dbusername = "root";      
$dbpassword = "";          
$dbname = "namethattune";  

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];


$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    
    $profile_picture_path = 'Icon/account.png'; 
} 


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $delete_user_id = $_POST['delete_user_id'];

    
    $delete_related_stmt = $conn->prepare("DELETE FROM record_question WHERE RecordID IN (SELECT RecordID FROM record WHERE UserID = ?)");
    $delete_related_stmt->bind_param("s", $delete_user_id);
    $delete_related_stmt->execute();
    $delete_related_stmt->close();

    
    $delete_record_stmt = $conn->prepare("DELETE FROM record WHERE UserID = ?");
    $delete_record_stmt->bind_param("s", $delete_user_id);
    $delete_record_stmt->execute();
    $delete_record_stmt->close();

    
    $delete_stmt = $conn->prepare("DELETE FROM user WHERE UserID = ?");
    $delete_stmt->bind_param("s", $delete_user_id);
    if ($delete_stmt->execute()) {
        echo "<script>alert('User deleted successfully!');</script>";
    } else {
        echo "<script>alert('Failed to delete user.');</script>";
    }
    $delete_stmt->close();
    
    echo "<script>window.location.href = window.location.href;</script>";
    exit();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user_id'])) {
    $edit_user_id = $_POST['edit_user_id'];
    $edit_username = $_POST['edit_username'];
    $edit_profile_picture = $_FILES['edit_profile_picture'];

    
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
    
    echo "<script>window.location.href = window.location.href;</script>";
    exit();
}


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
    <title>NameThatTune</title>
    <link rel="icon" href="icon/logo.jpg" type="image/png">
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>

    main {
        display: flex;
        flex-direction: column;
        font-family: "Lalezar", system-ui;
        font-weight: 1000;
        font-style: normal;
        justify-self: center;
        align-self: center;
        margin-top: 24px;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
        width: 80%;
        height: fit-content;
        min-height: 80vh;
    }

    main h2 {
        font-size: clamp(24px, 2vw, 32px);
        margin: 0 0 24px 0;
    }

    .table {
        font-family: "Lalezar", system-ui;
        font-size: clamp(14px, 2vw, 18px);
        font-weight: 700;
        font-style: normal;
        margin: 0;
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #ccc;
        padding: 10px;
        text-align: center;
    }

    .table th {
        background-color: #584cba;
        color: white;
    }

    .action-button {
        font-family: 'Lalezar', system-ui;
        font-size: (14px, 2vw, 18px);
        font-weight: 700;
        padding: 6px 12px;
        background-color: #ff5c5c;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .action-button:hover {
        background-color: darkred;
    }

    .edit-button {
        font-family: 'Lalezar', system-ui;
        font-size: (14px, 2vw, 18px);
        font-weight: 700;
        padding: 6px 12px;
        background-color: #584cba;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .edit-button:hover {
        background-color: #17066e;
    }

    .back-button {
        display: block;
        padding: 12px 0;
        font-family: 'Lalezar', system-ui;
        font-size: clamp(14px, 2vw, 16px);
        font-weight: 700;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer; 
        width: clamp(160px, 20vw, 200px);
        text-align: center;
        justify-self: center;
    }

    .back-button:hover {
        background-color: #45a049;
    }

    .back-button:active {
        background-color: #3e8e41;
        box-shadow: 0 3px #666;
        transform: translateY(2px);
    }

    .search-bar {
        margin-bottom: 24px;
        border-radius: 10px;
    }

    .search-bar input {
        width: 30vw;
        min-width: 200px;
        padding: 10px 12px;
        font-family: 'Lalezar', system-ui;
        font-weight: 700;
        font-size: clamp(14px, 2vw, 16px);
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    .search-bar button {
        padding: 10px 20px;
        font-family: 'Lalezar', system-ui;
        font-size: clamp(14px, 2vw, 16px);
        font-weight: 700;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    .search-bar button:hover {
        background-color: #45a049;
    }

   
    .editPopUpPage {
        font-family: 'Lalezar', system-ui;
        font-weight: 700;
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
        padding: 15px 30px;
        font-size: 18px;
        background-color: #4CAF50;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        margin: 5px;
    }

    .pagination-button:hover {
        background-color: #45a049;
    }

    #edit-user-form {
        font-size: clamp(14px, 1.5vw, 16px);
    }

    #current_profile_picture {
        width: 10rem;
        height: 10rem;
    }

    #save-changes-button {
        font-size: clamp(14px, 1.5vw, 16px);
    }

</style>
</head>
<body>
    <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login" onclick="">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>

    <main>
    <h2>User Information</h2>

    <div class="search-bar">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search by username or user ID" value="<?php echo htmlspecialchars($search_query); ?>">
            <button type="submit">Search</button>
        </form>
    </div>

    <div id="table-container">
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
        </tbody>
    </table>
</div>

<div id="pagination-controls">
    <button onclick="previousPage()" id="prev-btn" class="pagination-button">Previous</button>
    <button onclick="nextPage()" id="next-btn" class="pagination-button">Next</button>
</div>

<div id="back-to-first-page">
    <button onclick="resetSearch()" class="back-button">Back to Full List</button>
</div>

<div class="center-align">
    <button onclick="goBackToDashboard()" class="back-button">Back to Dashboard</button>
</div>
</main>

    <div id="editPopUpPage" class="editPopUpPage">
        <div class="editPopUpPage-content">
            <span class="close" onclick="closeEditPopUpPage()">&times;</span>
            <h2>Edit User</h2>
            <form id=edit-user-form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" id="edit_user_id" name="edit_user_id">
                <label id="new-username-label" for="edit_username">Enter New Username:</label>
                <input type="text" id="edit_username" name="edit_username" required>
                <br><br><label id="profile-picture-label" for="edit_profile_picture">Profile Picture:</label></br></br>
                <br><img id="current_profile_picture" src="" alt="Current Profile Picture"></br>
                <input type="file" id="edit_profile_picture" name="edit_profile_picture" accept="image/*">
                <br><br><button id="save-changes-button" type="submit" class="edit-button">Save Changes</button></br></br>
            </form>
        </div>
    </div>
    
    <script>
    

    function openEditPopUpPage(userId, username, profilePicture) {
        
        document.getElementById('edit_user_id').value = userId;
        document.getElementById('edit_username').value = username;
        document.getElementById('current_profile_picture').src = profilePicture;
    
        
        document.getElementById('editPopUpPage').style.display = "block";
    }
    
    function closeEditPopUpPage() {
        
        document.getElementById('editPopUpPage').style.display = "none";
    }

    function goBackToDashboard() {
        window.location.href = 'admin_adminDashboard.php'; 
    }
    </script>


<script>
    const rowsPerPage = 10; 
    let currentPage = 1; 
    const users = <?php
        
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

    
    function renderTable() {
    const tableBody = document.getElementById('table-body');
    const backToFirstPageButton = document.getElementById('back-to-first-page');

    tableBody.innerHTML = ''; 
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    const paginatedUsers = users.slice(start, end);

    
    paginatedUsers.forEach(user => {
        const row = `
            <tr>
                <td>${user.UserID}</td>
                <td>${user.Username}</td>
                <td>${user.DateJoined}</td>
                <td><img src="${user.ProfilePicture}" alt="Profile Picture" style="width:50px; height:50px; border-radius:50%;"></td>
                <td>
                    <button class='edit-button' onclick='openEditPopUpPage("${user.UserID}", "${user.Username}", "${user.ProfilePicture}")'>Edit</button>
                    <form method='POST' style='display: inline;'>
                        <input type='hidden' name='delete_user_id' value='${user.UserID}' />
                        <button type='submit' class='action-button'>Delete</button>
                    </form>
                </td>
            </tr>`;
        tableBody.insertAdjacentHTML('beforeend', row);
    });

    
    document.getElementById('prev-btn').style.display = currentPage > 1 ? 'inline-block' : 'none';
    document.getElementById('next-btn').style.display = currentPage * rowsPerPage < users.length ? 'inline-block' : 'none';

    
    const searchQuery = document.querySelector('input[name="search"]').value.trim();
    backToFirstPageButton.style.display = searchQuery ? 'block' : 'none';
}


    
    function resetSearch() {
            window.location.href = 'admin_userManagementPage.php';
        }


    
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

    
    renderTable();
</script>
</body>
</html>