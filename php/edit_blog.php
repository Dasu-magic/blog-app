<?php

session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $blog_id = $_POST["id"];
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    $stmt = $conn->prepare(
        "UPDATE blogPost
         SET title = ?, content = ?, updated_at = NOW()
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param(
        "ssii",
        $title,
        $content,
        $blog_id,
        $user_id
    );

    $stmt->execute();

    header("Location: ../view_blog.php?id=" . $blog_id);
    exit();
}

?>