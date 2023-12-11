<?php
include 'connectMusic.php';

if (isset($_GET['id'])) {
    $songId = $_GET['id'];
    $result = $conn->query("SELECT title, album_id FROM songs WHERE id = $songId");

    if ($result->num_rows > 0) {
        $songInfo = $result->fetch_assoc();
        echo json_encode($songInfo);
    }
}

$conn->close();
?>