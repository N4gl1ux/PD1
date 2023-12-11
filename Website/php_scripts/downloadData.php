<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../loginScreen.php");
    exit();
}

include 'connectMusic.php';

$artistsSQL = "SELECT * FROM artists";
$artistsResult = $conn->query($artistsSQL);

$albumsSQL = "SELECT * FROM albums";
$albumsResult = $conn->query($albumsSQL);

$songsSQL = "SELECT * FROM songs";
$songsResult = $conn->query($songsSQL);

if (
    $artistsResult->num_rows > 0 ||
    $albumsResult->num_rows > 0 ||
    $songsResult->num_rows > 0
) {
    header('Content-Type: text/plain');
    header('Content-Disposition: attachment; filename="database_data.txt"');

    echo "### Artists Table ###\n";
    while ($row = $artistsResult->fetch_assoc()) {
        $data = implode("\t", $row);
        echo $data . "\n";
    }

    echo "\n";

    echo "### Albums Table ###\n";
    while ($row = $albumsResult->fetch_assoc()) {
        $data = implode("\t", $row);
        echo $data . "\n";
    }

    echo "\n";

    echo "### Songs Table ###\n";
    while ($row = $songsResult->fetch_assoc()) {
        $data = implode("\t", $row);
        echo $data . "\n";
    }
} else {
    echo "No data found in the database.";
}

$conn->close();
?>