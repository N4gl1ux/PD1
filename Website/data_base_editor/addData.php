<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginScreen.php");
    exit();
}

include '../php_scripts/connectMusic.php';

$successMessageArtist = $successMessageAlbum = $successMessageSong = "";

function handleFormSubmission($table, $data, &$successMessage) {
    global $conn;
    $columns = implode(', ', array_keys($data));
    $values = "'" . implode("', '", $data) . "'";
    $sql = "INSERT INTO $table ($columns) VALUES ($values)";

    if ($conn->query($sql) === TRUE) {
        $successMessage = ucfirst($table) . " added successfully";
    } else {
        $successMessage = "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];

    switch ($formType) {
        case 'add_artist':
            handleFormSubmission('artists', ['name' => $_POST['name']], $successMessageArtist);
            break;

        case 'add_album':
            handleFormSubmission('albums', ['title' => $_POST['title'], 'artist_id' => $_POST['artist_id']], $successMessageAlbum);
            break;

        case 'add_song':
            $title = $_POST['title'];
            $album_id = $_POST['album_id'];

            $stmt = $conn->prepare("SELECT artists.name AS artist_name, albums.title AS album_name FROM albums JOIN artists ON albums.artist_id = artists.id WHERE albums.id = ?");
            $stmt->bind_param("i", $album_id);
            $stmt->execute();
            $stmt->bind_result($artist_name, $album_name);
            $stmt->fetch();
            $stmt->close();

            $sample_extension = pathinfo($_FILES['sample_path']['name'], PATHINFO_EXTENSION);
            $sample_path_server = '../song samples/' . $artist_name . '/' . $album_name . '/' . $title . '.' . $sample_extension;

            $image_extension = pathinfo($_FILES['image_path']['name'], PATHINFO_EXTENSION);
            $image_path_server = '../song images/' . $artist_name . '/' . $album_name . '/' . $title . '.' . $image_extension;

            if (file_exists($sample_path_server) || file_exists($image_path_server)) {
                $successMessageSong = 'Error: The file paths already exist. Choose a different title or album.';
                break;
            }

            if (strtolower($sample_extension) != 'mp3') {
                $successMessageSong = 'Error: Only MP3 files are allowed for Sample Path.';
                break;
            }

            $allowed_image_types = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array(strtolower($image_extension), $allowed_image_types)) {
                $successMessageSong = 'Error: Only JPG, JPEG, PNG, and GIF files are allowed for Image Path.';
                break;
            }

            if (!is_dir(dirname($sample_path_server))) {
                mkdir(dirname($sample_path_server), 0777, true);
            }

            if (!is_dir(dirname($image_path_server))) {
                mkdir(dirname($image_path_server), 0777, true);
            }

            if (move_uploaded_file($_FILES['sample_path']['tmp_name'], $sample_path_server) &&
                move_uploaded_file($_FILES['image_path']['tmp_name'], $image_path_server)) {

                handleFormSubmission('songs', [
                    'title' => $title,
                    'album_id' => $album_id,
                    'sample_path' => $sample_path_server,
                    'image_path' => $image_path_server
                ], $successMessageSong);
            } else {
                $successMessageSong = 'Error uploading files';
            }
            break;

        default:
            echo 'Invalid form type';
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
            height: 450px;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input,
        select,
        [type="file"] {
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

        .success-message {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #4caf50;
            color: white;
            padding: 8px;
            border-radius: 0 0 8px 8px;
            box-sizing: border-box;
            display: none;
        }

        .success-message.show {
            display: block;
        }
    </style>
</head>
<body>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="add_artist">
        <h2>Add Artist</h2>
        Name: <input type="text" name="name" required>
        <input type="submit" value="Add Artist">
        <?php if (isset($successMessageArtist) && !empty($successMessageArtist)) : ?>
            <div class="success-message artist show"><?php echo $successMessageArtist; ?></div>
        <?php endif; ?>
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
        <?php if (isset($successMessageAlbum) && !empty($successMessageAlbum)) : ?>
            <div class="success-message album show"><?php echo $successMessageAlbum; ?></div>
        <?php endif; ?>
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
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
        <br> Sample Path: <input type="file" name="sample_path" accept=".mp3" required>
        Image Path: <input type="file" name="image_path" accept="image/*" required>
        <input type="submit" value="Add Song">
        <?php if (isset($successMessageSong) && !empty($successMessageSong)) : ?>
            <div class="success-message song show"><?php echo $successMessageSong; ?></div>
        <?php endif; ?>
    </form>
    <div style="margin-top: 15px;">
        <a href="../adminScreen.php" style="text-decoration: none; display: inline-block;">
            <button class="back-button">Go back to options</button>
        </a>
    </div>
</body>
</html>

