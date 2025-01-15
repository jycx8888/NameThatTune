<?php
// Database connection
$servername = "127.0.0.1";
$username = "root"; // Adjust this if your username is different
$password = ""; // Adjust this if you have a password
$dbname = "namethattune";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$search_quiz_id = '';
$results = [];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_quiz_id = $_POST['quiz_id'];
    $sql = "SELECT * FROM record WHERE QuizID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_quiz_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
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
    <title>Analytics Page</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Analytics Page</h1>
    <form method="POST" action="analyticPage.php">
        <label for="quiz_id">Search by Quiz ID:</label>
        <input type="text" id="quiz_id" name="quiz_id" value="<?php echo htmlspecialchars($search_quiz_id); ?>" required>
        <button type="submit">Search</button>
    </form>

    <?php if (!empty($results)): ?>
        <h2>Results for Quiz ID: <?php echo htmlspecialchars($search_quiz_id); ?></h2>
        <table>
            <thead>
                <tr>
                    <th>RecordID</th>
                    <th>Result</th>
                    <th>Time</th>
                    <th>UserID</th>
                    <th>QuizID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($results as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['RecordID']); ?></td>
                        <td><?php echo htmlspecialchars($row['Result']); ?></td>
                        <td><?php echo htmlspecialchars($row['Time']); ?></td>
                        <td><?php echo htmlspecialchars($row['UserID']); ?></td>
                        <td><?php echo htmlspecialchars($row['QuizID']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php elseif ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
        <p>No results found for Quiz ID: <?php echo htmlspecialchars($search_quiz_id); ?></p>
    <?php endif; ?>
</body>
</html>
