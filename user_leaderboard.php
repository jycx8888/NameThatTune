<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: user_login.php");
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
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT ProfilePicture FROM user WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    // Handle case where user data is not found
    $profile_picture_path = 'Icon/account.png'; // Default profile picture
}

$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['update_username'])) {
        $new_username = $_POST['newUsername'];

        $stmt = $conn->prepare("UPDATE user SET Username = ? WHERE Username = ?");
        $stmt->bind_param("ss", $new_username, $username);
        if ($stmt->execute()) {
            $_SESSION['username'] = $new_username;
            $username = $new_username;
        } else {
            echo "Error updating username.";
        }

        $stmt->close();
    }

    if (isset($_POST['update_password'])) {
        $new_password =$_POST['newPassword'];
        $stmt = $conn->prepare("UPDATE user SET Password = ? WHERE Username = ?");
        $stmt->bind_param("ss", $new_password, $username);
        if ($stmt->execute()) {
        } else {
            echo "Error updating password.";
        }
        $stmt->close();
    }

    if (isset($_POST['update_profile'])) {
        $profile_picture = $_FILES['ProfilePicture']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($profile_picture);

        if (move_uploaded_file($_FILES['ProfilePicture']['tmp_name'], $target_file)) {
            $stmt = $conn->prepare("UPDATE user SET ProfilePicture = ? WHERE Username = ?");
            $stmt->bind_param("ss", $target_file, $username);
            if ($stmt->execute()) {
                $profile_picture_path = $target_file;
            } else {
                echo "Error updating profile picture.";
            }

            $stmt->close();
        } else {
            echo "Error uploading file.";
        }
    }

    // Redirect to avoid form resubmission
    header("Location: user_mainPage.php");
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header_footer.css">
    <style>
        
        #content {
            height: 100%;
            display: flex;
            justify-content: center;
        }

        #leaderboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            width: fit-content;
            height: fit-content;
            margin-top: 48px;
            padding: clamp(28px, 8vw, 40px);
            border-radius: 10px;
        }

        #leaderboard h1 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(32px, 5vw, 40px);
            font-weight: 1000;
            font-style: normal;
            margin: 0 0 24px 0;
        }

        .record {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: clamp(48px, 12vw, 68px);
            width: clamp(20em, 50vw, 30em);
            min-width: 240px;
            margin-top: 12px;
            background-color: grey;
            border-radius: 10px;
        }

        .record h3 {
            text-align: left;
            font-family: "Lalezar", system-ui;
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            font-style: normal;
        }

        .circle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 45px;
            height: 45px;
            background-color: white;
            border-radius: 50%;
            margin-left: 8px;
        }

        .circle h2 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            font-style: normal;
        }
        
        .result {
            justify-content: right;
            font-family: "Lalezar", system-ui;
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            font-style: normal;
            margin-right: 8px;
        }

        </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="\Icon\account.png" alt="avatar">
            <p>Username</p>
        </div>
    </div>

    <div id="content">
        <div id="leaderboard">
            <h1>Leaderboard</h1>
            <div class="record">
                <div class="circle">
                    <h2>1</h2>
                </div>
                <h3>kumkum</h3>
                <div class="result">5/5(1s)</div>
            </div>
            <div class="record">
                <div class="circle">
                    <h2>2</h2>
                </div>
                <h3>Diddy</h3>
                <div class="result">5/5(2s)</div>
            </div>
            <div class="record">
                <div class="circle">
                    <h2>3</h2>
                </div>
                <h3>Mervin Ooi</h3>
                <div class="result">5/5(3s)</div>
            </div>
            <div class="record">
                <div class="circle">
                    <h2>4</h2>
                </div>
                <h3>player</h3>
                <div class="result">5/5(4s)</div>
            </div>
            <div class="record">
                <div class="circle">
                    <h2>5</h2>
                </div>
                <h3>Username2</h3>
                <div class="result">5/5(5s)</div>
            </div>
            <div class="record">
                <div class="circle">
                    <h2>10</h2>
                </div>
                <h3>Hehe(You)</h3>
                <div class="result">5/5(10s)</div>
            </div>
        </div>
    </div>
    
</body>
</html>