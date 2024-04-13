<?php
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['username'])) {
    $conn = new mysqli("localhost", "root", "", "messaging_app");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $lastMessageId = isset($_GET['lastMessageId']) ? (int) $_GET['lastMessageId'] : 0;

    $query = "SELECT m.*, COALESCE(u.username, 'Unknown') AS sender_username FROM messages m LEFT JOIN users u ON m.sender_username = u.username WHERE m.id > ? ORDER BY m.sent_at ASC";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $lastMessageId);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        if ($row['sender_username'] === $_SESSION['username']) {
            // Nếu người gửi là người dùng hiện tại
            $class = 'my-message';
        } else {
            // Nếu không phải, hiển thị tên người gửi hoặc 'Unknown' nếu không có
            $class = 'friend-message';
            $row['sender_username'] = $row['sender_username'] ?? 'Unknown';
        }
        $messages[] = [
            'id' => $row['id'],
            'sender_username' => htmlspecialchars($row['sender_username']),
            'message' => htmlspecialchars($row['message']),
            'sent_at' => $row['sent_at']
        ];
    }

    echo json_encode($messages);
    $conn->close();

} else {
    echo json_encode(['error' => 'User not logged in.']);
}
?>