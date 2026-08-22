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
    $imagePath = "";

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
                $imagePath = "uploads/blogs/" . $fileName;
            }
        }
    }

    $stmt = $conn->prepare(
        "INSERT INTO blogPost (user_id, title, content, image_path)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->bind_param(
        "isss",
        $user_id,
        $title,
        $content,
        $imagePath
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