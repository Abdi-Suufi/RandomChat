<?php
session_start();

require_once 'database.php';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST["message"];
    $user_id = $_SESSION["user_id"]; // Use the user_id from the session

    if (!empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (user_id, message) VALUES (?, ?)");
        $stmt->bind_param("is", $user_id, $message);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch all messages with user details
$sql = "SELECT messages.message, messages.created_at, users.username, users.display_name, users.profile_picture 
        FROM messages 
        JOIN users ON messages.user_id = users.id 
        ORDER BY messages.created_at DESC";
$result = $conn->query($sql);

$messages = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $messages[] = $row;
    }
}

$conn->close();

// Return messages as JSON
header("Content-Type: application/json");
echo json_encode($messages);
?>