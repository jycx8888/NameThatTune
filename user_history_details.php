<?php
session_start();

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "namethattune";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

include 'user_fetch_profile.php';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune</title>
    <link rel="icon" href="icon/logo.jpg" type="image/png">
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_footer.css">
    <style>
        #content {
            font-family: "Lalezar", system-ui;
            margin: 24px 64px;
            min-height: 100vh;
            justify-content: center;
            font-style: normal;
        }

        #details {
            width: fit-content;
            height: fit-content;
            min-width: 75vw;
            min-height: 50vh;
            justify-self: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            padding-left: clamp(28px, 8vw, 40px);
            padding-right: clamp(28px, 8vw, 40px);
            padding-top: 12px;
            padding-bottom: 24px;
            margin: 24px 84px 24px 84px;
        }

        #title {
            font-size: clamp(24px, 3vw, 32px);
            font-weight: 1000;
        }

        p {
            font-family: "Lalezar", system-ui;
            font-weight: 500;
            line-height: 140%;
            font-size: clamp(14px, 2vw, 18px)
        }

        table {
            background-color: white;
            border-collapse: collapse;
            justify-self: center;
            width: 80%;
        }

        td, th {
            padding: 10px;
            border: 2px solid black;
            text-align: center;
            font-size: clamp(14px, 2vw, 18px);
        }

        td {
            font-weight: 500;
        }

        th {
            font-weight: 700;
        }

        input[type=button] {
            display: block;
            justify-self: center;
            background-color: rgb(91, 75, 193);
            font-family: 'Lalezar', system-ui;
            font-weight: 700;
            color: white;
            border-radius: 10px;
            padding: 12px 24px;
            margin: 24px 0 0 0;
            border: none;
            cursor: pointer;
            font-size: 16px;
        }

    </style>
</head>

<body>
    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <div id="login">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> 
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php
    include 'user_hamburger_menu.php'; 

    $record_id = $_GET['record_id'];
    $sql1 = "SELECT * FROM record WHERE RecordID = '$record_id'";
    $result1 = mysqli_query($conn, $sql1);
    $row1 = mysqli_fetch_assoc($result1);
    $quiz_id = $row1['QuizID'];
    $result = $row1['Result'];
    $time_used = $row1['TimeUsed'];
    $answer_date = $row1['Time'];

    $sql2 = "SELECT QuizName FROM quiz WHERE QuizID = '$quiz_id'";
    $result2 = mysqli_query($conn, $sql2);
    $row2 = mysqli_fetch_assoc($result2);
    $quiz_name = $row2['QuizName'];

    $sql3 = "SELECT * FROM record_question WHERE RecordID = '$record_id'";
    $result3 = mysqli_query($conn, $sql3);
    
    echo"<div id='content'>
        <div id='details'>
            <h1 id='title'>History Details</h1>
            <div>
                <p>Quiz ID: $quiz_id <br>
                Quiz Name: $quiz_name <br>
                Result: $result/5 <br>
                Time Used: $time_used"; echo"s <br>
                Answer Date: $answer_date </p>
            </div>
            <table>
                <tr>
                    <th>Question</th>
                    <th>Your Answer</th>
                    <th>Correct Answer</th>
                    <th>Result</th>
                </tr>";

                while ($row3 = mysqli_fetch_assoc($result3)) {
                    $question_id = $row3['QuestionID'];
                    $user_answer = $row3['UserAnswer'];

                    $sql4 = "SELECT CorrectAnswer FROM question WHERE QuestionID = '$question_id'";
                    $result4 = mysqli_query($conn, $sql4);
                    $row4 = mysqli_fetch_assoc($result4);
                    $correct_answer = $row4['CorrectAnswer'];

                    if ($user_answer == $correct_answer) {
                        $isCorrect = "Correct";
                    } else {
                        $isCorrect = "Wrong";
                    }

                    echo "<tr>
                        <td>$question_id</td>
                        <td>$user_answer</td>
                        <td>$correct_answer</td>
                        <td>$isCorrect</td>
                    </tr>";
                }
    ?>

            </table>
            <input type='button' value='Back' onclick='directToHistory()'>
        </div>
    </div>

    <div id="footer">
        <ul class="nav">
            <li><a href="user_about_us.php">About Us</a></li>
            <li><a href="user_terms_and_conditions.php">Terms and Conditions</a></li>
            <li><a href="user_privacy_policy.php">Privacy Policy</a></li>
            <li><a href="user_contact_us.php">Contact Us</a></li>
        </ul>
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

</body>

<script>
    function directToHistory() {
        window.location.href = "user_history.php";
    }
</script>
</html>