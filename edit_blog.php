<?php

session_start();
require "php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET["id"];
$user_id = $_SESSION["user_id"];

$stmt = $conn->prepare(
    "SELECT * FROM blogPost
     WHERE id = ? AND user_id = ?"
);

$stmt->bind_param("ii", $blog_id, $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "You are not allowed to edit this blog.";
    exit();
}

$blog = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Edit Blog</h1>

    <nav>
        <a href="index.php">Home</a>
    </nav>
</header>

<main>

    <form action="php/edit_blog.php" method="POST">

        <input
            type="hidden"
            name="id"
            value="<?php echo $blog["id"]; ?>"
        >

        <label>Blog Title</label>

        <input
            type="text"
            name="title"
            value="<?php echo htmlspecialchars($blog["title"]); ?>"
            required
        >

        <label>Blog Content</label>

        <textarea
            name="content"
            rows="10"
            required
        ><?php echo htmlspecialchars($blog["content"]); ?></textarea>

        <button type="submit">
            Update Blog
        </button>

    </form>

</main>

</body>
</html>