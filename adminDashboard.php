<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Admin Dashboard</title>
    <style>
        html, body {
            margin: 0;
            padding: 0;
            width: 100%;
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: rgb(104, 99, 174);
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }

        #header {
            background-color: gray;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
            width: 100%;
        }

        #header h1 {
            font-family: "Lalezar", system-ui;
            font-size: 36px;
            font-weight: 1000;
            padding-bottom: 4px;
            margin-left: 60px;
        }

        #login {
            width: 180px;
            height: 48px;
            background-color: white;
            border-radius: 10px;
            display: flex;
            justify-content: left;
            align-items: center;
            margin-right: 60px;
        }

        #login img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-left: 12px;
        }

        #login p {
            font-family: "Lalezar", system-ui;
            font-size: 20px;
            font-weight: 1000;
            margin-left: 12px;
            padding-bottom: 1px;
        }

        #footer {
            height: 144px;
            width: 100%;
            background-color: black;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: "Lalezar", system-ui;
            font-size: small;
            position: relative;
            bottom: 0;
        }

        #footer ul {
            display: flex;
            justify-content: center;
            padding: 0;
        }

        #footer ul li {
            list-style-type: none;
            font-size: 16px;
            padding: 16px;
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

        main {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, auto);
            gap: 20px;
            place-items: center; /* Centers items horizontally and vertically */
            min-height: 50vh; /* Ensures proper spacing */
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
            <img src="avatar.png" alt="avatar">
            <p>Username</p>
        </div>
    </div>

    <main>
        <div class="option-box" onclick="window.location.href='quizManagementPage.php'">
            Quiz 
            <br>Management</br>
        </div>
        <div class="option-box" onclick="window.location.href='userManagementPage.php'">
            User
            <br>Management</br>
        </div>
        <div class="option-box" onclick="window.location.href='analyticPage.php'">
            Analytics
        </div>
    </main>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="facebookLogo.png" alt="facebook" id="facebook">
                <img src="instagramLogo.png" alt="instagram" id="instagram">
            </li>
        </ul>
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>
</body>
</html>
