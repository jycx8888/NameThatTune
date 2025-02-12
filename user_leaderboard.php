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

    // Redirect to avoid form resubmission
    header("Location: user_mainPage.php");
    exit();
}
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            font-family: "Lalezar", system-ui;
            font-style: normal;
        }

        #leaderboard {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            background-color: white;
            width: fit-content;
            height: fit-content;
            margin-top: 48px;
            padding: clamp(28px, 8vw, 40px);
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        #leaderboard h1 {
            font-size: clamp(32px, 5vw, 40px);
            font-weight: 1000;
            font-style: normal;
            margin: 0 0 24px 0;
        }

        .record {
            display: flex;
            align-items: center;
            height: clamp(48px, 12vw, 68px);
            width: clamp(20em, 50vw, 30em);
            min-width: 240px;
            margin-top: 12px;
            background-color: white;
            border-radius: 10px;
            border-style: solid;
        }

        .record h3 {
            text-align: left;
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            font-style: normal;
        }

        .circle {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 45px;
            height: 45px;
            background-color: black;
            border-radius: 50%;
            margin-left: 8px;
        }

        .circle h2 {
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            color: white;
        }

        .username {
            text-align: center;
            flex: 1;
            margin-left: 8px;
        }
        
        .result {
            justify-self: end;
            font-size: clamp(20px, 4vw, 26px);
            font-weight: 1000;
            margin-right: 8px;
        }

        .button {
            margin: 24px 0 48px 0;
            padding: 16px 24px;
            font-size: clamp(18px, 2vw, 22px);
            font-weight: 1000;
            font-style: normal;
            background-color: #584cba;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .button:hover {
            background-color: #17066e;
        }

        .button:active {
            transform: scale(0.98);
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
        <div id="leaderboard">
            <h1>Leaderboard</h1>
            <?php
            $sql1 = "SELECT UserID, Result, TimeUsed FROM record ORDER BY Result DESC, TimeUsed ASC";
            $result1 = mysqli_query($conn, $sql1);
        
            if (mysqli_num_rows($result1) > 0) {
                $rank = 1;
                while ($row1 = mysqli_fetch_assoc($result1)) {
                    $userID = $row1['UserID'];
                    $sql2 = "SELECT Username FROM user WHERE UserID = '$userID'";
                    $result2 = mysqli_query($conn, $sql2);
                    $row2 = mysqli_fetch_assoc($result2);
                    $username = $row2['Username'];
                
                    if ($rank <= 5) {
                        echo "<div class='record'>";
                        echo "<div class='circle'>";
                        echo "<h2>$rank</h2>";
                        echo "</div>";
                        if ($username == $_SESSION['username']) {
                            echo "<h3 class='username'>$username(You)</h3>";
                            $user_rank = $rank;
                        } else {
                            echo "<h3 class='username'>$username</h3>";
                        }
                        echo "<div class='result'>" . $row1['Result'] . "/5(" . $row1['TimeUsed'] . "s)</div>";
                        echo "</div>";
                    }

                    if ($username == $_SESSION['username']) {
                        $user_result = $row1['Result'];
                        $user_timeused = $row1['TimeUsed'];
                        $user_rank = $rank;
                    }

                    $rank++;
                }
            }

            echo "<div class='record'>";
            echo "<div class='circle'>";
            echo "<h2>$user_rank</h2>";
            echo "</div>";
            echo "<h3 class='username'>" . $_SESSION['username'] . "(You)</h3>";
            echo "<div class='result'>" . $user_result . "/5(" . $user_timeused . "s)</div>";
            echo "</div>";
            ?>
        </div>

        <input type="button" value="Back to Main Page" onclick="location.href='user_mainPage.php'" class = "button">
    </div>


    
</body>
</html>

<?php
$conn->close();
?>
