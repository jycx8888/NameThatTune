<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NameThatTune - Register</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e0e0e0;
        }

        header {
            background-color: #ccc;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
        }

        header h1 {
            margin: 0;
            font-size: 24px;
        }

        header a {
            text-decoration: none;
            background-color: #333;
            color: #fff;
            padding: 8px 15px;
            border-radius: 5px;
            font-size: 14px;
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 160px);
        }

        .form-box {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .form-box img {
            width: 100px;
            margin-bottom: 20px;
        }

        .form-box input[type="text"], 
        .form-box input[type="password"] {
            width: 100%;
            padding: 10px 50px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-box label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        .form-box button {
            background-color: #333;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .form-box button:hover {
            background-color: #555;
        }

        footer {
            background-color: black;
            color: white;
            text-align: center;
            padding: 20px;
            font-size: 12px;
        }

        footer a {
            color: lightblue;
            text-decoration: none;
        }
    </style>
</head>
<body>

<header>
    <h1>NameThatTune</h1>
    <a href="#">Login/Sign Up</a>
</header>

<div class="container">
    <div class="form-box">
        <img src="logo-placeholder.png" alt="NameThatTune Logo">
        <form action="/register" method="post">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" required>
            
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>
            
            <label for="confirm-password">Confirm Password</label>
            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm Password" required>
            
            <div style="margin: 15px 0;">
                <input type="checkbox" id="terms" name="terms" required>
                <label for="terms">I have read and accept the <a href="#">Terms and Conditions</a></label>
            </div>
            
            <button type="submit">Register</button>
        </form>
    </div>
</div>

<footer>
    <p>About Us: NameThatTune is your go-to source for exploring music history...</p>
</footer>

</body>
</html>
