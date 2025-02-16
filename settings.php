<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    die(json_encode(["success" => false, "error" => "Unauthorized access."]));
}

require_once 'database.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"]; // Use the user_id from the session
    $display_name = $_POST["display_name"];
    $description = $_POST["description"];
    $profile_picture = $_FILES["profile_picture"]["name"];

    // Update display name and description
    $stmt = $conn->prepare("UPDATE users SET display_name = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $display_name, $description, $user_id);
    $stmt->execute();

    // Update profile picture if a new one is uploaded
    if (!empty($profile_picture)) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["profile_picture"]["name"]);
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->bind_param("si", $profile_picture, $user_id);
            $stmt->execute();
        }
    }

    $response["success"] = true;
    $response["display_name"] = $display_name;
}

$conn->close();

// Return JSON response
header("Content-Type: application/json");
echo json_encode($response);
?>