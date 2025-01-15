<?php
$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname,3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $dateJoined = date("Y-m-d");
    $answerCorrectRate = 0; // Default value
    $profilePicture = 'Icon/account.png'; // Path to the default profile picture
 
    // Generate UserID
    $result = $conn->query("SELECT COUNT(*) AS count FROM user");
    $row = $result->fetch_assoc();
    $userCount = $row['count'] + 1;
    $userID = 'U' . str_pad($userCount, 3, '0', STR_PAD_LEFT);
 
    // Insert user into the database
    $sql = "INSERT INTO user (UserID, Username, Password, DateJoined, AnswerCorrectRate, ProfilePicture) VALUES ('$userID', '$username', '$password', '$dateJoined', '$answerCorrectRate', '$profilePicture')";
 
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Register successfully!'); window.location.href = 'login_user.php';</script>";
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
    <title>Document</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            background-color: rgb(104, 99, 174);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        
        #header{
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }

        #header h1 {
            font-family: "Lalezar", system-ui;
            font-size: 36px;
            font-weight: 1000;
            font-style: normal;
            padding-bottom: 4px;
            margin-left: 60px;
        } 

        #loginOrRegister{
            width: 180px;
            height: 48px;
            background-color:white;
            border-radius: 10px;
            display: flex;
            justify-content: left;
            align-items: center;
            margin-right: 60px;
        }

        #loginOrRegister p {
            font-family: "Lalezar", system-ui;
            font-size: 20px;
            font-weight: 1000;
            font-style: normal;
            margin-left: 20px;
            padding-bottom: 1px;
        }

        #register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px); /* Adjust height to account for header */
            width: 100%;
        }

        #register {
            width: 650px;
            height: 450px;
            background-color: white;
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px; /* Add padding to the top */
        }
        
        #register input[type="text"],
        #register input[type="password"]{
            margin: -5px 0;
            padding: 10px 150px;
            padding-left: 5px;
            border: 3px solid #ccc;
            border-radius: 5px;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
        }


        .logo-image {
            width: 150px;
            height: 150px;
            border: 3px solid #000; /* Black border */
            border-radius: 25px;
            margin-bottom: 20px; /* Space between the logo and the form */
        }

        #button-container {
            display:flex;
            justify-content: center;
        }

        
        .password-container {
            display: flex;
            align-items: center;
        }

        .password-container input {
            margin-right: 5px;
            margin-left: 5px;
        }

        .password-container img {
            cursor: pointer;
            width: 20px;
            height: 20px;
            object-fit: contain;
            margin-left: -30px;
        }

        .submit-button {
            margin-top: 20px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            background-color: #320fbd;
            color: white;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-weight: 1000; /* Change the font family */
            font-size: 18px;
            align-self: center;
        }

        .submit-button:hover {
            background-color: #1a0573;
        }

        .terms {
            font-size: 12px; /* Smaller font size for terms and conditions */
            font-family: "Roboto", sans-serif;
            font-weight: 100;
            font-style: normal;
        }

        #footer {
            clear: both;
            height: 144px;
            width: 100%;
            background-color: black;
            color: white;
            display: inline-block;
            position: relative;
            bottom: 0;
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
            font-size: small;
            justify-content: space-between;
        }

        #footer ul {
            display: flex;
            justify-content: center;
            width: 100%;
            padding: 0;
        }

        #footer ul li {
            display: inline;
            list-style-type: none;
            font-size: 16px;

            padding: 16px 16px 4px 16px;
            text-align: center;
        }

        #footer #instagram {
            width: 30px;
            height: 30px;
            vertical-align: middle;
        }

        #footer #facebook {
            width: 26px;
            height: 26px;
            vertical-align: middle;
            margin-left: 4px;
        }

        #footer #copy {
            text-align: center;
            font-size: 16px;
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
        <h1>NameThatTune</h1>
        <div id="loginOrRegister" onclick="redirectToLogin()">
            <p>Login/Sign Up</p>
        </div>
    </div>

    <div id="register-container">
        <div id="register">
            <img src="Icon/logo.jpg" class="logo-image" alt="logo">
            <form onsubmit="return validateForm()" action="register_user.php" method="post">
                <input type="text" id="Username" name="username" placeholder="Username"><br><br>
                <div class="password-container">
                    <input type="password" id="Password" name="password" placeholder="Password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('Password', this)">
                </div><br>
                <div class="password-container">
                    <input type="password" id="ConfirmPassword" name="confirm password" placeholder="Confirm Password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('ConfirmPassword', this)">
                </div><br>
                <input type="checkbox" id="Terms" name="T&C" value="T&C">
                <span class="terms">I have read and accept the <a href="terms_and_conditions.html" target="_blank">Terms and Conditions</a></span><br>           
                <div id="button-container">
                    <input type="submit" class="submit-button" value="Register">
                </div>
            </form>
        </div>
    </div>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="Icon/facebook.png" alt="facebook" id="facebook">&nbsp;
                <img src="Icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

    <!-- Warning Popup -->
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
                iconElement.src = 'Icon/show.png'; // Change to the open eye icon
            } else {
                passwordField.type = 'password';
                iconElement.src = 'Icon/hide.png'; // Change to the closed eye icon
            }
        }

        function redirectToLogin() {
            window.location.href = "login_user.php";
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

