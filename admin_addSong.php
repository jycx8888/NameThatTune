<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "NameThatTune";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
            echo "New song added successfully";
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
</head>
<body>
<h1>Add a New Song</h1>
    <form action="admin_addSong.php" method="post" enctype="multipart/form-data">
        <label for="songName">Song Name:</label>
        <input type="text" id="songName" name="songName" required><br><br>
        <label for="audioFile">Upload Audio:</label>
        <input type="file" id="audioFile" name="audioFile" accept="audio/*" required><br><br>
        <label for="imageFile">Upload Image:</label>
        <input type="file" id="imageFile" name="imageFile" accept="image/*" required><br><br>
        <input type="submit" value="Add Song">
    </form>
</body>
</html>