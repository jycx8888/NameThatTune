<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        /* Same CSS as provided earlier */
    </style>
</head>
<body>
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
    echo "<p style='text-align: center; color: green;'>Connected successfully</p>";
    ?>

    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login" onclick="">
            <img src="account.png" alt="User avatar">
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
                <?php
                // Fetch quiz data from the database
                $sql = "SELECT quiz_id, name, num_questions, category FROM quizzes";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Output data of each row
                    while($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["quiz_id"] . "</td>";
                        echo "<td>" . $row["name"] . "</td>";
                        echo "<td>" . $row["num_questions"] . "</td>";
                        echo "<td>" . $row["category"] . "</td>";
                        echo "<td class='actions'><a href='#'>Edit</a> | <a href='#'>Delete</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No quizzes found</td></tr>";
                }
                ?>
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
