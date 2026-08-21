<?php

session_start();

require "php/db.php";

$sql = "SELECT blogPost.*, user.username
        FROM blogPost
        JOIN user ON blogPost.user_id = user.id
        ORDER BY blogPost.created_at DESC";

$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Blog App</title>

    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<header>

    <h1>My Blog</h1>

    <nav>

        <a href="index.php">Home</a>

        <?php if (isset($_SESSION["user_id"])): ?>

            <span>
                Welcome,
                <?php echo htmlspecialchars($_SESSION["username"]); ?>
            </span>
            <a href="create_blog.php">Create Blog</a>

            <a href="php/logout.php">Logout</a>

        <?php else: ?>

            <a href="login.html">Login</a>

            <a href="register.html">Register</a>

        <?php endif; ?>

    </nav>

</header>


<main>

    <h2>Latest Blogs</h2>

    <?php if ($result && $result->num_rows > 0): ?>

        <?php while ($blog = $result->fetch_assoc()): ?>

            <article class="blog-card">

                <h3>
                    <?php echo htmlspecialchars($blog["title"]); ?>
                </h3>

                <p class="author">

                    By
                    <?php echo htmlspecialchars($blog["username"]); ?>

                    |

                    <?php echo $blog["created_at"]; ?>

                </p>

                <p>

                    <?php
                    echo htmlspecialchars(
                        substr($blog["content"], 0, 200)
                    );
                    ?>

                    ...

                </p>

            </article>

        <?php endwhile; ?>

    <?php else: ?>

        <p>No blogs available yet.</p>

    <?php endif; ?>

</main>

</body>

</html>