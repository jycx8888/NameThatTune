<?php
session_start();

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

$login_success = false;
$username = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Prepare and bind
    $stmt = $conn->prepare("SELECT * FROM user WHERE Username = ? AND Password = ?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Login successful
        $row = $result->fetch_assoc();
        $_SESSION['username'] = $username;
        $login_success = true;
        header("Location: mainPage_user.php");
        exit();
    } else {
        // Login failed
        $login_error = "Invalid username or password.";
    }

    $stmt->close();
}

$conn->close();
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

        #login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 100px); /* Adjust height to account for header */
            width: 100%;
        }

        #login {
            width: 650px;
            height: 400px;
            background-color: white;
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 20px; /* Add padding to the top */
        }

        #login input[type="text"],
        #login input[type="password"]{
            margin: 0px 0;
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

        #createAccount-container {
            display:flex;
            justify-content: center;
        }

        .createAccount {
            font-size: 12px; /* Smaller font size for terms and conditions */
            font-family: "Roboto", sans-serif;
            font-weight: 100;
            font-style: normal;
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

        h3{
            font-family: "Lalezar", system-ui;
            font-size: 15px;
            font-weight: 1000;
            font-style: normal;
        }


    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="loginOrRegister" onclick="redirectToRegister()">
           <p>Login/Sign Up</p>
        </div>
    </div>

    <div id="login-container">
        <div id="login">
            <img src="Icon/logo.jpg" class="logo-image" alt="logo">
            <form onsubmit="return validateForm()" action="login_user.php" method="post">
                <input type="text" id="Username" name="username" placeholder="Enter your name"><br><br>
                <div class="password-container">
                    <input type="password" id="Password" name="password" placeholder="Enter your password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('Password', this)">
                </div><br>
                <div id="button-container">
                    <input type="submit" class="submit-button" value="Login">
                </div>
                <div id="createAccount-container">
                    <span class="createAccount">No account?  <a href="register_user.php" target="_blank">click here</a></span><br>
                </div> 
                <?php
                    if (isset($login_error)) {
                        echo "<h3 style='color:red; display:flex; justify-content: center; margin-top: -100px;'>$login_error</h3>";
                    }
                ?>
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

            if (username === "") {
                showWarning('Username cannot be empty.');
                return false;
            }

            if (password === "") {
                showWarning('Password cannot be empty.');
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

        <?php if ($login_success): ?>
            alert('Login successfully, welcome <?php echo htmlspecialchars($username); ?>');
            window.location.href = 'mainPage_user.php';
        <?php endif; ?>

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
    
        function redirectToRegister() {
            window.location.href = 'register_user.php';
        }
    </script>
</body>
</html>