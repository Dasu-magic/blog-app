<?php

session_start();
require __DIR__ . "/php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !isset($_POST["id"])) {
    header("Location: ../index.php");
    exit();
}

$blog_id = $_POST["id"];
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