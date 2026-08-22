<?php

session_start();

require "php/db.php";

$search = isset($_GET["search"])
    ? trim($_GET["search"])
    : "";

if ($search !== "") {

    $stmt = $conn->prepare(
        "SELECT blogPost.*, user.username
         FROM blogPost
         JOIN user ON blogPost.user_id = user.id
         WHERE blogPost.title LIKE ?
         OR blogPost.content LIKE ?
         ORDER BY blogPost.created_at DESC"
    );

    $keyword = "%" . $search . "%";

    $stmt->bind_param(
        "ss",
        $keyword,
        $keyword
    );

    $stmt->execute();

    $result = $stmt->get_result();

} else {

    $sql = "SELECT blogPost.*, user.username
            FROM blogPost
            JOIN user ON blogPost.user_id = user.id
            ORDER BY blogPost.created_at DESC";

    $result = $conn->query($sql);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFCC00">

    <title>LetsBlog</title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body>

<header>
  <div class="container header-inner">
<div class="brand">
  <h1>LetsBlog</h1>
</div>

<div class="nav-right">
<form class="search-form" method="GET" action="index.php">
  <span class="search-icon" aria-hidden="true">⌕</span>
  <input type="text" name="search" placeholder="Search stories..." value="<?php echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : ""; ?>">
  <button type="submit" aria-label="Search">Search</button>
</form>

<nav class="nav-links">
  <?php if (isset($_SESSION["user_id"])): ?>
      <a href="index.php">Home</a>
      <a href="create_blog.php" aria-label="Write a blog post" title="Write">✍️ Write</a>
      <a href="php/logout.php">Logout</a>
      <span class="profile-inline">
          <span class="welcome-message"><?php echo htmlspecialchars($_SESSION["username"] ?? "Reader"); ?></span>
          <a href="profile.php" class="profile-nav-link" aria-label="Go to profile" title="Profile">
              <img src="<?php echo !empty($_SESSION["profile_image"]) ? htmlspecialchars($_SESSION["profile_image"]) : "default-avatar.svg"; ?>" alt="Profile picture">
          </a>
      </span>
  <?php else: ?>
      <a href="index.php">Home</a>
      <a href="login.html">Login</a>
      <a href="register.html">Register</a>
      <?php endif; ?>
</nav>
</div>
  </div>
</header>

<main class="container">
<section class="hero">
    <div class="hero-copy">
        <span class="eyebrow">Real stories</span>
        <h2>Life, laughter, and the moments we never forget.</h2>
        <p>
            Share the little incidents, funny everyday mishaps, and personal stories that make life feel real. From awkward moments to unforgettable memories, LetsBlog is where people tell it like it happened.
        </p>
        <div class="hero-actions">
            <a href="register.html" class="btn-primary">Join the community</a>
            <a href="#latest" class="btn-secondary">Read latest stories</a>
        </div>
    </div>

    <div class="hero-panel">
        <div>
            <div class="featured-label">Featured story</div>
            <h3>The small moments that become the best memories.</h3>
        </div>
        <p>
            People come here to share personal experiences, hilarious day-to-day situations, and honest stories that make others say, “That happened to me too.”
        </p>
        <div class="stats-grid">
            <div class="stat">
                <strong>250+</strong>
                <span>Stories</span>
            </div>
            <div class="stat">
                <strong>18k</strong>
                <span>Readers</span>
            </div>
            <div class="stat">
                <strong>4.9</strong>
                <span>Laughs</span>
            </div>
        </div>
    </div>
</section>

<section class="content-grid" id="latest">
    <div class="blog-list">
        <div class="section-heading">
            <h2>Latest stories</h2>
            <span class="small">Fresh memories</span>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($blog = $result->fetch_assoc()): ?>
                <article class="blog-card">
                    <?php if (!empty($blog["image_path"])): ?>
                        <img class="blog-thumb" src="<?php echo htmlspecialchars($blog["image_path"]); ?>" alt="<?php echo htmlspecialchars($blog["title"]); ?>">
                    <?php endif; ?>
                    <div class="meta-row">
                        <span><?php echo htmlspecialchars($blog["username"]); ?></span>
                        <span>•</span>
                        <span><?php echo $blog["created_at"]; ?></span>
                    </div>
                    <h3><?php echo htmlspecialchars($blog["title"]); ?></h3>
                    <p>
                        <?php
                        echo htmlspecialchars(
                            substr($blog["content"], 0, 180)
                        );
                        ?>...
                    </p>
                    <a class="read-more" href="view_blog.php?id=<?php echo $blog["id"]; ?>">Read More</a>
                </article>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="empty-state">No blogs available yet.</div>
        <?php endif; ?>
    </div>

    <aside class="sidebar">
        <div class="widget">
            <h4>Popular stories</h4>
            <ul class="topic-list">
                <li>Funny day-to-day moments</li>
                <li>Personal incidents</li>
                <li>Life lessons</li>
                <li>Everyday adventures</li>
            </ul>
        </div>

        <div class="widget">
            <h4>About</h4>
            <p class="small">A space for honest moments, funny memories, and the little stories that make everyday life interesting.</p>
        </div>
    </aside>
</section>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <span>Designed by Dasuni Chamathka</span>
    </div>
</footer>

</body>

</html>