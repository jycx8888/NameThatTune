<?php
session_start();

// Enable error logging and save to a file instead of displaying it
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log'); // Error log file
error_reporting(E_ALL);

header('Content-Type: application/json'); // Ensure JSON response

if (!isset($_SESSION['username'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
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
    error_log("Connection failed: " . $conn->connect_error);
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

// Get the data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error: " . json_last_error_msg());
    die(json_encode(['success' => false, 'message' => 'JSON decode error: ' . json_last_error_msg()]));
}

error_log("Received data: " . print_r($data, true)); // Log the received data

$userId = $data['userId'];
$quizId = $data['quizId'];
$correctAnswersCount = $data['correctAnswersCount'];
$totalQuestions = $data['totalQuestions'];
$timeTaken = $data['timeTaken'];
$userAnswers = $data['userAnswers'];

// Verify that the user exists
$stmt = $conn->prepare("SELECT UserID FROM user WHERE UserID = ?");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}
$stmt->bind_param("s", $userId); // Bind as string
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    error_log("User does not exist: " . $userId);
    die(json_encode(['success' => false, 'message' => 'User does not exist']));
}
$stmt->close();

// Calculate the correct rate for each question
foreach ($userAnswers as $answer) {
    $questionId = $answer['questionId'];
    $isCorrect = $answer['isCorrect'];

    // Update the correct rate for the question
    $stmt = $conn->prepare("SELECT CorrectRate, TotalAttempts FROM question WHERE QuestionID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("s", $questionId);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $correctRate = $row['CorrectRate'];
    $totalAttempts = $row['TotalAttempts'];
    $stmt->close();

    // Increment the total attempts
    $totalAttempts++;

    // Increment the total correct answers if the answer is correct
    if ($isCorrect) {
        $correctRate++;
    }

    // Calculate the new correct rate
    $newCorrectRate = $correctRate / $totalAttempts;

    // Update the question table with the new correct rate and total attempts
    $stmt = $conn->prepare("UPDATE question SET CorrectRate = ?, TotalAttempts = ? WHERE QuestionID = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("dii", $newCorrectRate, $totalAttempts, $questionId);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
    }
    $stmt->close();
}

// Insert the record into the record table
$stmt = $conn->prepare("INSERT INTO record (Result, Time, UserID, QuizID) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    error_log("Prepare failed: " . $conn->error);
    die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
}
$stmt->bind_param("ssis", $correctAnswersCount, $timeTaken, $userId, $quizId); // Bind UserID as string
if (!$stmt->execute()) {
    error_log("Execute failed: " . $stmt->error);
    die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
}
$recordId = $stmt->insert_id;
$stmt->close();

// Insert the record_question data
foreach ($userAnswers as $answer) {
    $questionId = $answer['questionId'];
    $userAnswer = $answer['userAnswer'];

    $stmt = $conn->prepare("INSERT INTO record_question (RecordID, QuestionID, UserAnswer) VALUES (?, ?, ?)");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        die(json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]));
    }
    $stmt->bind_param("iis", $recordId, $questionId, $userAnswer);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die(json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]));
    }
    $stmt->close();
}

$conn->close();

echo json_encode(['success' => true]);
?>