<?php
require_once 'dbconnect.php';

if (!function_exists('fetchMessages')) {
    function fetchMessages($conn)
    {
        $sql = "SELECT * FROM messages ORDER BY sent_at ASC";
        $result = $conn->query($sql);

        $messages = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $messages[] = $row;
            }
        } else {
            echo "0 results";
        }
        return $messages;
    }
}
function fetchMessages($conn)
{
    // Cập nhật câu truy vấn để sử dụng cột `message`
    $sql = "SELECT id, sender_username, message, image_path, sent_at FROM messages ORDER BY sent_at DESC";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $messages;
}


?>