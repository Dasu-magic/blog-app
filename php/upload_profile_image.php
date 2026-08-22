<?php
session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if (!isset($_FILES["profile_image"]) || $_FILES["profile_image"]["error"] !== UPLOAD_ERR_OK) {
    header("Location: ../profile.php");
    exit();
}

$uploadDir = __DIR__ . "/../uploads/profiles/";
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

$fileExt = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
$allowed = ["png", "jpg", "jpeg", "gif", "webp"];

if (!in_array($fileExt, $allowed, true) || getimagesize($_FILES["profile_image"]["tmp_name"]) === false) {
}

$fileName = "profile_" . $_SESSION["user_id"] . "_" . uniqid() . "." . $fileExt;
$target = $uploadDir . $fileName;

if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target)) {
    header("Location: ../profile.php?error=" . urlencode("Upload failed."));
    exit();
}

$relativePath = "uploads/profiles/" . $fileName;
$stmt = $conn->prepare("UPDATE user SET profile_image = ? WHERE id = ?");
$stmt->bind_param("si", $relativePath, $_SESSION["user_id"]);
$stmt->execute();

$_SESSION["profile_image"] = $relativePath;
header("Location: ../profile.php");
exit();
