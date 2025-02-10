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
    $stmt = $conn->prepare("SELECT * FROM user WHERE Username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Compare passwords in a case-sensitive manner
        if ($row['Password'] === $password) {
            // Login successful
            $_SESSION['username'] = $username;
            $login_success = true;
            header("Location: user_mainPage.php");
            exit();
        } else {
            // Login failed
            $login_error = "Invalid username or password.";
        }
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
    <link rel="stylesheet" href="user_header.css">
    <style>
        #login-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 72px); /* Adjust height to account for header */
            width: 100%;
        }

        #login2 {
            width: clamp(20em, 50vw, 30em);
            height: 370px;
            padding: 48px;
            background-color: white;
            border-radius: 25px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #login2 input[type="text"],
        #login2 input[type="password"]{
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
            margin-top: 10px;
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
        <div id="login">
           <p>Login</p>
        </div>
    </div>

    <div id="login-container">
        <div id="login2">
            <img src="Icon/logo.jpg" class="logo-image" alt="logo">
            <h3 style="margin-top: 0px">Admin Login</h3>
            <form onsubmit="return validateForm()" action="admin_login.php" method="post">
                <input type="text" id="Username" name="username" placeholder="Enter your name"><br><br>
                <div class="password-container">
                    <input type="password" id="Password" name="password" placeholder="Enter your password">
                    <img src="Icon/hide.png" class="toggle-password" onclick="togglePasswordVisibility('Password', this)">
                </div><br>
                <div id="button-container">
                    <input type="submit" class="submit-button" value="Login">
                </div>
                <?php
                    if (isset($login_error)) {
                        echo "<h3 style='color:red; display:flex; justify-content: center; margin-top: -70px;'>$login_error</h3>";
                    }
                ?>
                </form>
        </div>
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
            window.location.href = 'admin_adminDashboard.php';
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
    </script>
</body>
</html>