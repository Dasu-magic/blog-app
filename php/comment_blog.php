<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if (!isset($_POST["blog_id"]) || !isset($_POST["comment"])) {
    header("Location: ../index.php");
    exit();
}

$blogId = (int) $_POST["blog_id"];
$comment = trim($_POST["comment"]);
$userId = (int) $_SESSION["user_id"];

if ($comment === "") {
    header("Location: ../view_blog.php?id=" . $blogId . "&error=" . urlencode("Comment cannot be empty."));
    exit();
}

$checkBlog = $conn->prepare("SELECT id FROM blogPost WHERE id = ?");
$checkBlog->bind_param("i", $blogId);
$checkBlog->execute();
if ($checkBlog->get_result()->num_rows !== 1) {
    header("Location: ../index.php");
    exit();
}

$insertComment = $conn->prepare("INSERT INTO blog_comment (blog_id, user_id, comment) VALUES (?, ?, ?)");
$insertComment->bind_param("iis", $blogId, $userId, $comment);
$insertComment->execute();

header("Location: ../view_blog.php?id=" . $blogId);
exit();
