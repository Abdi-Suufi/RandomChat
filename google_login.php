<?php
session_start();

require_once 'database.php';

// Get the token from the request
$data = json_decode(file_get_contents("php://input"), true);
$token = $data['token'];

// Verify the token with Google
$url = "https://oauth2.googleapis.com/tokeninfo?id_token=" . $token;
$response = file_get_contents($url);
$userinfo = json_decode($response, true);

if (isset($userinfo['email'])) {
    $email = $userinfo['email'];
    $name = $userinfo['name'];

    // Check if the user already exists in your database
    $stmt = $conn->prepare("SELECT id, username, display_name, is_admin FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $username, $display_name, $is_admin);

    if ($stmt->fetch()) {
        // User exists, log them in
        $_SESSION["user_id"] = $id;
        $_SESSION["username"] = $username;
        $_SESSION["is_admin"] = $is_admin;

        echo json_encode([
            "success" => true, 
            "username" => $username, 
            "display_name" => $display_name, 
            "user_id" => $id, 
            "is_admin" => $is_admin
        ]);
    } else {
        // User does not exist, create a new account
        $username = str_replace(' ', '', strtolower($name)); // Generate a username
        $display_name = $name; // Use the full name as display name
        
        $stmt = $conn->prepare("INSERT INTO users (username, email, display_name) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $username, $email, $display_name);
        $stmt->execute();

        $user_id = $stmt->insert_id;
        $_SESSION["user_id"] = $user_id;
        $_SESSION["username"] = $username;
        $_SESSION["is_admin"] = 0; // Default to non-admin

        echo json_encode([
            "success" => true, 
            "username" => $username, 
            "display_name" => $display_name, 
            "user_id" => $user_id, 
            "is_admin" => 0
        ]);
    }

    $stmt->close();
} else {
    // Invalid token
    echo json_encode(["success" => false, "message" => "Invalid token"]);
}

$conn->close();
?>