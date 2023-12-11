<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginScreen.php");
    exit();
}

include '../php_scripts/connectMusic.php';

function handleDeleteFormSubmission($table, $id) {
    global $conn;

    switch ($table) {
        case 'artists':
            deleteArtist($id);
            break;

        case 'albums':
            deleteAlbum($id);
            break;

        case 'songs':
            deleteSong($id);
            break;

        default:
            echo "Invalid table";
            return;
    }
}

function deleteArtist($artist_id) {
    global $conn;

    $albumResult = $conn->query("SELECT id FROM albums WHERE artist_id = $artist_id");
    while ($albumRow = $albumResult->fetch_assoc()) {
        deleteAlbum($albumRow['id']);
    }

    $sql = "DELETE FROM artists WHERE id = $artist_id";
    if ($conn->query($sql) === TRUE) {
        echo "Artist deleted successfully";
    } else {
        echo "Error deleting artist: " . $conn->error;
    }
}

function deleteAlbum($album_id) {
    global $conn;

    $songResult = $conn->query("SELECT id FROM songs WHERE album_id = $album_id");
    while ($songRow = $songResult->fetch_assoc()) {
        deleteSong($songRow['id']);
    }
   
    $sql = "DELETE FROM albums WHERE id = $album_id";
    if ($conn->query($sql) === TRUE) {
        echo "Album deleted successfully";
    } else {
        echo "Error deleting album: " . $conn->error;
    }
}

function deleteSong($song_id) {
    global $conn;

    $songResult = $conn->query("SELECT album_id, title, sample_path, image_path FROM songs WHERE id = $song_id");
    
    if ($songResult->num_rows > 0) {
        $songRow = $songResult->fetch_assoc();
        $album_id = $songRow['album_id'];
        $song_title = $songRow['title'];
        $sample_path_server = $songRow['sample_path'];
        $image_path_server = $songRow['image_path'];

        deleteFiles($sample_path_server);
        deleteFiles($image_path_server);
    }

    $sql = "DELETE FROM songs WHERE id = $song_id";
    if ($conn->query($sql) === TRUE) {
        echo "Song deleted successfully";
    } else {
        echo "Error deleting song: " . $conn->error;
    }
}

function getArtistName($artist_id) {
    global $conn;

    $sql = "SELECT name FROM artists WHERE id = $artist_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Artist not found";
    }
}

function getAlbumTitle($album_id) {
    global $conn;

    $sql = "SELECT title FROM albums WHERE id = $album_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['title'];
    } else {
        return "Album not found";
    }
}

function getAlbumArtistName($album_id) {
    global $conn;

    $sql = "SELECT artists.name 
            FROM artists 
            INNER JOIN albums ON artists.id = albums.artist_id 
            WHERE albums.id = $album_id";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['name'];
    } else {
        return "Artist not found";
    }
}

function deleteFiles($path) { 
   if (is_dir($path)) {
        $files = glob($path . '/*');
        foreach ($files as $file) {
            deleteFiles($file);
        }
        rmdir($path);
    } elseif (is_file($path)) {
        unlink($path);
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
        <input type="hidden" name="form_type" value="delete_artist">
        <h2>Delete Artist</h2>
        Select Artist:
        <select name="id" required>
            <?php
			include '../php_scripts/connectMusic.php';
			
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
			include '../php_scripts/connectMusic.php';
			
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
			include '../php_scripts/connectMusic.php';
			
            $result = $conn->query("SELECT id, title FROM songs");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Delete Song">
    </form>
	<div style="margin-top: 15px;">
        <a href="../adminScreen.php" style="text-decoration: none; display: inline-block;">
            <button class="back-button">Go back to options</button>
        </a>
    </div>
</body>
</html>