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
    <meta name="theme-color" content="#0a0b0d">
    <title>Create Blog</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="js/script.js" defer></script>
</head>

<body>

<header>
  <div class="container header-inner">
    <div class="brand">
      <h1>Create Blog</h1>
      <div class="kicker">Write something meaningful</div>
    </div>

    <nav class="nav-links">
      <a href="index.php">Home</a>
      <a href="php/logout.php">Logout</a>
    </nav>
  </div>
</header>

<main class="auth-shell">
    <div class="form-card">
        <h2>Write your next story</h2>
        <form action="php/create_blog.php" method="POST">
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

            <button type="submit">Publish Blog</button>
        </form>
    </div>
</main>

<footer>
  <div class="container">
    <p class="small">&copy; 2026 My Blog. Write long-form. Stay bold.</p>
  </div>
</footer>

</body>
</html>