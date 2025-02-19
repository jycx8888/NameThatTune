<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

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

// Generate new Song IDs and insert into song table
$result = $conn->query("SELECT SongID FROM song ORDER BY SongID DESC LIMIT 1");
$lastSongID = $result->fetch_assoc()['SongID'];
$newSongID = 'S' . str_pad((int)substr($lastSongID, 1) + 1, 3, '0', STR_PAD_LEFT);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $songName = $_POST['songName'];
    $questionID = NULL; // Question ID remains empty

    // Handle file uploads
    $audioFile = $_FILES['audioFile']['name'];
    $imageFile = $_FILES['imageFile']['name'];

    // Move uploaded files to a designated directory
    $audioTargetDir = "Question Songs/";
    $imageTargetDir = "Question Images/";
    $audioFilePath = $audioTargetDir . basename($audioFile);
    $imageFilePath = $imageTargetDir . basename($imageFile);

    if (move_uploaded_file($_FILES['audioFile']['tmp_name'], $audioFilePath) && move_uploaded_file($_FILES['imageFile']['tmp_name'], $imageFilePath)) {
        // Insert song details into the database
        $sql = "INSERT INTO song (SongID, SongName, QuestionID, SongAudio, SongImage) VALUES ('$newSongID','$songName', NULL, '$audioFilePath', '$imageFilePath')";

        if ($conn->query($sql) === TRUE) {
            
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "Sorry, there was an error uploading your files.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <style>
        #content {
            display: flex;
            flex-direction: column;
            align-items: center;
            font-family: 'Lalezar', system-ui;
            height: fit-content;
            width: 80vw;
            max-width: 600px;
            padding: 12px;
            margin: 48px auto;
            background-color: white;
            border-radius: 15px;
        }

        h1 {
            font-size: clamp(28px, 2.5vw, 32px);
            margin-bottom: 24px;
        }

        form {
            font-family: 'Lalezar', system-ui;
            font-weight: 500;
        }

        form div {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin: 6px 0;
        }

        label {
            margin-bottom: 5px;
            font-size: clamp(16px, 2vw, 18px);
        }

        input {
            font-family: 'Lalezar', system-ui;
        }

        
        input[type="text"], input[type="file"] {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 500;
            width: 100%;
            padding: 5px;
            border: 1px solid #000;
        }

        #buttons {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-top: 12px;
        }

        input[type="submit"] {
            font-size: clamp(16px, 2vw, 18px);
            font-weight: 700;
            padding: 12px 24px;
            align-self: center;
            border-radius: 15px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 12px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        input[type="button"] {
            font-size: clamp(16px, 2vw, 18px);
            font-weight: 700;
            padding: 12px 24px;
            align-self: center;
            border-radius: 15px;
            background-color: red;
            color: white;
            border: none;
            cursor: pointer;
            margin-bottom: 12px;
        }

        input[type="button"]:hover {
            background-color: darkred;
        }



    </style>
</head>

<body>
    <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div id="content">
        <h1>Add New Song</h1>
        <form action="admin_addSong.php" method="post" enctype="multipart/form-data">
            <div>
                <label for="songName">Song Name:</label>
                <input type="text" id="songName" name="songName" required><br>
            </div>
            <div>
                <label for="audioFile">Upload Audio:</label>
                <input type="file" id="audioFile" name="audioFile" accept="audio/*" required><br>
            </div>
            <div>
                <label for="imageFile">Upload Image:</label>
                <input type="file" id="imageFile" name="imageFile" accept="image/*" required><br>
            </div>
            <div id="buttons">
                <input type="submit" value="Add Song">
                <input type="button" value="Cancel" onclick="window.location.href='admin_quiz_management.php'">
            </div>
            
        </form>
    </div>



    <?php include 'admin_hamburger_menu.php'; ?>

</body>
</html>