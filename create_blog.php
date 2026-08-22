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
    <meta name="theme-color" content="#FFCC00">
    <title>Create Blog - LetsBlog</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="js/script.js" defer></script>
</head>

<body>

<header>
  <div class="container header-inner">
    <div class="brand">
      <h1>LetsBlog</h1>
    </div>

    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="php/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="auth-shell">
    <div class="form-card compose-card">
        <div class="compose-header">
            <div>
                <span class="eyebrow">New post</span>
                <h2>Write your next story</h2>
            </div>
        </div>
        <form action="php/create_blog.php" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <label>Blog Title</label>
                <input
                    type="text"
                    name="title"
                    placeholder="Enter blog title"
                    required
                >
            </div>

            <div class="form-row">
                <label>Blog Content</label>
                <textarea
                    name="content"
                    rows="10"
                    placeholder="Write your blog..."
                    required
                ></textarea>
            </div>

            <div class="form-row">
                <label>Blog Image</label>
                <input type="file" name="blog_image" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp">
            </div>

            <button type="submit">Publish Blog</button>
        </form>
    </div>
</main>

</body>
</html>