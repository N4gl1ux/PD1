<?php
include '..\php_scripts\connectMusic.php';

function handleDeleteFormSubmission($table, $id) {
	global $conn;
	
    switch ($table) {
        case 'artists':
            // Cascade delete: Delete albums and songs related to the artist
            $conn->query("DELETE FROM songs WHERE album_id IN (SELECT id FROM albums WHERE artist_id = $id)");
            $conn->query("DELETE FROM albums WHERE artist_id = $id");
            break;

        case 'albums':
            // Cascade delete: Delete songs related to the album
            $conn->query("DELETE FROM songs WHERE album_id = $id");
            break;

        case 'songs':
            // Delete the song
            break;

        default:
            echo "Invalid table";
            return;
    }

    // Delete the record from the specified table
    $sql = "DELETE FROM $table WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo ucfirst($table) . " deleted successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];

    switch ($formType) {
        case 'delete_artist':
            handleDeleteFormSubmission('artists', $_POST['id']);
            break;

        case 'delete_album':
            handleDeleteFormSubmission('albums', $_POST['id']);
            break;

        case 'delete_song':
            handleDeleteFormSubmission('songs', $_POST['id']);
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
<title>Delete Data Screen</title>

    <meta charset="UTF-8">
    <meta name="description" content="Delete data screen">
    <meta name="keywords" content="synthwave, music, favorite, retrowave">
    <meta name="author" content="Naglis Seliokas, Dovydas Kasulis, Lukas Malijauskas, Nedas Orlingis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="2023-12-09">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #837f7f;
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
    </style>
    
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="delete_artist">
        <h2>Delete Artist</h2>
        Select Artist:
        <select name="id" required>
            <?php
			include '..\php_scripts\connectMusic.php';
			
            $result = $conn->query("SELECT id, name FROM artists");
			
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Delete Artist">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="delete_album">
        <h2>Delete Album</h2>
        Select Album:
        <select name="id" required>
            <?php
			include '..\php_scripts\connectMusic.php';
			
            $result = $conn->query("SELECT id, title FROM albums");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Delete Album">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="delete_song">
        <h2>Delete Song</h2>
        Select Song:
        <select name="id" required>
            <?php
			include '..\php_scripts\connectMusic.php';
			
            $result = $conn->query("SELECT id, title FROM songs");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Delete Song">
    </form>
</body>
</html>