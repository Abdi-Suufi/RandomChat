<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = password_hash($_POST["password"], PASSWORD_DEFAULT); // Hash password

    // Set display_name to username by default
    $display_name = $username;

    $stmt = $conn->prepare("INSERT INTO users (username, email, password, display_name) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $email, $password, $display_name);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Registration successful!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error: " . $stmt->error]);
    }

    $stmt->close();
}

$conn->close();
?>