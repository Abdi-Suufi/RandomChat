<?php
session_start();

require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT id, username, password, is_admin FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $hashed_password, $is_admin);

    if ($stmt->fetch() && password_verify($password, $hashed_password)) {
        // Login successful
        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $username;
        $_SESSION["is_admin"] = $is_admin; // Store admin status in session
        echo json_encode(["success" => true, "username" => $username, "is_admin" => $is_admin]);
    } else {
        // Login failed
        echo json_encode(["success" => false]);
    }

    $stmt->close();
}

$conn->close();
?>