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
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(217deg, #80cdfb, rgba(255, 0, 0, 0) 70.71%),
        linear-gradient(127deg, #ffaad7, rgba(0, 255, 0, 0) 70.71%),
        linear-gradient(336deg, #ffa7b4, rgba(0, 0, 255, 0) 70.71%);
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    padding: 20px;
}

.chat-container {
    width: 100%;
    max-width: 600px;

}

.chat-box {
    height: 400px;
    background: #fff;
    border-radius: 8px;
    overflow-y: auto;
    padding: 10px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.message {
    margin-bottom: 10px;
    border-radius: 10px;
    padding: 10px;
    max-width: 80%;
}

.my-message {
    background-color: #015769;
    margin-left: auto;
    color: white;
}

.friend-message {
    background-color: black;
    color: white;
}

/* Custom scrollbar */
.chat-box::-webkit-scrollbar {
    width: 12px;
}

.chat-box::-webkit-scrollbar-track {
    background: #f1f1f1;
}

.chat-box::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 6px;
}

.chat-box::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.input-group,
.logout-button {
    width: 100%;
    max-width: 600px;
    margin-top: 10px;
}

.input-group {
    display: flex;
    align-items: center;
    background-color: #f5f5f5;
    border-radius: 10px;
    border: 1px solid #0056b3;
    padding: 0 10px;
}

#chat-input {
    flex: 1;
    border: none;
    /* background-color: transparent; */
    margin: 10px;
    height: 30px;
    cursor: text;
}

.send-button,
.logout-button {
    background-color: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    outline: none;
}

.send-button:hover,
.logout-button:hover {
    background-color: #0056b3;
}

h2 {
    color: white;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

@media (max-width: 768px) {

    .chat-container,
    .input-group,
    .logout-button {
        width: 90%;
    }
}
</style>

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
                <img src="<?php echo htmlspecialchars($message['image_path']); ?>" alt="Image"
                    style="max-width:200px; border-radius: 20px;">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="input-group">
            <div id="chat-input" contenteditable="true" placeholder="Nhập tin nhắn của bạn"></div>
            <button id="send-message" class="send-button"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>
        </div>
        <input type="file" id="image" accept="image/*">
        <form method="post" action="logout.php">
            <button type="submit" name="logout" class="logout-button">Đăng xuất</button>
        </form>
    </div>

    <script>
    $(document).ready(function() {
        $('#chat-input').focus();
        var imageData = null;
        var lastMessageId = 0; // ID của tin nhắn cuối cùng được lấy
        var currentUsername =
            '<?php echo addslashes($_SESSION["username"]); ?>'; // Lấy tên người dùng từ PHP session

        // Xử lý sự kiện kéo và thả, dán hình ảnh, và chọn tệp
        setupDragAndDrop();
        setupPasteImage();
        setupFileSelect();

        // Gửi tin nhắn khi nhấn enter hoặc nhấp vào nút gửi
        setupMessageSending();

        // Lấy tin nhắn mới mỗi 5 giây
        setInterval(fetchNewMessages, 5000);

        function setupDragAndDrop() {
            $('#chat-input, .chat-box').on('dragover', function(event) {
                event.preventDefault();
            }).on('drop', function(event) {
                event.preventDefault();
                handleImageDrop(event.originalEvent.dataTransfer.files);
            });
        }

        function setupPasteImage() {
            $('#chat-input').on('paste', function(event) {
                var items = (event.clipboardData || event.originalEvent.clipboardData).items;
                for (var index in items) {
                    var item = items[index];
                    if (item.kind === 'file' && item.type.startsWith('image/')) {
                        handleImage(item.getAsFile());
                    }
                }
            });
        }

        function setupFileSelect() {
            $('#image').on('change', function() {
                if (this.files && this.files[0]) {
                    handleImage(this.files[0]);
                }
            });
        }

        function setupMessageSending() {
            $('#send-message').click(sendMessage);
            $('#chat-input').keypress(function(e) {
                if (e.which == 13 && !e.shiftKey) {
                    e.preventDefault();
                    sendMessage();
                }
            });
        }



        function handleImage(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                imageData = e.target.result; // Chuyển file hình ảnh thành base64
            };
            reader.readAsDataURL(file);
        }

        function sendMessage() {
            var messageContent = $('#chat-input').text().trim();
            if (messageContent.length === 0 && !imageData) {
                return; // Không gửi tin nhắn nếu không có nội dung hoặc hình ảnh
            }

            var formData = new FormData();
            formData.append('message', messageContent);
            if (imageData) {
                formData.append('imageData', imageData);
                imageData = null; // Reset imageData sau khi gửi
            }

            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function() {
                    $('#chat-input').html(''); // Xóa nội dung nhập vào sau khi gửi
                    fetchNewMessages(); // Gọi tin nhắn mới ngay sau khi gửi thành công
                },
                error: function() {
                    alert('Có lỗi xảy ra khi gửi tin nhắn.');
                }
            });
        }



        function fetchNewMessages() {
            $.ajax({
                url: 'fetch_new_messages.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    lastMessageId: lastMessageId
                },
                success: updateChatBox,
                error: function(xhr, status, error) {
                    console.error("Error fetching new messages:", status, error);
                }
            });
        }

        function updateChatBox(messages) {
            if (!messages || !messages.length) return;

            messages.forEach(function(message) {
                if (message.sender_username && message.id) {
                    var isMyMessage = message.sender_username.trim().toLowerCase() === currentUsername
                        .trim().toLowerCase();
                    var messageClass = isMyMessage ? 'my-message' : 'friend-message';
                    var messageSender = isMyMessage ? 'Me' : message.sender_username;
                    var escapedMessage = $('<div>').text(message.message).html();
                    var messageElement = $('<div>').addClass('message ' + messageClass).html(
                        `<strong>${messageSender}:</strong> ${escapedMessage}`
                    );
                    $('#chat-box').append(messageElement);
                    lastMessageId = Math.max(lastMessageId, message.id);
                }
            });


            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);


        }
    });
    </script>
</body>

</html>