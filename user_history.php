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
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_footer.css">
    <style>
        #content {
            min-height: 100vh;
            justify-content: center;
            font-family: "Lalezar", system-ui;
            font-style: normal;
        }

        #history {
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
            font-family: 'Lalezar', system-ui;
        }

        table {
            background-color: white;
            border-collapse: collapse;
            justify-self: center;
            width: clamp(50%, 80vw, 80%);
        }

        td, th {
            padding: 10px;
            border: 2px solid black;
            text-align: center;
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 2vw, 18px);
        }

        td {
            font-weight: 500;
        }

        th {
            font-weight: 700;
        }

    </style>
</head>

<body>
    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <div id="login">
        <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
        <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'user_hamburger_menu.php'; ?>

    <div id='content'>
        <div id='history'>
        <h1 id='title'>History</h1>
        <table>
            <tr>
                <th>Number</th>
                <th>Quiz ID</th>
                <th>Quiz Name</th>
                <th>Result</th>
                <th>Time Used</th>
                <th>Answer Date</th>
                <th>Action</th>
            </tr>
            
            <?php
			$sql1 = "SELECT UserID FROM user WHERE Username = '$username'";
			$result1 = mysqli_query($conn, $sql1);
            if (mysqli_num_rows($result1) != 0) {
            
                $row1 = mysqli_fetch_assoc($result1);
                $user_id = $row1['UserID'];

                $sql2 = "SELECT Result, TimeUsed, Time, QuizID FROM record WHERE UserID = '$user_id' ORDER BY Time DESC";
			    $result2 = mysqli_query($conn, $sql2);

                $count = 1;
                while ($row2 = mysqli_fetch_assoc($result2)) {
                    $quiz_result = $row2['Result'];
                    $time_used = $row2['TimeUsed'];
                    $quiz_id = $row2['QuizID'];
                    $time = $row2['Time'];

                    $sql3 = "SELECT QuizName FROM quiz WHERE QuizID = '$quiz_id'";
                    $result3 = mysqli_query($conn, $sql3);
                    $row3 = mysqli_fetch_assoc($result3);
                    $quiz_name = $row3['QuizName'];

                    echo "<tr>
                    <td>$count</td>
                    <td>$quiz_id</td>
                    <td>$quiz_name</td>
                    <td>$quiz_result/5</td>
                    <td>$time_used s</td>
                    <td>$time</td>
                    <td><a href='user_history_details.php'>View Details</a></td>
                </tr>";

                $count++;
                }
            }
            ?>
        </table>
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
</html>