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
            height: max-content;
            display: flex;
            justify-content: center;
        }

        #terms {
            width: 75vw;
            height: auto;
            margin: 48px 0;
            padding: 12px 20px;
            background-color: white;
            border-radius: 10px;
            overflow: auto;
        }

        #terms h2 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(0.65em, 5.5vw, 2.2em);
            font-weight: 1000;
            font-style: normal;
            margin-top: 12px;
            text-align: center;
        }

        #terms .ol1 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(1rem, 3vw, 1.3rem);
            font-weight: 700;
            font-style: normal;
            margin: 12px 24px 0 24px;
        }

        #terms .ol2 {
            font-family: "Lalezar", system-ui;
            font-size: clamp(1rem, 3vw, 1.3rem);
            font-weight: 700;
            font-style: normal;
            text-align: start;
        }

        #terms ol {
            counter-reset: item;
        }

       #terms ol > li {
            display: block;
            margin: 12px 0;
        }

        #terms ol > li:before {
            content: counters(item, ".") ": ";
            counter-increment: item;
        }

        #terms ol ol {
            counter-reset: item;
        }

        #terms ol ol > li:before {
            content: counters(item, ".") ": ";
            counter-increment: item;
        }
        
        </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <?php

            if (isset($_SESSION['username'])) {
                $profile_picture_path = htmlspecialchars($profile_picture_path);
                $username = htmlspecialchars($username);
                echo "<img src='$profile_picture_path'>";
                echo "<p>$username</p>";
            } else {
                echo "<div id='login' onclick='redirectToLogin()'>
                 <p>Login</p>
                 </div>";
            }
            ?>

        </div>
    </div>

    <div id="content">
        <div id="terms">
            <h2>Terms and Conditions</h2>
            <ol class="ol1">
                <li>Introduction
                    <ol class="ol2">
                        <li>Welcome to NameThatTune! By accessing or using the app, website, or any associated services, you agree to comply with these Terms and Conditions. If you do not agree, please refrain from using our services.<br></li>
                    </ol>
                </li>
                <li>Eligibility
                    <ol class="ol2">
                        <li>You must be at least 12 years old to participate in the game.</li>
                        <li>If you are under 18, you must have parental or guardian consent.</li>
                    </ol>
                </li>
                <li>Game Rules
                    <ol class="ol2">
                        <li>Players must identify the song based on the audio clip provided.</li>
                        <li>Any form of cheating, including but not limited to the use of external tools or collusion with others, is strictly prohibited.</li>
                        <li>Players who violate the rules may be disqualified and have their accounts suspended.</li>
                    </ol>
                </li>
                <li>Account Responsibility
                    <ol class="ol2">
                        <li>Players are responsible for maintaining the confidentiality of their account credentials.</li>
                        <li>Sharing or transferring accounts is not permitted.</li>
                    </ol>
                </li>
                <li>Intellectual Property
                    <ol class="ol2">
                        <li>All content in NameThatTune, including audio clips, graphics, and design, is protected by copyright laws.</li>
                        <li>You are not permitted to reproduce, distribute, or modify any part of the app or its content without prior written consent.</li>
                    </ol>
                </li>
                <li>Privacy Policy
                    <ol class="ol2">
                        <li>Your data will be collected and handled in accordance with our <a href="user_privacy_policy.php">Privacy Policy</a>.</li>
                        <li>By using the website, you consent to the collection and use of your data for game-related purposes.</li>
                    </ol>
                </li>
                <li>Prohibited Conduct
                    <ol class="ol2">
                        <li>Use the website for illegal purposes.</li>
                        <li>Attempt to hack, disrupt, or manipulate the game mechanics or other players.</li>
                        <li>Post or share offensive or inappropriate content within the website or related communities.</li>
                    </ol>
                </li>
                <li>Limitation of Liability
                    <ol class="ol2">
                        <li>NameThatTune and its developers are not responsible for technical issues, including app crashes, data loss, or delays.</li>
                        <li>We are not liable for any disputes or losses arising from the use of the website.</li>
                    </ol>
                </li>
                <li>Updates and Modifications
                    <ol class="ol2">
                        <li>We reserve the right to update these Terms and Conditions at any time.</li>
                        <li>Continued use of the website after updates constitutes acceptance of the revised terms.</li>
                    </ol>
                </li>
                <li>Termination
                    <ol class="ol2">
                        <li>We may terminate your account at any time for violation of these Terms and Conditions.</li>
                        <li>You may discontinue your use of the website at any time.</li>
                    </ol>
                </li>
                <li>Governing Law
                    <ol class="ol2">
                        <li>These terms shall be governed by the laws of Malaysia.</li>
                    </ol>
                </li>
                <li>Contact Us
                    <ol class="ol2">
                        <li>For any questions regarding these Terms and Conditions, please contact us at 019-999-9999.</li>
                        <li>By using NameThatTune, you confirm that you have read, understood, and agreed to these Terms and Conditions.</li>
                    </ol>
                </li>

            </ol>
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
    function redirectToLogin() {
    window.location.href = "user_login.php";
    }
</script>
</html>