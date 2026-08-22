<?php
session_start();
require "php/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

$userId = isset($_GET["id"]) ? (int) $_GET["id"] : (int) $_SESSION["user_id"];

$stmt = $conn->prepare("SELECT id, username, email, profile_image FROM user WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if (!$user) {
    header("Location: index.php");
    exit();
}

$blogsStmt = $conn->prepare(
    "SELECT bp.*, 
            (SELECT COUNT(*) FROM blog_like bl WHERE bl.blog_id = bp.id) AS like_count,
            (SELECT COUNT(*) FROM blog_comment bc WHERE bc.blog_id = bp.id) AS comment_count
     FROM blogPost bp
     WHERE bp.user_id = ?
     ORDER BY bp.created_at DESC"
);
$blogsStmt->bind_param("i", $userId);
$blogsStmt->execute();
$blogsResult = $blogsStmt->get_result();

$totalViews = 0;
$totalLikes = 0;

if ($blogsResult && $blogsResult->num_rows > 0) {
    while ($blog = $blogsResult->fetch_assoc()) {
        $totalViews += (int) $blog["views"];
        $totalLikes += (int) $blog["like_count"];
    }
    $blogsResult->data_seek(0);
}

$defaultAvatar = "default-avatar.svg";
$avatarPath = !empty($user["profile_image"]) ? htmlspecialchars($user["profile_image"]) : $defaultAvatar;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user["username"]); ?> | LetsBlog</title>
    <meta name="theme-color" content="#FFCC00">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Poppins:wght@500;600;700;800&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container header-inner">
            <div class="brand">
                <h1>LetsBlog</h1>
            </div>
            <nav class="nav-links">
                <a href="index.php">Home</a>
                <a href="create_blog.php">Create</a>
                <a href="php/logout.php">Logout</a>
            </nav>
        </div>
    </header>

    <main class="container profile-page">
        <aside class="sidebar">
            <div class="widget profile-card compact-profile-card">
                <div class="profile-identity-row">
                    <img class="profile-avatar" src="<?php echo $avatarPath; ?>" alt="Profile picture">
                    <div class="profile-identity-copy">
                        <span class="profile-label">Profile</span>
                        <h2><?php echo htmlspecialchars($user["username"]); ?></h2>
                    </div>
                </div>
                <p class="small"><?php echo htmlspecialchars($user["email"]); ?></p>

                <div class="profile-stats">
                    <div>
                        <strong><?php echo $blogsResult->num_rows; ?></strong>
                        <span>Blogs</span>
                    </div>
                    <div>
                        <strong><?php echo $totalViews; ?></strong>
                        <span>Views</span>
                    </div>
                    <div>
                        <strong><?php echo $totalLikes; ?></strong>
                        <span>Hearts</span>
                    </div>
                </div>

                <?php if ((int) $_SESSION["user_id"] === (int) $userId): ?>
                    <form action="php/upload_profile_image.php" method="POST" enctype="multipart/form-data" class="profile-upload-form">
                        <input type="file" name="profile_image" accept="image/*" required>
                        <button type="submit">Upload picture</button>
                    </form>
                <?php endif; ?>
            </div>
        </aside>

        <div class="blog-list">
            <div class="section-heading">
                <h2><?php echo ((int) $_SESSION["user_id"] === (int) $userId) ? "My blogs" : htmlspecialchars($user["username"]) . "'s blogs"; ?></h2>
            </div>

            <?php if ($blogsResult && $blogsResult->num_rows > 0): ?>
                <?php while ($blog = $blogsResult->fetch_assoc()): ?>
                    <article class="blog-card">
                        <div class="meta-row">
                            <span>Views <?php echo (int) $blog["views"]; ?></span>
                            <span>•</span>
                            <span>♥ <?php echo (int) $blog["like_count"]; ?></span>
                            <span>•</span>
                            <span>💬 <?php echo (int) $blog["comment_count"]; ?></span>
                        </div>
                        <h3><?php echo htmlspecialchars($blog["title"]); ?></h3>
                        <p><?php echo htmlspecialchars(substr($blog["content"], 0, 180)); ?>...</p>
                        <div class="profile-blog-actions">
                            <a class="read-more" href="view_blog.php?id=<?php echo $blog["id"]; ?>">Open</a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">No blogs published yet.</div>
            <?php endif; ?>
        </div>
    </main>
</body>
</html>
