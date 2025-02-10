<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Management</title>
    <link rel="stylesheet" href="user_hamburger_menu.css">
    <link rel="stylesheet" href="user_header.css">
    <style>
        #container {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-grow: 1;
            padding: 20px;
        }

        #form-container {
            width: 500px;
            padding: 20px;
            background-color: white;
            border-radius: 30px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        #form-container h2 {
            text-align: center;
            font-family: "Lalezar", system-ui;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"], input[type="file"] {
            width: 90%;
            padding: 10px;
            border: 1px solid black;
            border-radius: 15px;
            font-size: 14px;
        }

        .options {
            margin-bottom: 20px;
        }

        .option-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }

        .option-item input[type="text"] {
            flex: 1;
            margin-right: 10px;
            border-radius: 10px;
        }

        .option-item img {
            cursor: pointer;
            width: 30px;
            height: 30px;
            margin-left: 10px;
        }

        .option-item img.selected {
            border: 2px solid green;
            border-radius: 50%;
        }

        .submit-button {
            width: 100%;
            padding: 10px;
            background-color: #4B006E;
            color: white;
            border: none;
            border-radius: 15px;
            cursor: pointer;
            font-family: "Lalezar", system-ui;
            font-size: 18px;
        }

        .submit-button:hover {
            background-color: #CBC3E3;
        }
    </style>
</head>
<body>
    <!-- Header Section -->
    <div id="header">
        <h1>NameThatTune</h1>
        <div id="login">
            <img src="avatar.png" alt="User Avatar">
            <p>Username</p>
        </div>
    </div>

    <!-- Main Content Section -->
    <div id="container">
        <div id="form-container">
            <h2>Edit Quiz</h2>
            <form id="quiz-form">
                <div class="form-group">
                    <label for="song-name">Song Name</label>
                    <input type="text" id="song-name" placeholder="Enter Song Name">
                </div>

                <div class="form-group">
                    <label for="song-photo">Song Photo</label>
                    <input type="file" id="song-photo" accept="image/*">
                    <div id="song-photo-display" style="margin-top: 10px;"></div>
                </div>

                <div class="form-group">
                    <label for="song-mp3">Song MP3</label>
                    <input type="file" id="song-mp3" accept=".mp3">
                    <div id="mp3-display" style="margin-top: 10px;"></div>
                </div>

                <div class="form-group options">
                    <label>Options</label>
                    <div class="option-item">
                        <input type="text" placeholder="Option 1">
                        <img src="Icon/no_select.png" alt="Correct Icon" class="correct-icon">
                    </div>
                    <div class="option-item">
                        <input type="text" placeholder="Option 2">
                        <img src="Icon/no_select.png" alt="Correct Icon" class="correct-icon">
                    </div>
                    <div class="option-item">
                        <input type="text" placeholder="Option 3">
                        <img src="Icon/no_select.png" alt="Correct Icon" class="correct-icon">
                    </div>
                    <div class="option-item">
                        <input type="text" placeholder="Option 4">
                        <img src="Icon/no_select.png" alt="Correct Icon" class="correct-icon">
                    </div>
                </div>

                <button type="submit" class="submit-button">Confirm</button>
            </form>
        </div>
    </div>

    <script>
        const songPhotoInput = document.getElementById('song-photo');
        const songPhotoDisplay = document.getElementById('song-photo-display');
        const songMp3Input = document.getElementById('song-mp3');
        const mp3Display = document.getElementById('mp3-display');
        const correctIcons = document.querySelectorAll('.correct-icon');

        // Handle song photo selection
        songPhotoInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    songPhotoDisplay.innerHTML = `
                        <img src="${e.target.result}" alt="Song Photo" style="max-width: 200px;">
                        <button id="delete-photo">Delete Photo</button>
                    `;
                    document.getElementById('delete-photo').addEventListener('click', () => {
                        songPhotoDisplay.innerHTML = '';
                        songPhotoInput.value = '';
                    });
                };
                reader.readAsDataURL(file);
            } else {
                alert("Please select a valid image file.");
            }
        });

        // Handle MP3 file selection
        songMp3Input.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file && file.type === "audio/mpeg") {
                const fileURL = URL.createObjectURL(file);
                mp3Display.innerHTML = `
                    <p>Selected MP3: ${file.name}</p>
                    <audio controls src="${fileURL}"></audio>
                    <button id="delete-mp3">Delete</button>
                `;
                document.getElementById('delete-mp3').addEventListener('click', () => {
                    mp3Display.innerHTML = '';
                    songMp3Input.value = '';
                });
            } else {
                alert("Please select a valid MP3 file.");
            }
        });

        // Handle selecting the correct answer option
        correctIcons.forEach(icon => {
            icon.addEventListener('click', () => {
                correctIcons.forEach(icon => icon.classList.remove('selected'));
                icon.classList.add('selected');
            });
        });
    </script>
</body>
</html>
