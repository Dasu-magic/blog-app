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
    <meta name="theme-color" content="#0a0b0d">

    <title><?php echo htmlspecialchars($blog["title"]); ?></title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body>

<header>
  <div class="container header-inner">
    <div class="brand">
      <h1>My Blog</h1>
      <div class="kicker">Black & White Edition</div>
    </div>

    <nav class="nav-links">
      <a href="index.php">Home</a>
    </nav>
  </div>
</header>

<main class="container">
    <article class="single-blog">
        <p class="meta-row">
            <span>By <?php echo htmlspecialchars($blog["username"]); ?></span>
            <span>•</span>
            <span><?php echo $blog["created_at"]; ?></span>
        </p>

        <h2><?php echo htmlspecialchars($blog["title"]); ?></h2>

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

            <div class="blog-actions">
                <a href="edit_blog.php?id=<?php echo $blog["id"]; ?>" class="btn-secondary">
                    Edit Blog
                </a>

                <a href="php/delete_blog.php?id=<?php echo $blog["id"]; ?>"
                   class="btn-primary"
                   onclick="return confirm('Are you sure you want to delete this blog?');">
                    Delete Blog
                </a>
            </div>

        <?php endif; ?>
    </article>
</main>

<footer>
  <div class="container">
    <p class="small">&copy; 2026 My Blog. Black & White theme.</p>
  </div>
</footer>

</body>

</html>