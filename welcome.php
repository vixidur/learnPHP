<?php
session_start();

// Redirect the user to the login page if not logged in.
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "messaging_app");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Hàm lấy tin nhắn từ cơ sở dữ liệu
function fetchMessages($conn)
{
    $sql = "SELECT * FROM messages ORDER BY sent_at ASC"; // Sắp xếp theo thời gian gửi tin nhắn từ cũ đến mới
    $result = $conn->query($sql);

    $messages = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $messages[] = $row;
        }
    }
    return $messages;
}

$messages = fetchMessages($conn);
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat App</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body>
    <div class="chat-container">
        <h2>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> !</h2>
        <div id="chat-box" class="chat-box">
            <!-- Tin nhắn sẽ được hiển thị ở đây -->
            <?php foreach ($messages as $message): ?>
                <div
                    class="message <?php echo $message['sender_username'] === $_SESSION['username'] ? 'my-message' : 'friend-message'; ?>">
                    <strong><?php echo htmlspecialchars($message['sender_username']) . ':'; ?></strong>
                    <?php echo htmlspecialchars($message['message']); ?>
                    <?php if (!empty($message['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Image" style="max-width:200px;">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="input-group">
            <div id="chat-input" contenteditable="true" placeholder="Nhập tin nhắn của bạn"></div>
            <button id="send-message" class="send-button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
        </div>
        <input type="file" id="image" accept="image/*">
    </div>
    <script src="./main.js"></script>
    <form method="post" action="logout.php">
        <button type="submit" name="logout" class="logout-button">Đăng xuất</button>
    </form>
</body>

</html>