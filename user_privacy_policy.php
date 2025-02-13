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
            font-size: clamp(1em, 3vw, 1.3em);
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

        #terms ul {
            list-style-type: square;
            margin: 0 24px;
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

    <?php include 'user_hamburger_menu.php'; ?>

    <div id="content">
        <div id="terms">
            <h2>Privacy Policy</h2>
            <ol class="ol1">
                <li>Information We Collect
                    <ol class="ol2">
                        <li>Information You Provide
                            <ul>
                                <li>Account Information: When you register, we may collect your name, email address, username, and other details.</li>
                                <li>Game Activity: Information about your participation, scores, and achievements.</li>
                            </ul>
                        </li>
                        <li>Automatically Collected Information
                            <ul>
                                <li>Device Information: Details about the device you use, including operating system, device type, and IP address.</li>
                                <li>Usage Data: Information about how you use the app, such as session length, features accessed, and errors encountered.</li>
                            </ul>
                        </li>
                        <li>Third-Party Information
                            <ul>
                                <li>If you link your account to a third-party platform (e.g., Facebook, Google), we may collect basic profile information from them.</li>
                            </ul>
                        </li>
                    </ol>
                </li>
                <li>How We Use Your Information
                    <ul>
                        We use your data to:
                        <li>Provide and improve the game experience.</li>
                        <li>Track scores, achievements, and leaderboard standings.</li>
                        <li>Communicate with you about updates, promotions, and support inquiries.</li>
                        <li>Ensure compliance with our Terms and Conditions.</li>
                    </ul>
                </li>
                <li>Sharing Your Information
                    <ol>
                        <li>With Third Parties
                            <ul>
                                We may share your data with:
                                <li>Service Providers: For hosting, analytics, and customer support.</li>
                                <li>Advertising Partners: To show personalized ads within the app.</li>
                            </ul>
                        </li>
                        <li>Legal Requirements
                            <ul>
                                <li>We may disclose your information if required by law or in response to legal processes.</li>
                            </ul>
                        </li>
                        <li>Mergers or Acquisitions
                            <ul>
                                <li>If the app undergoes a merger, acquisition, or sale, your information may be transferred as part of the transaction.</li>
                            </ul>
                        </li>
                    </ol>
                </li>
                <li>Data Security
                    <ul>
                        <li>We implement appropriate technical and organizational measures to protect your data. However, no system is entirely secure, and we cannot guarantee absolute security.</li>
                    </ul>
                </li>
                <li>Retention of Data
                    <ul>
                        <li>We retain your information only as long as necessary for the purposes outlined in this policy or as required by law.</li>
                    </ul>
                </li>
                <li>Your Rights
                    <ul>
                        Depending on your location, you may have the right to:
                        <li>Access, correct, or delete your personal information.</li>
                        <li>Opt-out of data collection for targeted advertising.</li>
                        <li>Withdraw consent for data processing.</li>
                        <li>To exercise your rights, contact us at [Insert Contact Information].</li>
                    </ul>
                </li>
                <li>Children’s Privacy
                    <ul>
                        <li>Our app is not intended for children under 12.</li>
                        <li>We do not knowingly collect data from minors without parental consent.</li>
                    </ul>
                </li>
                <li>International Users
                    <ul>
                        <li>If you access our website from outside Malaysia, note that your data may be transferred and processed in Malaysia or other jurisdictions.</li>
                    </ul>
                </li>
                <li>Changes to This Privacy Policy
                    <ul>
                        <li>We may update this policy periodically. We will notify users of significant changes through the app or email. Continued use of the website signifies acceptance of the revised policy.</li>
                    </ul>
                </li>
                <li>Contact Us
                    <ul>
                        <li>If you have questions or concerns about this Privacy Policy, contact us at: 019-999 9999</li>
                        <li>Email: namethattune@gmail.com</li>
                    </ul>
                </li>

                <p>By using NameThatTune, you acknowledge that you have read and understood this Privacy Policy.</p>
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

</html>