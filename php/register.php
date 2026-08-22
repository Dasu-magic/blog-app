<?php
session_start();
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT);
    $profileImage = "";

    if (isset($_FILES["profile_image"]) && $_FILES["profile_image"]["error"] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . "/../uploads/profiles/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileExt = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
        $allowed = ["jpg", "jpeg", "png", "gif", "webp"];

        if (in_array($fileExt, $allowed, true)) {
            $fileName = uniqid("profile_", true) . "." . $fileExt;
            $target = $uploadDir . $fileName;

            if (move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target)) {
                $profileImage = "uploads/profiles/" . $fileName;
            }
        }
    }

    $stmt = $conn->prepare("INSERT INTO user (username, email, password, profile_image) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $name, $email, $password, $profileImage);

    if ($stmt->execute()) {
        $_SESSION["user_id"] = $conn->insert_id;
        $_SESSION["username"] = $name;
        $_SESSION["email"] = $email;
        $_SESSION["profile_image"] = $profileImage;

        header("Location: ../index.php");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}