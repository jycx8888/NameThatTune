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
        }

        body {
            background-color: rgb(104, 99, 174);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        #header {
            background-color: gray;
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
            padding: 0 60px;
        }

        #header h1 {
            font-family: "Lalezar", system-ui;
            font-size: 36px;
            font-weight: 1000;
            font-style: normal;
            padding-bottom: 4px;
            margin: 0;
        }

        #login {
            width: 180px;
            height: 48px;
            background-color: white;
            border-radius: 10px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        #login img {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            margin-right: 12px;
        }

        #login p {
            font-family: "Lalezar", system-ui;
            font-size: 20px;
            font-weight: 1000;
            font-style: normal;
            margin: 0;
        }

        @media (max-width: 768px) {
            #header {
                flex-direction: column;
                height: auto;
                padding: 10px;
            }

            #header h1 {
                font-size: 28px;
                margin-bottom: 10px;
            }

            #login {
                width: 180px;
                height: auto;
                padding: 10px;
                margin-top: 10px;
            }

            #login img {
                margin-right: 8px;
            }

            #login p {
                font-size: 18px;
            }
        }

        @media (max-width: 480px) {
            #header h1 {
                font-size: 24px;
            }

            #login p {
                font-size: 16px;
            }
        }

        #content {
            height: 785px;
            display: flex;
            justify-content: center;
        }

        #footer {
            clear: both;
            height: 144px;
            width: 100%;
            background-color: black;
            color: white;
            display:inline-block;
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


    </div>
    
    <div id="footer">
        <ul class="nav">
            <li><a href="user_about_us.html">About Us</a></li>
            <li><a href="user_terms_and_conditions.php">Terms and Conditions</a></li>
            <li><a href="user_privacy_policy.html">Privacy Policy</a></li>
            <li><a href="user_contact_us.php"></a>Contact Us
                <img src="\RWDD Assignment\images\facebook.png" alt="facebook.png" id="facebook">&nbsp;
                <img src="\RWDD Assignment\images\instagram.png" alt="instagram.png" id="instagram">
            </li>
        </ul>
        
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>
</body>
</html>