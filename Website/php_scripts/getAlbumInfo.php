<?php
include 'connectMusic.php';

if (isset($_GET['id'])) {
    $albumId = $_GET['id'];
    $result = $conn->query("SELECT title, artist_id FROM albums WHERE id = $albumId");

    if ($result->num_rows > 0) {
        $albumInfo = $result->fetch_assoc();
        echo json_encode($albumInfo);
    }
}

$conn->close();
?>