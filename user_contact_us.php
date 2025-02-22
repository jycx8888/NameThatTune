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
    <title>NameThatTune</title>
    <link rel="icon" href="icon/logo.jpg" type="image/png">
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_footer.css">
    <style>
        
        #content {
            flex-grow: 5;
            display: flex;
            justify-content: center;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
        }

        .container {
            width: clamp(420px, 50%, 800px);
            margin: 20px auto;
            padding: 20px 20px 10px;
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            font-size: clamp(18px, 3vw, 22px);

        }

        .container-short-bottom {
            margin-bottom: 5px;
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .contact-info h1 {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            margin: 20px 0;
        }

        .contact-info p {
            margin: 10px 24px;
            text-align: center;
        }

        .contact-info a {
            text-decoration: none;
            color: #4CAF50;
            margin: 10px 0;
            transition: color 0.3s;
        }

        .contact-info a:hover {
            color: #388E3C;
        }

        .icon {
            margin-right: 10px;
        }

    </style>
</head>

<body>
    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <?php
        if (isset($_SESSION['username'])) {
            $profile_picture_path = htmlspecialchars($profile_picture_path);
            $username = htmlspecialchars($username);
            echo "<div id='login'>
            <img src='$profile_picture_path'>
            <p>$username</p>
            </div>";
        } else {
            echo "<div id='login' onclick='redirectToLogin()'>
            <p>Login</p>
            </div>";
        }
        ?>
    </div>

    <?php
    include 'user_hamburger_menu.php';
    ?>

   <div id="content">
        <div class="container">
            <div class="contact-info">
                <h1>Contact Us</h1>
                <p>We'd love to hear from you! Connect with us through any of the following channels:</p>
                <a href="https:
                    <span class="icon">📸</span> Follow us on Instagram
                </a>
                <a href="https:
                    <span class="icon">📘</span> Like us on Facebook
                </a>
                <a href="mailto:namethattune@gmail.com">
                    <span class="icon">✉️</span> Email us at namethattune@gmail.com
                </a>
                <a href="tel:019-782-6732">
                    <span class="icon">📞</span> Call us at 019-782-6732
                </a>
            </div>
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
