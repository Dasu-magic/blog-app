<?php

session_start();

require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare(
        "INSERT INTO blogPost (user_id, title, content)
         VALUES (?, ?, ?)"
    );

    $stmt->bind_param(
        "iss",
        $user_id,
        $title,
        $content
    );

    if ($stmt->execute()) {

        header("Location: ../index.php");
        exit();

    } else {

        echo "Error creating blog.";

    }

    $stmt->close();
    $conn->close();
}

?>