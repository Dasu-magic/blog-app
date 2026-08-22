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
    <meta name="theme-color" content="#0a0b0d">
    <title>Edit Blog</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <script src="js/script.js" defer></script>
</head>

<body>

<header>
  <div class="container header-inner">
    <div class="brand">
      <h1>Edit Blog</h1>
      <div class="kicker">Refine your post</div>
    </div>

    <nav class="nav-links">
      <a href="index.php">Home</a>
    </nav>
  </div>
</header>

<main class="auth-shell">
    <div class="form-card">
        <h2>Edit your post</h2>
        <form action="php/edit_blog.php" method="POST">

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

            <button type="submit">Update Blog</button>
        </form>
    </div>
</main>

<footer>
  <div class="container">
    <p class="small">&copy; 2026 My Blog. Edit with care.</p>
  </div>
</footer>

</body>
</html>