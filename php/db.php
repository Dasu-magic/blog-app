<?php
$host = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "blog_app";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

$createTables = [
    "CREATE TABLE IF NOT EXISTS user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(100) NOT NULL,
        email VARCHAR(150) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        profile_image VARCHAR(255) NOT NULL DEFAULT '',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    "CREATE TABLE IF NOT EXISTS blogPost (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        content TEXT NOT NULL,
        image_path VARCHAR(255) NOT NULL DEFAULT '',
        views INT NOT NULL DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS blog_like (
        id INT AUTO_INCREMENT PRIMARY KEY,
        blog_id INT NOT NULL,
        user_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY unique_like (blog_id, user_id),
        FOREIGN KEY (blog_id) REFERENCES blogPost(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
    )",
    "CREATE TABLE IF NOT EXISTS blog_comment (
        id INT AUTO_INCREMENT PRIMARY KEY,
        blog_id INT NOT NULL,
        user_id INT NOT NULL,
        comment TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (blog_id) REFERENCES blogPost(id) ON DELETE CASCADE,
        FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
    )"
];

foreach ($createTables as $query) {
    $conn->query($query);
}

$checkProfileCol = $conn->query("SHOW COLUMNS FROM user LIKE 'profile_image'");
if ($checkProfileCol && $checkProfileCol->num_rows === 0) {
    $conn->query("ALTER TABLE user ADD COLUMN profile_image VARCHAR(255) NOT NULL DEFAULT ''");
}

$checkViewsCol = $conn->query("SHOW COLUMNS FROM blogPost LIKE 'views'");
if ($checkViewsCol && $checkViewsCol->num_rows === 0) {
    $conn->query("ALTER TABLE blogPost ADD COLUMN views INT NOT NULL DEFAULT 0");
}

$checkImageCol = $conn->query("SHOW COLUMNS FROM blogPost LIKE 'image_path'");
if ($checkImageCol && $checkImageCol->num_rows === 0) {
    $conn->query("ALTER TABLE blogPost ADD COLUMN image_path VARCHAR(255) NOT NULL DEFAULT ''");
}

$uploadsDir = __DIR__ . "/../uploads/profiles";
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true);
}

$blogUploadsDir = __DIR__ . "/../uploads/blogs";
if (!is_dir($blogUploadsDir)) {
    mkdir($blogUploadsDir, 0777, true);
}