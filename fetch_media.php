<?php
session_start();

// Database connection details
$server = "localhost";
$user = "root";
$password = "";
$database = "namethattune";

if (!isset($_GET['type']) || !isset($_GET['id'])) {
    die('Missing parameters');
}

$type = $_GET['type'];
$questionId = $_GET['id'];

$conn = new mysqli($server, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$column = ($type === 'audio') ? 'SongAudio' : 'SongImage';
$contentType = ($type === 'audio') ? 'audio/mpeg' : 'image/jpeg';

$stmt = $conn->prepare("SELECT $column FROM song WHERE QuestionID = ?");
$stmt->bind_param("s", $questionId);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    header("Content-Type: $contentType");
    echo $row[$column];
}

$conn->close();
?>