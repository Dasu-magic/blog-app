<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Blog</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>
    <h1>Create Blog</h1>

    <nav>
        <a href="index.php">Home</a>
        <a href="php/logout.php">Logout</a>
    </nav>
</header>

<main>

    <form action="php/create_blog.php" method="POST">

        <label>Blog Title</label>

        <input
            type="text"
            name="title"
            placeholder="Enter blog title"
            required
        >

        <label>Blog Content</label>

        <textarea
            name="content"
            rows="10"
            placeholder="Write your blog..."
            required
        ></textarea>

        <button type="submit">Publish Blog</button>

    </form>

</main>

</body>
</html>