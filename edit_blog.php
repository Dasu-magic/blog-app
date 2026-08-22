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
    <meta name="theme-color" content="#FFCC00">
    <title>Edit Blog - LetsBlog</title>
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
    </nav>
  </div>
</header>

<main class="auth-shell">
    <div class="form-card">
        <h2>Edit your post</h2>
        <form action="php/edit_blog.php" method="POST" enctype="multipart/form-data">

            <input
                type="hidden"
                name="id"
                value="<?php echo $blog["id"]; ?>"
            >

            <div class="form-row">
                <label>Blog Title</label>
                <input
                    type="text"
                    name="title"
                    value="<?php echo htmlspecialchars($blog["title"]); ?>"
                    required
                >
            </div>

            <div class="form-row">
                <label>Blog Content</label>
                <textarea
                    name="content"
                    rows="10"
                    required
                ><?php echo htmlspecialchars($blog["content"]); ?></textarea>
            </div>

            <?php if (!empty($blog["image_path"])): ?>
                <div class="form-row">
                    <label>Current Blog Image</label>
                    <img src="<?php echo htmlspecialchars($blog["image_path"]); ?>" alt="Blog image preview" class="blog-preview-image">
                </div>
            <?php endif; ?>

            <div class="form-row">
                <label>Replace blog image</label>
                <input type="file" name="blog_image" accept="image/png,image/jpeg,image/jpg,image/gif,image/webp">
            </div>

            <button type="submit">Update Blog</button>
        </form>
    </div>
</main>

</body>
</html>