<?php
$host = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "blog_app";

$conn = new mysqli($host, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}