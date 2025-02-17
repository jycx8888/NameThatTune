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

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Admin Dashboard</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>

        main {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 72px);
        }

        #options {
            display: flex;
            width: 80vw;
            min-width: 300px;
            justify-content: space-evenly;
        }

        .option-box {
            height: clamp(120px, 20vw, 300px);
            width: clamp(120px, 20vw, 300px);
            background-color: #584cba;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: "Lalezar", system-ui;
            font-weight: 700;
            font-size: clamp(12px, 3vw, 32px);
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .option-box:hover {
            background-color: #17066e;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <main>
        <div id="options">
            <div class="option-box" onclick="window.location.href='admin_quiz_management.   php'">
                Quiz Management
            </div>
            <div class="option-box" onclick="window.location.   href='admin_userManagementPage.php'">
                User Management
            </div>
            <div class="option-box" onclick="window.location.href='admin_analyticPage.  php'">
                Analytics
            </div>
        </div>

    </main>

    <?php include 'admin_hamburger_menu.php'; ?>
</body>
</html>
