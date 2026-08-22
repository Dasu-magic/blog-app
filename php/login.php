<?php

session_start();

require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare(
        "SELECT id, username, email, password, profile_image 
         FROM user 
         WHERE email = ?"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user["password"])) {
            $_SESSION["user_id"] = $user["id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["profile_image"] = $user["profile_image"] ?? "";

            header("Location: ../index.php");
            exit();
        }
        header("Location: ../login.html?error=" . urlencode("Invalid email or password."));

        exit();
    }
     header("Location: ../login.html?error=" . urlencode("Invalid email or password."));
    exit();
}
?>