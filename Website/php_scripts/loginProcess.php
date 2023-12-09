<?php
include 'connectAuth.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $conn->real_escape_string($_POST["username"]);
    $password = $conn->real_escape_string($_POST["password"]);

    $stmt = $conn->prepare("SELECT * FROM credentials WHERE username = ?");
    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $storedSalt = $row['salt'];
        $storedHashedPassword = $row['password'];

        $combinedPassword = $password . $storedSalt;

        $hashedPassword = hash('sha256', $combinedPassword);

        if ($hashedPassword === $storedHashedPassword) {
            header("Location: adminScreen.html");
            $stmt->close();
            $conn->close();
            exit();
        } else {
            header("Location: loginScreen.php?error=Invalid password");
            $stmt->close();
            $conn->close();
            exit();
        }
    } else {
        header("Location: loginScreen.php?error=Invalid username");
        $stmt->close();
        $conn->close();
        exit();
        
    }
}
?>