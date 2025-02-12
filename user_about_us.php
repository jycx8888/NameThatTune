<?php
session_start();

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];

}

$servername = "localhost";
$dbusername = "root"; // Database username
$dbpassword = ""; // Database password
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    include 'user_fetch_profile.php';
}

$conn->close();
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
            height: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 5vw;
        }

        #content div {
            background-color: white;
            width: 65vw;
            height: auto;
            margin: 48px 0 180px 0 ;
            border-radius: 10px;
            overflow: auto;
        }

        #content h2 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(0.65em, 5.5vw, 2.2em);
            font-weight: 1000;
            font-style: normal;
            margin: 0;
            padding-top: clamp(0.8em, 6vw, 1em);
            text-align: center;
        }

        #content p {
            font-family: "Lalezar", system-ui;
            font-size: clamp(1em, 3vw, 1.3em);
            font-weight: 700;
            font-style: normal;
            margin: 0 10%;
            padding: 24px 0 48px 0;
            text-align: justify;
        }

        </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
            <?php
            if (isset($_SESSION['username'])) {
                $profile_picture_path = htmlspecialchars($profile_picture_path);
                $username = htmlspecialchars($username);
                echo "<div id='login'>
                <img src='$profile_picture_path'>
                <p>$username</p>";
            } else {
                echo "<div id='login' onclick='redirectToLogin()'>
                <p>Login</p>
                </div>";
            }
            ?>
        </div>
    </div>

    <?php include 'user_hamburger_menu.php'; ?>

    <div id="content">
        <div>
            <h2>About Us</h2>
            <p>At NameThatTune, we bring the excitement and passion of music to life with engaging, interactive games crafted for music lovers of all kinds. Since our launch, we have dedicated ourselves to creating a platform where music fans can test their skills, learn new tunes, and share memorable experiences. In 2024, we are proud to introduce an upgraded gaming experience, complete with new challenges, modes, and ways to connect with friends. Whether you’re a seasoned music expert or a casual listener, you’ll find daily quizzes, song identification games, and challenges that push the limits of your musical knowledge. Dive into the world of music, compete with others, and explore your favorite songs like never before.</p>
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