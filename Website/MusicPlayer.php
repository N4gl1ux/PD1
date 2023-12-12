<?php
session_start();

if (!isset($_COOKIE['cookie_choice'])) {
    $cookieScript = '
        <script>
            if (confirm("Do you want to remember your last song?")) {
                document.cookie = "cookie_choice=accepted; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
            } else {
                document.cookie = "cookie_choice=declined; expires=Fri, 31 Dec 9999 23:59:59 GMT; path=/";
            }
        </script>';
} else {
    $cookieScript = '';
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Favorite tracks and albums</title>
    <meta charset="UTF-8">
    <meta name="description" content="Page where my favorite tracks and albums are placed">
    <meta name="keywords" content="synthwave, music, favorite, retrowave">
    <meta name="author" content="Naglis Seliokas, Dovydas Kasulis, Lukas Malijauskas, Nedas Orlingis">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="date" content="2023-10-11">
    <link rel="stylesheet" href="Styles.css">

    <style>
        body {
            background-image: url("BackgroundPictures/ForSongs.jpg");
        }

        div.gallery {
            margin: 5px;
            border: 1px solid #ccc;
            float: left;
            width: 220px;
            height: 300px;
        }

        div.gallery:hover {
            border: 1px solid #777;
        }

        div.gallery img {
            width: 100%;
            height: auto;
        }

        div.desc {
            padding: 30px;
            text-align: center;
            color: white;
        }

        h4 {
            color: white;
        }

        .downloadLink {
            text-decoration: underline;
        }

        h4.artistName {
            margin-left: 10px; 
        }
    </style>
</head>
<body>

<ul class="meniu">
    <li><a href="index.html">HOME</a></li>
    <li><a href="WhatIsSynthwave.html">WHAT IS SYNTHWAVE</a></li>
    <li><a href="SynthwaveHistory.html">SYNTHWAVE HISTORY AND POPULAR ARTISTS</a></li>
    <li><a class="active" href="MusicPlayer.php">MUSIC PLAYER</a></li>
    <li><a class="icon" href="index.html"><img src="BackgroundPictures/icontest.png"></a></li>
</ul>

<div class="text">
    <header>
        <h1 class="header">Music Player</h1>
    </header>

	<?php
		include 'php_scripts/connectMusic.php';

		$cookieChoice = isset($_COOKIE['cookie_choice']) ? $_COOKIE['cookie_choice'] : '';

		$resultSongIds = $conn->query("SELECT id FROM songs");
                $songIds = [];
                while ($rowSongIds = $resultSongIds->fetch_assoc()) {
                        $songIds[] = $rowSongIds['id'];
                }

		$currentSongId = 0;

		if ($cookieChoice === 'accepted' && isset($_SESSION['lastSongId'])) {
			$currentSongId = $_SESSION['lastSongId'];
			$_GET['currentSongId'] = $currentSongId;
		}

		if (isset($_GET['currentSongId'])) {
			$currentSongId = $_GET['currentSongId'];
		} else {
			$currentSongId = $songIds[0];
		}

		if (isset($_GET['changeSong'])) {
			$change = $_GET['changeSong'] === 'prev' ? -1 : 1;
			$currentIndex = array_search($currentSongId, $songIds);

			if ($currentIndex !== false) {
				$currentIndex += $change;

				if ($currentIndex < 0) {
					$currentIndex = count($songIds) - 1;
				} elseif ($currentIndex >= count($songIds)) {
					$currentIndex = 0;
				}

				$currentSongId = $songIds[$currentIndex];
				$_SESSION['lastSongId'] = $currentSongId;
			}
		}

		$result = $conn->query("SELECT songs.title as song_title, artists.name as artist_name, albums.title as album_title,
			songs.sample_path, songs.image_path
			FROM songs
			LEFT JOIN albums ON songs.album_id = albums.id
			LEFT JOIN artists ON albums.artist_id = artists.id
			WHERE songs.id = $currentSongId");

		if ($result && $result->num_rows > 0) {
			$row = $result->fetch_assoc();

			echo '<h4>' . $row['album_title'] . '</h4>';
			echo '<img src="' . $row['image_path'] . '" alt="' . $row['song_title'] . '" style="border: 1px solid white;">';
			echo '<h3>' . $row['song_title'] . '</h3>';
			echo '<h4 class="artistName">' . $row['artist_name'] . '</h4>';

			echo '<div class="songButtons">';
			echo '<form method="get" action="' . $_SERVER['PHP_SELF'] . '">';
			echo '<input type="hidden" name="currentSongId" value="' . $currentSongId . '">';
			echo '<button type="submit" name="changeSong" value="prev">Previous Song</button>';
			echo '<button type="submit" name="changeSong" value="next">Next Song</button>';
			echo '</form>';
			echo '</div>';

			echo '<audio controls>';
			echo '<source src="' . $row['sample_path'] . '" type="audio/mpeg">';
			echo '</audio>';
			echo '<br>';
			echo '<a class="downloadLink" href="' . $row['sample_path'] . '" download="' . $row['song_title'] . '">Download</a>';
		} else {
			echo '<h3>No songs available</h3>';
		}

		$conn->close();
	?>
	<h1 class="header">Favorite albums:</h1>
    <div class="gallery">
        <a target="_blank" href="Album/CB.jpg">
          <img src="Album/CB.jpg" alt="EPII | Carpenter Brut" width="600" height="400">
        </a>
        <div class="desc">EPII | Carpenter Brut</div>
    </div>
      
    <div class="gallery">
        <a target="_blank" href="Album/DwtD.jpg">
          <img src="Album/DwtD.jpg" alt="Horizon | Dance with the dead" width="600" height="400">
        </a>
        <div class="desc">Horizon | Dance with the dead</div>
    </div>
      
    <div class="gallery">
        <a target="_blank" href="Album/LazerHawkRedline.jpg">
          <img src="Album/LazerHawkRedline.jpg" alt="Redline | Lazerhawk" width="600" height="400">
        </a>
        <div class="desc">Redline | Lazerhawk</div>
    </div>
      
    <div class="gallery">
        <a target="_blank" href="Album/LazerHawkVisitors.jpg">
          <img src="Album/LazerHawkVisitors.jpg" alt="Visitors | Lazerhawk" width="600" height="400">
        </a>
        <div class="desc">Visitors | Lazerhawk</div>
    </div>
</div>

    <footer>
        <ul class="footer">
            <li><a href="ContactUs.html">CONTACT US</a></li>
            <li><a href="sitemap.html">SITEMAP</a></li>
            <li><a href="adminScreen.php">ADMIN SCREEN</a></li>
            <li class="date">Copyright Â© 2023 Nag S Synthwave team</li>
        </ul>
    </footer>

<?php echo $cookieScript; ?>

</body>
</html>
