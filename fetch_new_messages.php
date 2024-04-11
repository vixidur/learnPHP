<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['username'])) {
    $conn = new mysqli("localhost", "root", "", "messaging_app");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Lấy ID của tin nhắn cuối cùng được client nhận
    $lastMessageId = isset($_GET['lastMessageId']) ? (int) $_GET['lastMessageId'] : 0;

    $query = "SELECT * FROM messages WHERE id > ? ORDER BY sent_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lastMessageId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        $messages[] = array(
            'id' => $row['id'],
            'sender_username' => htmlspecialchars($row['sender_username']),
            'message' => htmlspecialchars($row['message']),
            'sent_at' => $row['sent_at']
        );
    }

    echo json_encode($messages);
    $conn->close();
} else {
    echo json_encode(['error' => 'User not logged in.']);
}
?>