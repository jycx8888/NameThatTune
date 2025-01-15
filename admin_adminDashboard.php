<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Admin Dashboard</title>
    <link rel="stylesheet" href="user_header_footer.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        main {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, auto);
            gap: 20px;
            place-items: center; /* Centers items horizontally and vertically */
            min-height: 92vh; /* Ensures proper spacing */
        }

        .option-box {
            height: 150px;
            width: 150px;
            background-color: brown;
            border-radius: 15px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
            font-family: Arial, sans-serif;
            font-size: 18px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .option-box:hover {
            background-color: #555;
        }

        h1 {
            font-family: "Lalezar", system-ui;
            font-weight: 1000;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="Icon/account.png" alt="avatar">
            <p>Username</p>
        </div>
    </div>

    <main>
        <div class="option-box" onclick="window.location.href='admin_quiz_management.php'">
            Quiz 
            <br>Management</br>
        </div>
        <div class="option-box" onclick="window.location.href='admin_userManagementPage.php'">
            User
            <br>Management</br>
        </div>
        <div class="option-box" onclick="window.location.href='admin_analyticPage.php'">
            Analytics
        </div>
    </main>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="Icon/facebook.png" alt="facebook" id="facebook">
                <img src="Icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>
</body>
</html>
