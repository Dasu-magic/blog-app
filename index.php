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
    <meta name="theme-color" content="#0a0b0d">

    <title>Blog App</title>

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

<div class="nav-right">
  <form class="search-form" method="GET" action="index.php">
    <input type="text" name="search" placeholder="Search blogs..." value="<?php echo isset($_GET["search"]) ? htmlspecialchars($_GET["search"]) : ""; ?>">
    <button type="submit">Search</button>
  </form>

  <nav class="nav-links">
    <a href="index.php">Home</a>
    <?php if (isset($_SESSION["user_id"])): ?>
        <span class="welcome-pill small">Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?></span>
        <a href="create_blog.php">Create</a>
        <a href="php/logout.php">Logout</a>
    <?php else: ?>
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
        <span class="eyebrow">Fresh ideas</span>
        <h2>Stories that move people.</h2>
        <p>
            Discover thoughtful writing, bold opinions, and creative insight from people who love to express ideas in a clean, modern space.
        </p>
        <div class="hero-actions">
            <a href="register.html" class="btn-primary">Join the community</a>
            <a href="#latest" class="btn-secondary">Read latest posts</a>
        </div>
    </div>

    <div class="hero-panel">
        <div>
            <div class="featured-label">Featured Story</div>
            <h3>Why thoughtful design still commands attention.</h3>
        </div>
        <p>
            Simplicity brings clarity. A disciplined visual system makes every story feel more intentional and memorable.
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
                <span>Rating</span>
            </div>
        </div>
    </div>
</section>

<section class="dashboard-shell" aria-label="Live dashboard overview">
    <div class="dashboard-header">
        <h3>Live dashboard</h3>
        <div class="live-indicator">
            <span class="live-dot"></span>
            <span id="live-clock">Updated just now</span>
        </div>
    </div>

    <div class="dashboard-grid">
        <article class="metric-card cyan">
            <span class="label">Active readers</span>
            <strong data-target="1864">0</strong>
            <span>+12.4% this week</span>
        </article>
        <article class="metric-card teal">
            <span class="label">Published</span>
            <strong data-target="72">0</strong>
            <span>12 today</span>
        </article>
        <article class="metric-card amber">
            <span class="label">Engagement</span>
            <strong data-target="84">0</strong>
            <span>Avg. 4.7 min read</span>
        </article>
        <article class="metric-card rose">
            <span class="label">Followers</span>
            <strong data-target="9200">0</strong>
            <span>+430 this month</span>
        </article>
    </div>
</section>

<section class="content-grid" id="latest">
    <div class="blog-list">
        <div class="section-heading">
            <h2>Latest Blogs</h2>
            <span class="small">Fresh writing</span>
        </div>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($blog = $result->fetch_assoc()): ?>
                <article class="blog-card">
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
            <h4>Popular Topics</h4>
            <ul class="topic-list">
                <li>Design</li>
                <li>Writing</li>
                <li>Creativity</li>
                <li>Startup life</li>
            </ul>
        </div>

        <div class="widget">
            <h4>About</h4>
            <p class="small">A minimal place for ideas, stories, and voices worth reading.</p>
        </div>
    </aside>
</section>
</main>

<footer>
  <div class="container">
<p class="small">&copy; 2026 My Blog. Built with ♥ — minimalist black & white theme.</p>
  </div>
</footer>

</body>

</html>