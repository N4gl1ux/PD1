<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginScreen.php");
    exit();
}

include '../php_scripts/connectMusic.php';

function handleFormSubmission($table, $data) {
    global $conn;
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", $data) . "'";
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

    if ($conn->query($sql) === TRUE) {
        echo ucfirst($table) . " added successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];

    switch ($formType) {
        case 'add_artist':
            handleFormSubmission('artists', ['name' => $_POST['name']]);
            break;

        case 'add_album':
            handleFormSubmission('albums', ['title' => $_POST['title'], 'artist_id' => $_POST['artist_id']]);
            break;

        case 'add_song':
            handleFormSubmission('songs', [
                'title' => $_POST['title'],
                'album_id' => $_POST['album_id'],
                'sample_path' => $_POST['sample_path'],
                'image_path' => $_POST['image_path']
            ]);
            break;

        default:
            echo "Invalid form type";
            break;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<title>Add Data Screen</title>

    <meta charset="UTF-8">
    <meta name="description" content="Add data screen">
    <meta name="keywords" content="synthwave, music, favorite, retrowave">
    <meta name="author" content="Naglis Seliokas, Dovydas Kasulis, Lukas Malijauskas, Nedas Orlingis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="2023-12-09">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
            box-sizing: border-box;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
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
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="add_artist">
        <h2>Add Artist</h2>
        Name: <input type="text" name="name" required>
        <input type="submit" value="Add Artist">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="add_album">
        <h2>Add Album</h2>
        Title: <input type="text" name="title" required>
        Artist:
        <select name="artist_id" required>
            <?php
			include '../php_scripts/connectMusic.php';
			
            $result = $conn->query("SELECT id, name FROM artists");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Add Album">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="add_song">
        <h2>Add Song</h2>
        Title: <input type="text" name="title" required>
        Album:
        <select name="album_id" required>
            <?php
			include '../php_scripts/connectMusic.php';
			
            $result = $conn->query("SELECT id, title FROM albums");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <br> Sample Path: <input type="text" name="sample_path" required>
        Image Path: <input type="text" name="image_path" required>
        <input type="submit" value="Add Song">
    </form>
    <div style="margin-top: 15px;">
        <a href="../adminScreen.php" style="text-decoration: none; display: inline-block;">
            <button class="back-button">Go back to options</button>
        </a>
    </div>
</body>
</html>