<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if (!isset($_POST["blog_id"])) {
    header("Location: ../index.php");
    exit();
}

$blogId = (int) $_POST["blog_id"];
$userId = (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id FROM blogPost WHERE id = ?");
$stmt->bind_param("i", $blogId);
$stmt->execute();
if ($stmt->get_result()->num_rows !== 1) {
    header("Location: ../index.php");
    exit();
}

$likeStmt = $conn->prepare("SELECT id FROM blog_like WHERE blog_id = ? AND user_id = ?");
$likeStmt->bind_param("ii", $blogId, $userId);
$likeStmt->execute();
$likeResult = $likeStmt->get_result();

if ($likeResult->num_rows === 0) {
    $insertLike = $conn->prepare("INSERT INTO blog_like (blog_id, user_id) VALUES (?, ?)");
    $insertLike->bind_param("ii", $blogId, $userId);
    $insertLike->execute();
}

header("Location: ../view_blog.php?id=" . $blogId);
exit();
