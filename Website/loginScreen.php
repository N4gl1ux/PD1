<?php
	session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="description" content="Login page">
    <meta name="keywords" content="synthwave, music, favorite, retrowave">
    <meta name="author" content="Naglis Seliokas, Dovydas Kasulis, Lukas Malijauskas, Nedas Orlingis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="2023-12-09">

    <meta http-equiv="Content-Security-Policy" content="
        default-src 'self';
        style-src 'self' 'unsafe-inline';
    ">

    <title>Login Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #837f7f;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            width: 300px;
            margin: auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }
		
		.back-button {
            background-color: #ff0000;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Administrator Login</h2>
        <?php
			if (isset($_GET['error'])) {
				echo '<p style="color: red;">' . htmlspecialchars($_GET['error']) . '</p>';
			}
        ?>
        <form action="php_scripts/loginProcess.php" method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>

            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>

            <input type="submit" value="Login">
        </form>
	<div style="margin-top: 15px;">
            <a href="index.html" style="text-decoration: none; display: inline-block;">
                <button class="back-button">Go back to website</button>
            </a>
        </div>
    </div>
</body>
</html>