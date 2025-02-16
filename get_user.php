<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    die(json_encode(["success" => false, "error" => "Unauthorized access."]));
}

require_once 'database.php';

$user_id = $_GET["user_id"];
$response = ["success" => false];

$stmt = $conn->prepare("SELECT username, display_name, profile_picture, description FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $display_name, $profile_picture, $description);
$stmt->fetch();

if ($username) {
    $response["success"] = true;
    $response["username"] = $username;
    $response["display_name"] = $display_name;
    $response["profile_picture"] = $profile_picture;
    $response["description"] = $description;
}

$stmt->close();
$conn->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($response);
?>