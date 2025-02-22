<?php
session_start();

ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');
error_reporting(E_ALL);

header('Content-Type: application/json');

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "namethattune";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    die(json_encode(['success' => false, 'message' => 'JSON decode error: ' . json_last_error_msg()]));
}

error_log("Received data: " . print_r($data, true));

$userId = $data['userId'];
$quizId = $data['quizId'];
$recordId = $data['recordId'];
$correctAnswersCount = $data['correctAnswersCount'];
$totalQuestions = $data['totalQuestions'];
$timeTaken = $data['timeTaken'];
$startTime = $data['startTime'];
$userAnswers = $data['userAnswers'];

$stmt = $conn->prepare("SELECT UserID FROM user WHERE UserID = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}
$stmt->bind_param("s", $userId);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    error_log("User does not exist: " . $userId);
    die(json_encode(['success' => false, 'message' => 'User does not exist']));
}
$stmt->close();

$stmt = $conn->prepare("INSERT INTO record (RecordID, Result, Time, UserID, QuizID, TimeUsed) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}
$stmt->bind_param("ssssss", $recordId, $correctAnswersCount, $startTime, $userId, $quizId, $timeTaken); 
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
}
$stmt->close();

foreach ($userAnswers as $answer) {
    $questionId = $answer['questionId'];
    $userAnswer = $answer['userAnswer'];

    $stmt = $conn->prepare("INSERT INTO record_question (RecordID, QuestionID, UserAnswer) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("sss", $recordId, $questionId, $userAnswer);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
    }
    $stmt->close();
}

foreach ($userAnswers as $answer) {
    $questionId = $answer['questionId'];

    $stmt = $conn->prepare("SELECT COUNT(*) AS totalAttempts, SUM(CASE WHEN UserAnswer = (SELECT CorrectAnswer FROM question WHERE QuestionID = ?) THEN 1 ELSE 0 END) AS correctAttempts FROM record_question WHERE QuestionID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("ss", $questionId, $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalAttempts = $row['totalAttempts'];
    $correctAttempts = $row['correctAttempts'];
    $stmt->close();

    $newCorrectRate = $correctAttempts / $totalAttempts;

    $stmt = $conn->prepare("UPDATE question SET CorrectRate = ?, TotalAttempts = ? WHERE QuestionID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("dis", $newCorrectRate, $totalAttempts, $questionId);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
    }
    $stmt->close();
}

$conn->close();

echo json_encode(['success' => true]);
?>