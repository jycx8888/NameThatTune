<?php
// Database connection
    $server = 'localhost';
    $user = 'root';
    $password = '';
    $database = 'namethattune';

     $connection = mysqli_connect($server, $user, $password, $database);

    if (!$connection) {
         die("Connection failed: " . mysqli_connect_error());
    }echo"Connected successfully";
     // Close connection
     mysqli_close($connection);

    // Query to fetch quiz data
    $query = "SELECT quiz_id, name, num_questions, category FROM quizzes";
    $results = mysqli_query($connection, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="user_header_footer.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        #content {
            background-color: #f4f4f4;
            width: 80%;
            max-width: 600px;
            margin: 1rem auto;
            padding: 1rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .section-title {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 1rem;
            text-align: center;
        }

        .search-bar {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .search-bar select, .search-bar input {
            padding: 0.5rem;
            font-size: 1rem;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: calc(50% - 0.5rem);
            min-width: 120px;
        }

        .search-bar button {
            padding: 0.5rem 1rem;
            font-size: 1rem;
            background-color: rgb(104, 99, 174);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            font-size: 0.9rem;
        }

        th, td {
            padding: 0.5rem;
            text-align: center;
            border: 1px solid #ddd;
        }

        th {
            background-color: rgb(104, 99, 174);
            color: white;
        }

        .actions a {
            font-size: 0.85rem;
            color: #ACD7EC;
            text-decoration: none;
        }

        .actions a:hover {
            text-decoration: underline;
            color: blue;
        }

        .back-button {
            display: block;
            width: fit-content;
            padding: 0.6rem 1rem;
            background-color: blueviolet;
            color: white;
            text-align: center;
            border-radius: 5px;
            margin: 1rem auto 0;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="account.png" alt="avatar">
            <p>Username</p>
        </div>
    </div>

    <!-- Content -->
    <div id="content">
        <div class="section-title">Edit Quiz</div>

        <!-- Search Filter Section -->
        <div class="search-bar">
            <label for="filter">Search by:</label>
            <select id="filter">
                <option value="name">Name</option>
                <option value="id">Quiz ID</option>
                <option value="category">Category</option>
            </select>
            <input type="text" id="search" placeholder="Search....">
            <button onclick="performSearch()">Search</button>
            <button onclick="addSong()" style="background-color: grey; color: white;">Add Song</button>
        </div>

        <!-- Table Section -->
        <table>
            <thead>
                <tr>
                    <th>Quiz ID</th>
                    <th>Name</th>
                    <th>Number of Questions</th>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="quizTable">

            <?php while ($row = mysqli_fetch_assoc($results)): ?>
        <tr>
            <td><?php echo $row['quiz_id']; ?></td>
            <td><?php echo $row['name']; ?></td>
            <td><?php echo $row['num_questions']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td class="actions">
                <a href="edit.php?id=<?php echo $row['quiz_id']; ?>">Edit</a> |
                <a href="delete.php?id=<?php echo $row['quiz_id']; ?>">Delete</a>
            </td>
        </tr>
    <?php endwhile; ?>

            </tbody>
        </table>

        <!-- Back Button -->
        <a href="#" class="back-button">Back</a>
    </div>

    <div id="footer">
        <ul class="nav">
            <li>About Us</li>
            <li>Terms and Conditions</li>
            <li>Privacy Policy</li>
            <li>Contact Us
                <img src="Icon/facebook.png" alt="facebook" id="facebook">&nbsp;
                <img src="Icon/instagram.png" alt="instagram" id="instagram">
            </li>
        </ul>
        
        <p id="copy">&copy; 2025 NameThatTune. All Rights Reserved.</p>
    </div>

    <!-- Script for Search Functionality -->
    <script>
        function performSearch() {
            const filter = document.getElementById("filter").value;
            const searchTerm = document.getElementById("search").value.toLowerCase();
            const table = document.getElementById("quizTable");
            const rows = table.getElementsByTagName("tr");

            for (let row of rows) {
                const cells = row.getElementsByTagName("td");
                let shouldDisplay = false;

                if (filter === "id" && cells[0].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                } else if (filter === "name" && cells[1].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                } else if (filter === "category" && cells[3].textContent.toLowerCase().includes(searchTerm)) {
                    shouldDisplay = true;
                }

                row.style.display = shouldDisplay ? "table-row" : "none";
            }
        }
    </script>
</body>
</html>
