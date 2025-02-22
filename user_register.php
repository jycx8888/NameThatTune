<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "namethattune";


$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname,3306);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dateJoined = date("Y-m-d");
    $answerCorrectRate = 0; 
    $profilePicture = 'uploads/avatar.png'; 
 
    
    $result = $conn->query("SELECT UserID FROM user ORDER BY UserID DESC LIMIT 1");
    $row = $result->fetch_assoc();

    $lastUserID = $row ? $row['UserID'] : 'U000';
    $userCount = (int)substr($lastUserID, 1) +1;
    $userID = 'U' . str_pad($userCount, 3, '0', STR_PAD_LEFT);
 
    
    $sql = "INSERT INTO user (UserID, Username, Password, DateJoined, AnswerCorrectRate, ProfilePicture) VALUES ('$userID', '$username', '$password', '$dateJoined', '$answerCorrectRate', '$profilePicture')";
 
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Register successfully!'); window.location.href = 'user_login.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
 
    $conn->close();
    }
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
        #register-container {
            display: flex;
            justify-content: center;
            height: 75%;
            width: 100%;
            margin: 90px 0 180px 0;
        }

        #register {
            width: clamp(20em, 50vw, 30em);
            height: auto;
            padding: 48px;
            background-color: white;
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        #register input[type="text"],
        #register input[type="password"]{
            height: 32px;
            width: clamp(15em, 20vw, 25em);
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding-left: 5px;
            margin: 5px 0;
            border: 3px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 900;
            font-size: 18px;
        }

        .logo-image {
            width: 150px;
            height: 150px;
            border: 3px solid #000;
            border-radius: 25px;
            margin-bottom: 20px;
        }

        #button-container {
            display:flex;
            justify-content: center;
        }

        .password-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .password-container img {
            position: absolute;
            cursor: pointer;
            right: 10px;
            width: 20px;
            height: 16px;
        }

        .submit-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 10vw;
            border-radius: 5px;
            border: none;
            background-color: #320fbd;
            color: white;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: 18px;
            align-self: center;
        }

        .submit-button:hover {
            background-color: #1a0573;
        }

        .terms {
            font-size: 12px;
            font-family: "Roboto", sans-serif;
            font-weight: 100;
            font-style: normal;
        }

        h1{
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-style: normal;
        }

        .overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: none;
        justify-content: center;
        align-items: center;
        }
        
        .overlay.show {
            display: flex;
        }
        
        .popup {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        
        .popup .close-btn {
            position: absolute;
            top: 10px;
            right: 10px;
            cursor: pointer;
            font-size: 20px;
        }
    
        #warningPopup {
            font-family: "Lalezar", system-ui;
            font-size: 20px;
            font-weight: 1000;
            font-style: normal;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1><a href="user_mainPage.php">NameThatTune</a></h1>
        <link rel="icon" href="icon/logo.jpg" type="image/png">
        <div id="login" onclick="redirectToLogin()">
            <p>Login/Sign Up</p>
        </div>
    </div>

    <div id="register-container">
        <div id="register">
            <img src="Icon/logo.jpg" class="logo-image" alt="logo">
            <form onsubmit="return validateForm()" action="user_register.php" method="post">
                <input type="text" id="Username" name="username" placeholder="Username">
                <div class="password-container">
                    <input type="password" id="Password" name="password" placeholder="Password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('Password', this)">
                </div>
                <div class="password-container">
                    <input type="password" id="ConfirmPassword" name="confirm password" placeholder="Confirm Password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('ConfirmPassword', this)">
                </div>
                <input type="checkbox" id="Terms" name="T&C" value="T&C">
                <span class="terms">I have read and accept the <a href="user_terms_and_conditions.php" target="_blank">Terms and Conditions</a></span><br>           
                <div id="button-container">
                    <input type="submit" class="submit-button" value="Register">
                </div>
            </form>
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

    <div id="warningPopup" class="overlay">
        <div class="popup">
            <span class="close-btn" onclick="closeWarningPopup()">&times;</span>
            <p id="warningMessage"></p>
        </div>
    </div>

    <script>
        function validateForm() {
            const username = document.getElementById('Username').value;
            const password = document.getElementById('Password').value;
            const confirmPassword = document.getElementById('ConfirmPassword').value;
            const terms = document.getElementById('Terms').checked;
            const passwordRegex = /^(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,14}$/;

            if (username === "") {
                showWarning('Username cannot be empty.');
                return false;
            }

            if (password === "") {
                showWarning('Password cannot be empty.');
                return false;
            }

            if (confirmPassword === "") {
                showWarning('Confirm Password cannot be empty.');
                return false;
            }

            if (password !== confirmPassword) {
                showWarning('Passwords do not match.');
                return false;
            }

            if (!passwordRegex.test(password)) {
                showWarning('Password must be 8-14 characters long, include at least one capital letter, one number, and one special character.');
                return false;
            }

            if (!terms) {
                alert('You must accept the Terms and Conditions.');
                return false;
            }

            return true;
        }

        function togglePasswordVisibility(fieldId, iconElement) {
            const passwordField = document.getElementById(fieldId);
            const icon = document.getElementById(iconElement);
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                iconElement.src = 'Icon/show.png'; 
            } else {
                passwordField.type = 'password';
                iconElement.src = 'Icon/hide.png'; 
            }
        }

        function redirectToLogin() {
            window.location.href = "user_login.php";
        }

        function showWarning(message) {
            const warningPopup = document.getElementById('warningPopup');
            const warningMessage = document.getElementById('warningMessage');
            warningMessage.textContent = message;
            warningPopup.classList.add('show');
        }
        
        function closeWarningPopup() {
            const warningPopup = document.getElementById('warningPopup');
            warningPopup.classList.remove('show');
        }
    </script>
</body>
</html>

