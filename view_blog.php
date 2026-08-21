<?php

session_start();

require "php/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = $_GET["id"];

$stmt = $conn->prepare(
    "SELECT blogPost.*, user.username
     FROM blogPost
     JOIN user ON blogPost.user_id = user.id
     WHERE blogPost.id = ?"
);

$stmt->bind_param("i", $blog_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Blog not found.";
    exit();
}

$blog = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?php echo htmlspecialchars($blog["title"]); ?></title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>

    <h1>My Blog</h1>

    <nav>
        <a href="index.php">Home</a>
    </nav>

</header>

<main>

    <article class="single-blog">

        <h2>
            <?php echo htmlspecialchars($blog["title"]); ?>
        </h2>

        <p class="author">
            By <?php echo htmlspecialchars($blog["username"]); ?>
            |
            <?php echo $blog["created_at"]; ?>
        </p>

        <div class="blog-content">

            <?php
            echo nl2br(
                htmlspecialchars($blog["content"])
            );
            ?>

        </div>

        <?php if (
            isset($_SESSION["user_id"]) &&
            $_SESSION["user_id"] == $blog["user_id"]
        ): ?>

            <a href="edit_blog.php?id=<?php echo $blog["id"]; ?>">
                Edit Blog
            </a>

            <a href="php/delete_blog.php?id=<?php echo $blog["id"]; ?>"
               onclick="return confirm('Are you sure you want to delete this blog?');">
                Delete Blog
            </a>

        <?php endif; ?>

    </article>

</main>

</body>

</html>