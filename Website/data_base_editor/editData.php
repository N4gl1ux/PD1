<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginScreen.php");
    exit();
}

include '../php_scripts/connectMusic.php';

function handleEditFormSubmission($table, $id, $data) {
    global $conn;
    
    $update_values = [];
    foreach ($data as $key => $value) {
        $update_values[] = "$key = '$value'";
    }
    $update_values_str = implode(', ', $update_values);

    $sql = "UPDATE $table SET $update_values_str WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        return ucfirst($table) . " updated successfully";
    } else {
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

$successMessageArtist = "";
$successMessageAlbum = "";
$successMessageSong = "";						   						  
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $formType = $_POST['form_type'];

    switch ($formType) {
        case 'edit_artist':
            $successMessageArtist = handleEditFormSubmission('artists', $_POST['id'], ['name' => $_POST['name']]);
            break;

        case 'edit_album':
            $successMessageAlbum = handleEditFormSubmission('albums', $_POST['id'], ['title' => $_POST['title'], 'artist_id' => $_POST['artist_id']]);
            break;

        case 'edit_song':
            $successMessageSong = handleEditFormSubmission('songs', $_POST['id'], [
                'title' => $_POST['title'],
                'album_id' => $_POST['album_id'],
            ]);
            break;

        default:
            $successMessageArtist = $successMessageAlbum = $successMessageSong = "Invalid form type";
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
    <meta name="description" content="Edit data screen">
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
			height: 500px;
            display: flex;
            flex-direction: column;
            position: relative;
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

        .success-message.artist { display: <?php echo $successMessageArtist ? 'block' : 'none'; ?>; }
        .success-message.album { display: <?php echo $successMessageAlbum ? 'block' : 'none'; ?>; }
        .success-message.song { display: <?php echo $successMessageSong ? 'block' : 'none'; ?>; }

        h2 {
            text-align: center;
            color: #333;
        }

        input,
		select {		
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
    <input type="hidden" name="form_type" value="edit_artist">
    <h2>Edit Artist</h2>
    Select Artist:
    <select name="id" required onchange="loadArtistInfo(this.value)">
        <?php
        include '../php_scripts/connectMusic.php';

        $result = $conn->query("SELECT id, name FROM artists");
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
        }
        $conn->close();
        ?>
    </select>
    New Name: <input type="text" name="name" id="artistName" required>
    <input type="submit" value="Edit Artist">
	<div class="success-message artist"><?php echo $successMessageArtist; ?></div>																				
	</form>

	<script>
	function loadArtistInfo(artistId) {
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				var artistInfo = JSON.parse(xhr.responseText);
				document.getElementById("artistName").value = artistInfo.name;
			}
		};
		xhr.open("GET", "../php_scripts/getArtistInfo.php?id=" + artistId, true);
		xhr.send();
	}
	</script>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<input type="hidden" name="form_type" value="edit_album">
		<h2>Edit Album</h2>
		Select Album:
		<select name="id" required onchange="loadAlbumInfo(this.value)">
			<?php
			include '../php_scripts/connectMusic.php';
			$result = $conn->query("SELECT id, title FROM albums");
			while ($row = $result->fetch_assoc()) {
				echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
			}
			$conn->close();
			?>
		</select>
		New Title: <input type="text" name="title" id="albumTitleInput" required>
		New Artist:
		<select name="artist_id" id="albumArtistId" required>
			<?php
			include '../php_scripts/connectMusic.php';
			$result = $conn->query("SELECT id, name FROM artists");
			while ($row = $result->fetch_assoc()) {
				echo "<option value='" . $row['id'] . "'>" . $row['name'] . "</option>";
			}
			$conn->close();
			?>
		</select>
		<input type="submit" value="Edit Album">
		<div class="success-message album"><?php echo $successMessageAlbum; ?></div>
	</form>
	
	<script>
	function loadAlbumInfo(albumId) {
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				var albumInfo = JSON.parse(xhr.responseText);
				document.getElementById("albumTitleInput").value = albumInfo.title;
				document.getElementById("albumArtistId").value = albumInfo.artist_id;
			}
		};
		xhr.open("GET", "../php_scripts/getAlbumInfo.php?id=" + albumId, true);
		xhr.send();
	}
	</script>

	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
		<input type="hidden" name="form_type" value="edit_song">
		<h2>Edit Song</h2>
		Select Song:
		<select name="id" required onchange="loadSongInfo(this.value)">
			<?php
			include '../php_scripts/connectMusic.php';
			$result = $conn->query("SELECT id, title FROM songs");
			while ($row = $result->fetch_assoc()) {
				echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
			}
			$conn->close();
			?>
		</select>
		New Title: <input type="text" name="title" id="songTitleInput" required>
		New Album:
		<select name="album_id" id="songAlbumId" required>
			<?php
			include '../php_scripts/connectMusic.php';
			$result = $conn->query("SELECT id, title FROM albums");
			while ($row = $result->fetch_assoc()) {
				echo "<option value='" . $row['id'] . "'>" . $row['title'] . "</option>";
			}
			$conn->close();
			?>
		</select>
		<input type="submit" value="Edit Song">
		<div class="success-message song"><?php echo $successMessageSong; ?></div>
	</form>
	
	<script>
	function loadSongInfo(songId) {
		var xhr = new XMLHttpRequest();
		xhr.onreadystatechange = function() {
			if (xhr.readyState == 4 && xhr.status == 200) {
				var songInfo = JSON.parse(xhr.responseText);
				document.getElementById("songTitleInput").value = songInfo.title;
				document.getElementById("songAlbumId").value = songInfo.album_id;
			}
		};
		xhr.open("GET", "../php_scripts/getSongInfo.php?id=" + songId, true);
		xhr.send();
	}
	</script>
	<div style="margin-top: 15px;">
        <a href="../adminScreen.php" style="text-decoration: none; display: inline-block;">
            <button class="back-button">Go back to options</button>
        </a>
    </div>
</body>
</html>