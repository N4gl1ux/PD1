<?php
include 'connectMusic.php';

if (isset($_GET['id'])) {
    $artistId = $_GET['id'];
    $result = $conn->query("SELECT name FROM artists WHERE id = $artistId");

    if ($result->num_rows > 0) {
        $artistInfo = $result->fetch_assoc();
        echo json_encode($artistInfo);
    }
}

$conn->close();
?>