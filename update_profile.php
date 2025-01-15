<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login_user.php");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['ProfilePicture'])) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES['ProfilePicture']["name"]);
    if (move_uploaded_file($_FILES['ProfilePicture']["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("UPDATE user SET ProfilePicture = ? WHERE Username = ?");
        $stmt->bind_param("ss", $target_file, $username);
        if ($stmt->execute()) {
            $_SESSION['ProfilePicture'] = $target_file;
            header("Location: mainPage_user.php?status=success");
        } else {
            echo "Error updating record: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
} else {
    echo "Invalid request.";
}

$conn->close();
?>