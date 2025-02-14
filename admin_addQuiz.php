<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Add New Quiz</title>
    <link rel="stylesheet" href="user_header.css">
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <style>
        body {
            background-color: #d1a3ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 1000px;
            text-align: center;
            margin-top: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            padding: 10px;
            border: 1px solid black;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            min-width: 120px;
            margin: 10px;
            transition: all 0.3s ease;
        }
        button:hover {
            background-color: #0056b3;
        }
        .actions a {
            background-color: #ACD7EC;
            color: black;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            margin: 2px;
        }
        .actions a:hover {
            background-color: #89CFF0;
        }
        
        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            overflow-y: auto; /* Add this to enable vertical scrolling */
        }

        .modal-content {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            margin: 5% auto; /* Changed from 15% to 5% to position higher */
            text-align: center;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
            max-height: 90vh; /* Set maximum height */
            overflow-y: auto; /* Enable scrolling within the modal content */
        }

        .modal-content input {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .modal-content label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .modal-content select {
            width: 80%;
            padding: 8px;
            margin: 5px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            cursor: pointer;
        }
        
        .modal-content select:focus {
            outline: none;
            border-color: #007bff;
        }

        .option-container div {
        display: flex;
        align-items: center;
        background-color:rgb(166, 92, 176);
        border-radius: 20px;
        padding: 10px;
        margin-bottom: 10px;
        position: relative;
        border: 2px solidrgb(130, 185, 229);
        }

        .option-container input[type="text"] {
            flex-grow: 1;
            border: none;
            background: transparent;
            font-size: 16px;
            color: black;
            outline: none;
        }

        .option-container input[type="radio"] {
            position: absolute;
            right: 10px;
            width: 20px;
            height: 20px;
            accent-color: green;
        }

        .option-container .icon {
            margin-right: 10px;
            font-size: 16px;
        }

        .close {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="<?php echo htmlspecialchars($profile_picture_path); ?>">
            <p><?php echo htmlspecialchars($username); ?></p>
        </div>
    </div>

    <div class="container">
        <h2>Add New Quiz</h2>
        <table>
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Options</th>
                    <th>Answer</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Q001</td>
                    <td>Never Gonna Give You Up, Niggas in Paris, Blank Space, YMCA</td>
                    <td>Never Gonna Give You Up</td>
                    <td class="actions">
                        <a href="edit_question.php?question_id=Q001">Edit</a> |
                        <a href="delete_question.php?question_id=Q001">Delete</a>
                    </td>
                </tr>
            </tbody>
        </table>
        <br><br>

        <button type="button" onclick="openModal()">Add Quiz</button>
        <button type="button" onclick="window.location.href='admin_quiz_management.php'">Cancel</button>
    </div>

    <!-- Modal -->
    <div id="quizModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Add New Question</h2>
            <form id="addQuestionForm">
                <div style="margin: 15px;">
                    <label for="questionId">Question ID:</label>
                    <input type="text" id="questionId" name="questionId" required>
                </div>
                <div style="margin: 15px;">
                    <label for="options">New Quiz Category:</label>
                    <select id="options" name="options" required style="width: 80%; padding: 8px; margin: 5px 0; border: 1px solid #ddd; border-radius: 4px;">
                        <option value="">Select a category</option>
                        <option value="english">English</option>
                        <option value="japanese">Japanese</option>
                        <option value="korean">Korean</option>
                    </select>
                </div>
                <div style="margin: 15px;">
                    <label for="answer">Correct Answer:</label>
                    <input type="text" id="answer" name="answer" required>
                </div>
            </form>

            <label for="category">Category:</label>
            <input type="text" id="category" name="category" required>

            <label for="songName">Song Name:</label>
            <input type="text" id="songName" name="songName" required>

            <label for="songUpload">Song Upload (8 secs):</label>
            <input type="file" id="songUpload" name="songUpload" accept="audio/mp3">

            <label for="songPhoto">Song Photo:</label>
            <input type="file" id="songPhoto" name="songPhoto" accept="image/*">
            
            <label>Options:</label>
            <div class="option-container">
                <div>
                    <span class="icon">↗</span>
                    <input type="text" name="option1" value="Never Gonna Give You Up" readonly>
                    <input type="radio" name="correctOption" value="option1" checked>
                </div>
                <div>
                    <span class="icon">↗</span>
                    <input type="text" name="option2" value="Niggas in Paris" readonly>
                    <input type="radio" name="correctOption" value="option2">
                </div>
                <div>
                    <span class="icon">↗</span>
                    <input type="text" name="option3" value="Blank Space" readonly>
                    <input type="radio" name="correctOption" value="option3">
                </div>
                <div>
                    <span class="icon">↗</span>
                    <input type="text" name="option4" value="YMCA" readonly>
                    <input type="radio" name="correctOption" value="option4">
                </div>
            </div>

            
            <button type="submit">Add Question</button>
            <button type="button" onclick="closeModal()">Cancel</button>
        </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById("quizModal").style.display = "block";
        }
        
        function closeModal() {
            document.getElementById("quizModal").style.display = "none";
            document.getElementById("addQuestionForm").reset();
        }
        
        document.getElementById("addQuestionForm").onsubmit = function(e) {
            e.preventDefault();
            // Here you would typically add AJAX call to save the question
            alert("Question Added Successfully!");
            closeModal();
        };
    </script>
</body>
</html>
