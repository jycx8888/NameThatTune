<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: user_mainPage.php");
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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newUsername'])) {
    $newUsername = $_POST['newUsername'];
    $stmt = $conn->prepare("UPDATE user SET Username = ? WHERE Username = ?");
    $stmt->bind_param("ss", $newUsername, $username);
    if ($stmt->execute()) {
        $_SESSION['username'] = $newUsername;
        header("Location: user_mainPage.php?status=success");
    } else {
        header("Location: user_mainPage.php?status=error");
    }
    $stmt->close();
}

$conn->close();
?>