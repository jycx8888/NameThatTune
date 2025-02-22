<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$dbusername = "root"; 
$password = ""; 
$dbname = "namethattune"; 

$conn = new mysqli($servername, $dbusername, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

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

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $profile_picture_path = $row['ProfilePicture'];
} else {
    $profile_picture_path = 'Icon/account.png';
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>NameThatTune</title>
    <link rel="icon" href="icon/logo.jpg" type="image/png">
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        body {
            font-family: 'Lalezar', system-ui;
        }
        h1 {
            text-align: center;
            margin-top: 12px;
            font-size: clamp(24px, 3vw, 32px);
        }
        .container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 24px;
            margin: 24px auto;
            width: 90%;
            max-width: 800px;
        }
        table {
            border-collapse: collapse;
            width: 80%;
            margin: 20px auto;
            background-color: white;
        }
        th, td {
            font-family: 'Lalezar', system-ui;
            font-weight: 700;
            font-size: clamp(14px, 2vw, 16px);
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #584cba;
            color: white;
        }
        canvas {
            display: block;
            margin: 20px auto;
            background-color: white; 
            padding: 20px; 
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px; 
            height: 500px; 
        }
        .back-button {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(16px, 2vw, 18px);
            font-weight: 700;
            display: block;
            width: 35%;
            min-width: 200px;
            margin: 20px auto;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 4px;
        }
        .back-button:hover {
            background-color: #45a049;
        }
        .search-bar {
            text-align: center;
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px; 
        }
        
        .search-bar input[type="text"] {
            font-family: 'Lalezar', system-ui;
            font-weight: 500;
            width: 60%; 
            padding: 10px;
            font-size: clamp(14px, 2vw, 16px);
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        
        .search-bar button {
            font-family: 'Lalezar', system-ui;
            font-weight: 700;
            font-size: clamp(14px, 2vw, 16px);
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .search-bar button:hover {
            background-color: #45a049;
        }
        
        .refresh-button {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #f44336;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        
        .refresh-button:hover {
            background-color: #e53935;
        }
            </style>
            <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        </head>
        <body>
  
   <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div class="container">
        <h1>Guess Song Quiz Analytics</h1>
        <div class="search-bar">
            <input type="text" id="searchInput" placeholder="Search by Quiz ID">
            <button onclick="filterTableAndChart()">Search</button>
            <button class="refresh-button" onclick="refreshTableAndChart()">Refresh</button>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Total Attempts</th>
                    <th>Total Correct</th>
                    <th>Total Incorrect</th>
                </tr>
            </thead>
            <tbody id="table-body">
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
        <canvas id="quizChart" width="400" height="200"></canvas>
        <a href="admin_adminDashboard.php" class="back-button">Back to Admin Dashboard</a>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>

    <script>
        function refreshTableAndChart() {
            document.getElementById('searchInput').value = '';
            const tableBody = document.getElementById('table-body');
            const rows = tableBody.getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                rows[i].style.display = '';
            }
        
            quizChart.data.labels = labels;
            quizChart.data.datasets[0].data = totalAttempts;
            quizChart.data.datasets[1].data = totalCorrect;
            quizChart.data.datasets[2].data = totalIncorrect;
            quizChart.update();
        }
    </script>

    <script>
        const labels = <?php echo json_encode(array_column($data, 'QuizID')); ?>;
        const totalAttempts = <?php echo json_encode(array_column($data, 'TotalAttempts')); ?>;
        const totalCorrect = <?php echo json_encode(array_column($data, 'TotalCorrect')); ?>;
        const totalIncorrect = <?php echo json_encode(array_column($data, 'TotalIncorrect')); ?>;

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

        function filterTableAndChart() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const tableBody = document.getElementById('table-body');
            const rows = tableBody.getElementsByTagName('tr');
            const filteredData = [];

            // Filter table rows
            for (let i = 0; i < rows.length; i++) {
                const cells = rows[i].getElementsByTagName('td');
                const quizID = cells[0].textContent.toLowerCase();

                if (quizID.includes(searchInput)) {
                    rows[i].style.display = '';
                    filteredData.push({
                        QuizID: cells[0].textContent,
                        TotalAttempts: parseInt(cells[1].textContent),
                        TotalCorrect: parseInt(cells[2].textContent),
                        TotalIncorrect: parseInt(cells[3].textContent)
                    });
                } else {
                    rows[i].style.display = 'none';
                }
            }

            // Update chart with filtered data
            const filteredLabels = filteredData.map(item => item.QuizID);
            const filteredTotalAttempts = filteredData.map(item => item.TotalAttempts);
            const filteredTotalCorrect = filteredData.map(item => item.TotalCorrect);
            const filteredTotalIncorrect = filteredData.map(item => item.TotalIncorrect);

            quizChart.data.labels = filteredLabels;
            quizChart.data.datasets[0].data = filteredTotalAttempts;
            quizChart.data.datasets[1].data = filteredTotalCorrect;
            quizChart.data.datasets[2].data = filteredTotalIncorrect;
            quizChart.update();
        }
    </script>
</body>
</html>