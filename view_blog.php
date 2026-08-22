<?php

session_start();
require "php/db.php";

if (!isset($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$blog_id = (int) $_GET["id"];

$conn->query("UPDATE blogPost SET views = views + 1 WHERE id = " . $blog_id);

$stmt = $conn->prepare(
    "SELECT bp.*, u.username, u.profile_image, u.id AS author_id,
            (SELECT COUNT(*) FROM blog_like bl WHERE bl.blog_id = bp.id) AS like_count,
            (SELECT COUNT(*) FROM blog_comment bc WHERE bc.blog_id = bp.id) AS comment_count
     FROM blogPost bp
     JOIN user u ON bp.user_id = u.id
     WHERE bp.id = ?"
);
$stmt->bind_param("i", $blog_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Blog not found.";
    exit();
}

$blog = $result->fetch_assoc();
$blog['like_count'] = (int) $blog['like_count'];
$blog['comment_count'] = (int) $blog['comment_count'];

$hasLiked = false;
if (isset($_SESSION["user_id"])) {
    $likeCheck = $conn->prepare("SELECT id FROM blog_like WHERE blog_id = ? AND user_id = ?");
    $likeCheck->bind_param("ii", $blog_id, $_SESSION["user_id"]);
    $likeCheck->execute();
    $hasLiked = $likeCheck->get_result()->num_rows > 0;
}

$commentStmt = $conn->prepare(
    "SELECT bc.*, u.username, u.profile_image
     FROM blog_comment bc
     JOIN user u ON bc.user_id = u.id
     WHERE bc.blog_id = ?
     ORDER BY bc.created_at DESC"
);
$commentStmt->bind_param("i", $blog_id);
$commentStmt->execute();
$comments = $commentStmt->get_result();

$authorPosts = $conn->prepare(
    "SELECT id, title, views FROM blogPost WHERE user_id = ? ORDER BY created_at DESC LIMIT 3"
);
$authorPosts->bind_param("i", $blog["author_id"]);
$authorPosts->execute();
$authorPostsResult = $authorPosts->get_result();

$authorAvatar = !empty($blog["profile_image"]) ? htmlspecialchars($blog["profile_image"]) : "default-avatar.svg";
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#FFCC00">

    <title><?php echo htmlspecialchars($blog["title"]); ?></title>

    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" type="image/x-icon">
</head>

<body>

<header>
  <div class="container header-inner">
    <div class="brand">
      <h1>LetsBlog</h1>
    </div>

    <nav class="nav-links">
      <a href="index.php">Home</a>
      <?php if (isset($_SESSION["user_id"])): ?>
        <a href="profile.php">Profile</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container blog-layout">
    <article class="single-blog">
        <div class="blog-author-row">
            <div class="profile-mini small-profile">
                <img src="<?php echo $authorAvatar; ?>" alt="Author avatar">
                <div>
                    <span>By <?php echo htmlspecialchars($blog["username"]); ?></span>
                    <small><?php echo $blog["created_at"]; ?></small>
                </div>
            </div>
            <span class="view-count">👁 <?php echo (int) $blog["views"]; ?> views</span>
        </div>

        <h2><?php echo htmlspecialchars($blog["title"]); ?></h2>

        <?php if (!empty($blog["image_path"])): ?>
            <div class="blog-feature-image-wrap">
                <img class="blog-feature-image" src="<?php echo htmlspecialchars($blog["image_path"]); ?>" alt="<?php echo htmlspecialchars($blog["title"]); ?>">
            </div>
        <?php endif; ?>

        <div class="blog-content">
            <?php echo nl2br(htmlspecialchars($blog["content"])); ?>
        </div>

        <div class="blog-actions">
            <?php if (isset($_SESSION["user_id"])): ?>
                <form action="php/like_blog.php" method="POST" class="inline-form">
                    <input type="hidden" name="blog_id" value="<?php echo $blog["id"]; ?>">
                    <button type="submit" class="btn-secondary heart-btn"><?php echo $hasLiked ? '♥ Liked' : '♡ Like'; ?> (<?php echo $blog["like_count"]; ?>)</button>
                </form>
            <?php else: ?>
                <a href="login.html" class="btn-secondary heart-btn">♡ Like (<?php echo $blog["like_count"]; ?>)</a>
            <?php endif; ?>

            <?php if (
                isset($_SESSION["user_id"]) &&
                $_SESSION["user_id"] == $blog["user_id"]
            ): ?>
                <a href="edit_blog.php?id=<?php echo $blog["id"]; ?>" class="btn-secondary">Edit Blog</a>
                <a href="php/delete_blog.php?id=<?php echo $blog["id"]; ?>" class="btn-primary" onclick="return confirm('Are you sure you want to delete this blog?');">Delete Blog</a>
            <?php endif; ?>
        </div>

        <div class="comment-section">
            <h3>Comments (<?php echo $blog["comment_count"]; ?>)</h3>

            <?php if ($comments && $comments->num_rows > 0): ?>
                <?php while ($comment = $comments->fetch_assoc()): ?>
                    <div class="comment-item">
                        <div class="comment-head">
                            <img src="<?php echo !empty($comment["profile_image"]) ? htmlspecialchars($comment["profile_image"]) : 'default-avatar.svg'; ?>" alt="Comment avatar">
                            <div>
                                <strong><?php echo htmlspecialchars($comment["username"]); ?></strong>
                                <small><?php echo $comment["created_at"]; ?></small>
                            </div>
                        </div>
                        <p><?php echo htmlspecialchars($comment["comment"]); ?></p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="empty-state inline-empty">No comments yet.</p>
            <?php endif; ?>

            <?php if (isset($_SESSION["user_id"])): ?>
                <form action="php/comment_blog.php" method="POST" class="comment-form">
                    <input type="hidden" name="blog_id" value="<?php echo $blog["id"]; ?>">
                    <textarea name="comment" rows="3" placeholder="Write a comment..." required></textarea>
                    <button type="submit">Post Comment</button>
                </form>
            <?php else: ?>
                <p class="small-text">Please <a href="login.html">log in</a> to comment.</p>
            <?php endif; ?>
        </div>
    </article>

    <aside class="sidebar">
        <div class="widget profile-widget">
            <div class="profile-mini">
                <img src="<?php echo $authorAvatar; ?>" alt="Author avatar">
                <div>
                    <h4>Author</h4>
                    <strong><?php echo htmlspecialchars($blog["username"]); ?></strong>
                </div>
            </div>
            <a href="profile.php?id=<?php echo $blog["author_id"]; ?>" class="btn-primary small-btn">View profile</a>
        </div>

        <div class="widget">
            <h4>More from this author</h4>
            <ul class="topic-list">
                <?php if ($authorPostsResult && $authorPostsResult->num_rows > 0): ?>
                    <?php while ($entry = $authorPostsResult->fetch_assoc()): ?>
                        <li><a href="view_blog.php?id=<?php echo $entry["id"]; ?>"><?php echo htmlspecialchars($entry["title"]); ?></a></li>
                    <?php endwhile; ?>
                <?php else: ?>
                    <li>No other posts yet.</li>
                <?php endif; ?>
            </ul>
        </div>
    </aside>
</main>

</body>

</html>