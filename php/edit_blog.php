<?php

session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $blog_id = (int) $_POST["id"];
    $title = trim($_POST["title"]);
    $content = trim($_POST["content"]);
    $user_id = $_SESSION["user_id"];

    $existing = $conn->prepare("SELECT image_path FROM blogPost WHERE id = ? AND user_id = ?");
    $existing->bind_param("ii", $blog_id, $user_id);
    $existing->execute();
    $existingResult = $existing->get_result();
    $imagePath = "";

    if ($existingResult->num_rows === 1) {
        $blog = $existingResult->fetch_assoc();
        $imagePath = $blog["image_path"] ?? "";
    }

    if (isset($_FILES["blog_image"]) && $_FILES["blog_image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/../uploads/blogs/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($_FILES["blog_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ["png", "jpg", "jpeg", "gif", "webp"];

        if (in_array($fileExt, $allowed, true)) {
            $fileName = "blog_" . $user_id . "_" . uniqid() . "." . $fileExt;
            $targetPath = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["blog_image"]["tmp_name"], $targetPath)) {
                if (!empty($imagePath) && file_exists(__DIR__ . "/../" . $imagePath)) {
                    unlink(__DIR__ . "/../" . $imagePath);
                }
                $imagePath = "uploads/blogs/" . $fileName;
            }
        }
    }

    $stmt = $conn->prepare(
        "UPDATE blogPost
         SET title = ?, content = ?, image_path = ?, updated_at = NOW()
         WHERE id = ? AND user_id = ?"
    );

    $stmt->bind_param(
        "sssii",
        $title,
        $content,
        $imagePath,
        $blog_id,
        $user_id
    );

    $stmt->execute();

    header("Location: ../view_blog.php?id=" . $blog_id);
    exit();
}

?>