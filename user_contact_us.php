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
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        
        #content {
            flex-grow: 5; /* This ensures content takes up remaining space */
            display: flex;
            justify-content: center;
        }

        .container {
            font-family: "Lalezar", system-ui;
            max-width: 800px;
            margin: 20px auto;
            padding: 20px 20px 10px; /* Reduced the padding-bottom from 20px to 10px */
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .container-short-bottom {
            margin-bottom: 5px; /* Reduced from 10px to 5px */
        }
        
        .contact-info {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .contact-info a {
            text-decoration: none;
            color: #4CAF50;
            font-size: 1.2rem;
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
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

   <div id="content">

   
    <div class="container">
        <div class="contact-info">
            <h1>Contact Us</h1>
        <p>We'd love to hear from you! Connect with us through any of the following channels:</p>
            <a href="https://www.instagram.com" target="_blank">
                <span class="icon">📸</span> Follow us on Instagram
            </a>
            <a href="https://www.facebook.com" target="_blank">
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
