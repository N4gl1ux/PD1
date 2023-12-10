<?php
include '..\php_scripts\connectMusic.php';

function handleEditFormSubmission($table, $id, $data) {
    global $conn;
    
    $update_values = [];
    foreach ($data as $key => $value) {
        $update_values[] = "$key = '$value'";
    }
    $update_values_str = implode(', ', $update_values);

    $sql = "UPDATE $table SET $update_values_str WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        echo ucfirst($table) . " updated successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];

    switch ($formType) {
        case 'edit_artist':
            handleEditFormSubmission('artists', $_POST['id'], ['name' => $_POST['name']]);
            break;

        case 'edit_album':
            handleEditFormSubmission('albums', $_POST['id'], ['title' => $_POST['title'], 'artist_id' => $_POST['artist_id']]);
            break;

        case 'edit_song':
            handleEditFormSubmission('songs', $_POST['id'], [
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
<title>Edit Data Screen</title>

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
        <input type="hidden" name="form_type" value="edit_artist">
        <h2>Edit Artist</h2>
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
        New Name: <input type="text" name="name" required>
        <input type="submit" value="Edit Artist">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="edit_album">
        <h2>Edit Album</h2>
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
        New Title: <input type="text" name="title" required>
        New Artist:
        <select name="artist_id" required>
            <?php
			include '..\php_scripts\connectMusic.php';
			
            $result = $conn->query("SELECT id, name FROM artists");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        <input type="submit" value="Edit Album">
    </form>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <input type="hidden" name="form_type" value="edit_song">
        <h2>Edit Song</h2>
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
        New Title: <input type="text" name="title" required>
        New Album:
        <select name="album_id" required>
            <?php
			include '..\php_scripts\connectMusic.php';
			
            $result = $conn->query("SELECT id, title FROM albums");
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
            }
            $conn->close();
            ?>
        </select>
        New Sample Path: <input type="text" name="sample_path" required>
        New Image Path: <input type="text" name="image_path" required>
        <input type="submit" value="Edit Song">
    </form>
</body>
</html>