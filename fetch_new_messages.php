<?php
session_start();

header('Content-Type: application/json'); // Đặt kiểu nội dung trả về là JSON

if (isset($_SESSION['username'])) {
    // Establish a new database connection
    $conn = new mysqli("localhost", "root", "", "messaging_app");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Lấy timestamp từ yêu cầu, mặc định là hiện tại nếu không có
    $lastFetched = isset($_GET['lastFetched']) ? (int) $_GET['lastFetched'] : time();

    // Đảm bảo timestamp là số nguyên hợp lệ và không lớn hơn thời gian hiện tại
    $lastFetched = $lastFetched > time() ? time() : $lastFetched; // dieu kien 3 ngoi

    // Chuyển timestamp sang định dạng DateTime để sử dụng trong truy vấn SQL
    $lastFetchedDateTime = date('Y-m-d H:i:s', $lastFetched);

    // Fetch messages sent after the last fetched time
    $stmt = $conn->prepare("SELECT * FROM messages WHERE sent_at > ? ORDER BY sent_at ASC");
    $stmt->bind_param("s", $lastFetchedDateTime);
    $stmt->execute();
    $result = $stmt->get_result();
    $messages = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = array(
                'id' => $row['id'],
                'sender_username' => htmlspecialchars($row['sender_username']),
                'message' => htmlspecialchars($row['message']),
                'sent_at' => $row['sent_at']
            );
        }
    }

    echo json_encode($messages); // Trả về tin nhắn dưới dạng JSON
    $conn->close();
} else {
    echo json_encode(array('error' => 'User not logged in.'));
}
?>