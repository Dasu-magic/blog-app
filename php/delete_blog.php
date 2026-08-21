<?php

session_start();
require "db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: ../index.php");
    exit();
}

$blog_id = $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "DELETE FROM blogPost
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();

$stmt->close();
$conn->close();

header("Location: ../index.php");
exit();

?>