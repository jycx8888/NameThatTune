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
            margin: 24px 64px;
            min-height: 100vh;
            justify-content: center;
            font-family: "Lalezar", system-ui;
            font-style: normal;
        }

        #details {
            width: 75vw;
            height: 50vh;
            min-width: fit-content;
            min-height: fit-content;
            justify-self: center;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
            padding-left: clamp(28px, 8vw, 40px);
            padding-right: clamp(28px, 8vw, 40px);
            padding-top: clamp(28px, 8vw, 40px);
            padding-bottom: clamp(28px, 8vw, 40px);
            margin: 48px 84px 24px 84px;
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
            width: 80%;
        }

        td, th {
            padding: 10px;
            border: 1px solid black;
            text-align: center;
            font-family: 'Lalezar', system-ui;
            font-size: clamp(16px, 2vw, 18px);
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
        <div id='details'>
            <h1 id='title'>History Details</h1>
            <div>
                <span>Quiz ID: Q002</span><br>
                <span>Quiz Name: Korean Quiz 1</span><br>
                <span>Result: 4/5</span><br>
                <span>Time Used: 30s</span><br>
                <span>Answer Date: 2021-07-01 21:24:11</span>
            </div><br>
            <table>
                <tr>
                    <th>Question</th>
                    <th>User Answer</th>
                    <th>Correct Answer</th>
                    <th>Result</th>
                </tr>
                <tr>
                    <td>1</td>
                    <td>Answer 1</td>
                    <td>Answer 1</td>
                    <td>Correct</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td>Answer 2</td>
                    <td>Answer 2</td>
                    <td>Correct</td>
                </tr>
                <tr>
                    <td>3</td>
                    <td>Answer 3</td>
                    <td>Answer 2</td>
                    <td>Wrong</td>
                </tr>
                <tr>
                    <td>4</td>
                    <td>Answer 4</td>
                    <td>Answer 4</td>
                    <td>Correct</td>
                </tr>
                <tr>
                    <td>5</td>
                    <td>Answer 5</td>
                    <td>Answer 5</td>
                    <td>Correct</td>
                </tr>
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