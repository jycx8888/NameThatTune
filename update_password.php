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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['newPassword']) && isset($_POST['confirmPassword'])) {
    $newPassword = $_POST['newPassword'];
    $confirmPassword = $_POST['confirmPassword'];
    if ($newPassword === $confirmPassword) {
        $stmt = $conn->prepare("UPDATE user SET Password = ? WHERE Username = ?");
        $stmt->bind_param("ss", $newPassword, $username);
        if ($stmt->execute()) {
            header("Location: mainPage_user.php?status=success");
        } else {
            header("Location: mainPage_user.php?status=error");
        }
        $stmt->close();
    }else {
        header("Location: mainPage_user.php?status=error");
    }
}else {
    header("Location: mainPage_user.php?status=error");
}

$conn->close();
?>