<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

// Step 1: Database Connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = ""; // Replace with your MySQL password
$dbname = "namethattune"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Fetch user data from the database
$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
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

// Step 2: Fetch Data for Analytics
$sql = "SELECT QuizID, COUNT(*) AS TotalAttempts, 
        SUM(Result) AS TotalCorrect, 
        COUNT(*) - SUM(Result) AS TotalIncorrect 
        FROM record GROUP BY QuizID";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the database connection
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Guess Song Quiz Analytics</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        h1 {
            text-align: center;
            margin-top: 20px;
        }
        .container {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 50px;
        margin: 20px auto -30% auto;
        width: 90%;
        max-width: 800px;
    }
        table {
        border-collapse: collapse;
        width: 80%;
        margin: 20px auto -200px auto; /* Reduced bottom margin */
    }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        canvas {
            display: block;
            margin: 1px auto;
            width: 70%;
        }
        .back-button {
            display: block;
            width: 200px;
            margin: 70px auto;
            padding: 10px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .back-button:hover {
            background-color: #45a049;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

   <!-- Header Section -->
   <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div class="container">
    <h1>Guess Song Quiz Analytics</h1>
    <table>
        <thead>
            <tr>
                <th>Quiz ID</th>
                <th>Total Attempts</th>
                <th>Total Correct</th>
                <th>Total Incorrect</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($data as $row): ?>
                <tr>
                    <td><?php echo $row['QuizID']; ?></td>
                    <td><?php echo $row['TotalAttempts']; ?></td>
                    <td><?php echo $row['TotalCorrect']; ?></td>
                    <td><?php echo $row['TotalIncorrect']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <canvas id="myChart"></canvas>
    <!-- Step 4: Add Chart -->
    <canvas id="quizChart" width="400" height="200"></canvas>
    <a href="admin_adminDashboard.php" class="back-button">Back to Admin Dashboard</a>
</div>

<script>
    // Prepare data for the chart
    const labels = <?php echo json_encode(array_column($data, 'QuizID')); ?>;
    const totalAttempts = <?php echo json_encode(array_column($data, 'TotalAttempts')); ?>;
    const totalCorrect = <?php echo json_encode(array_column($data, 'TotalCorrect')); ?>;
    const totalIncorrect = <?php echo json_encode(array_column($data, 'TotalIncorrect')); ?>;

    // Configure the chart
    const data = {
        labels: labels,
        datasets: [
            {
                label: 'Total Attempts',
                data: totalAttempts,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            },
            {
                label: 'Total Correct',
                data: totalCorrect,
                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            },
            {
                label: 'Total Incorrect',
                data: totalIncorrect,
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                borderColor: 'rgba(255, 99, 132, 1)',
                borderWidth: 1
            }
        ]
    };

    const config = {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Quiz Performance Overview'
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    };

    // Render the chart
    const quizChart = new Chart(
        document.getElementById('quizChart'),
        config
    );
</script>
</body>
</html>