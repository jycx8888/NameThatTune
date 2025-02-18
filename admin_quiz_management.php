<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: admin_login.php");
    exit();
}

$servername = "localhost";
$dbusername = "root"; // Database username
$dbpassword = ""; // Database password
$dbname = "namethattune";

// Create connection
$connection = new mysqli($servername, $dbusername, $dbpassword, $dbname);

// Check connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

$username = $_SESSION['username'];

// Fetch user data from the database
$stmt = $connection->prepare("SELECT ProfilePicture FROM admin WHERE Username = ?");
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

// Function to check available songs
function checkAvailableSongs($connection) {
    $query = "SELECT COUNT(*) AS available_songs FROM song WHERE QuestionID IS NULL";
    $result = mysqli_query($connection, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        return $row['available_songs'] >= 5;
    }
    return false;
}

// Query to fetch quiz data
$query = "SELECT QuizID AS quiz_id, GenreID AS genre_id, CreatedTime AS created_time FROM quiz";
$results = mysqli_query($connection, $query);

if (!$results) {
    die("Query failed: " . mysqli_error($connection));
}

// Check if delete action is requested
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    $quiz_id = $_GET['id'];

    $stmt = $connection->prepare("DELETE FROM song WHERE QuestionID IN (SELECT QuestionID FROM question WHERE QuizID = ?)");
    $stmt->bind_param("s", $quiz_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("DELETE FROM `option` WHERE QuestionID IN (SELECT QuestionID FROM question WHERE QuizID = ?)");
    $stmt->bind_param("s", $quiz_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("DELETE FROM question WHERE QuizID = ?");
    $stmt->bind_param("s", $quiz_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $connection->prepare("DELETE FROM quiz WHERE QuizID = ?");
    $stmt->bind_param("s", $quiz_id);

    if ($stmt->execute()) {
        echo "<script>alert('Quiz deleted successfully!'); window.location.href='admin_quiz_management.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        
        #content {
            display: flex;
            justify-content: center;
            height: calc(100vh - 72px);
        }

        #edit-quiz-container {
            display: flex;
            flex-direction: column;
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            background-color: #f4f4f4;
            width: 80%;
            max-width: 1100px;
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            align-self: center;
        }

        .section-title {
            font-size: clamp(24px, 1.5vw, 32px);
            font-weight: 700;
            margin-bottom: 1rem;
            text-align: left;
        }

        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            justify-content: left;
            margin-bottom: 1rem;
        }

        .search-bar label {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 20px);
            font-weight: 700;
        }

        .search-bar select, .search-bar input {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(12px, 1.5vw, 16px);
            font-weight: 700;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(50% - 0.5rem);
            min-width: 120px;
        }

        .search-bar button {
            padding: 0.5rem 1rem;
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            background-color: #584cba;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .search-bar button:hover {
            background-color: #17066e;
        }

        table {

            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-family: 'Lalezar', system-ui;
            font-weight: 700;
        }

        th, td {
            font-size: (14px, 1.5vw, 16px);
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            font-weight: 700;
            background-color: #584cba;
            color: white;
        }

        .actions a {
            font-size: clamp(14px, 1.5vw, 16px);
        }

        .actions #edit-link {
            color: #584cba;
            text-decoration: none;
        }

        .actions #edit-link:hover {
            color: #17066e;
            text-decoration: underline;
        }

        .actions #delete-link {
            color: red;
            text-decoration: none;
        }

        .actions #delete-link:hover {
            color: darkred;
            text-decoration: underline;
        }

        .back-button {
            font-family: 'Lalezar', system-ui;
            font-size: clamp(14px, 1.5vw, 16px);
            font-weight: 700;
            display: block;
            width: fit-content;
            padding: 0.6rem 1rem;
            background-color: #584cba;
            color: white;
            text-align: center;
            border-style: none;
            border-radius: 5px;
            margin: 1rem auto 0;
        }
        .back-button:hover{
            background-color: #17066e;
        }

    </style>
    <script>
        function addSong() {
    console.log("Add Song button clicked!"); // Debugging
    window.location.href = 'admin_quiz_management_2.php';
}
    </script>
</head>
<body>
    <div id="header">
        <h1><a href="admin_adminDashboard.php">NameThatTune</a></h1>
        <div id="login" onclick="">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>"> <!-- Display the profile picture -->
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <?php include 'admin_hamburger_menu.php'; ?>

    <!-- Content -->
    <div id="content">
        <div id="edit-quiz-container">
        <div class="section-title">Quiz Management</div>

<!-- Search Filter Section -->
<div class="search-bar">
<label for="filter">Search by:</label>
<select id="filter">
    <option value="id">Quiz ID</option>
    <option value="genre">Genre ID</option>
    <option value="time">Created Time</option>
</select>
<input type="text" id="search" placeholder="Search....">
<button onclick="performSearch()">Search</button>
<button id="addSongBtn" onclick="moveToAddSong()">Add New Quiz</button>

</div>
<!-- Table Section -->
 
<table>
<thead>
<tr>
    <th>Quiz ID</th>
    <th>Genre ID</th>
    <th>Created Time</th>
    <th>Action</th>
</tr>

<script>
document.addEventListener("DOMContentLoaded", function() {
var button = document.getElementById("addSong");
if (button) {
button.addEventListener("click", function() {
    console.log("Redirecting to admin_quiz_management_2.php");
    window.location.href = 'admin_quiz_management_2.php';
});
} else {
console.log("Button not found.");
}
});
</script>

</thead>
<tbody id="quizTable">
<?php if (mysqli_num_rows($results) > 0): ?>
    <?php while ($row = mysqli_fetch_assoc($results)): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['quiz_id']); ?></td>
            <td><?php echo htmlspecialchars($row['genre_id']); ?></td>
            <td><?php echo htmlspecialchars($row['created_time']); ?></td>
            <td class="actions">
                <a id="edit-link" href="admin_quiz_management_2.php?quiz_id=<?php echo urlencode($row['quiz_id']); ?>">Edit</a> |
                <a id="delete-link" href="admin_quiz_management.php?action=delete&id=<?php echo urlencode($row['quiz_id']); ?>" onclick="return confirm('Are you sure you want to delete this quiz?');">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
    <tr>
        <td colspan="4">No quizzes found.</td>
    </tr>
<?php endif; ?>
</tbody>
</table>

<!-- Back Button -->
<!-- Back Button -->
<button onclick="back()" class="back-button">Back to Dashboard</button>

        </div>


    </div>

    <!-- Script for Search Functionality -->
    <script>
        function performSearch() {
            const searchInput = document.getElementById("search");
            if (!searchInput) {
                console.error("Error: Search input not found!");
                return;
            }
            console.log("Searching for: " + searchInput.value);
        
            const filter = document.getElementById("filter").value;
            const searchTerm = searchInput.value.toLowerCase();
            const table = document.getElementById("quizTable");
            if (!table) {
                console.error("Error: Quiz table not found!");
                return;
            }
        
            const rows = table.getElementsByTagName("tr");
            let found = false;
        
            for (let row of rows) {
                const cells = row.getElementsByTagName("td");
                if (cells.length === 0) continue;
        
                let shouldDisplay = false;
                if (filter === "id" && cells[0] && cells[0].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                } else if (filter === "genre" && cells[1] && cells[1].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                } else if (filter === "time" && cells[2] && cells[2].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                }
        
                row.style.display = shouldDisplay ? "" : "none";
                if (shouldDisplay) found = true;
            }
        
            let noResultsRow = document.getElementById("noResultsRow");
            if (!found) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement("tr");
                    noResultsRow.id = "noResultsRow";
                    noResultsRow.innerHTML = `<td colspan="4" style="text-align:center;">No matching quizzes found.</td>`;
                    table.appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        }

        function moveToAddSong() {
            if (!<?php echo json_encode(checkAvailableSongs($connection)); ?>) {
                alert('There must be at least 5 songs not linked to any question to add a new quiz.');
                return;
            }
            window.location.href = 'admin_addQuizNew.php';
        }

        function back(){
            window.location.href = 'admin_adminDashboard.php';
        }
        
    </script>
    
     <?php 
    // Close database connection at the very end of the page
    mysqli_close($connection);
    ?>
</body>
</html>