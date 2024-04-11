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
    <title>Sân Chơi Giới Trẻ</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" href="./img/LOGO-DKN-VER2.png" type="image/png" sizes="16x16">
</head>
<style>
img[src="https://www.000webhost.com/static/default.000webhost.com/images/powered-by-000webhost.png"] {
    display: none;
}

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
    word-wrap: break-word;
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
    /* background-color: #f5f5f5; */
    border-radius: 10px;
    /* border: 1px solid #0056b3; */
    padding: 0 10px;
    flex-direction: row;
    /* max-height: 100px; */
}

.textarea {
    flex-grow: 1;
    margin-right: 10px;
    /* Khoảng cách giữa textarea và nút gửi */
    padding: 8px;
    border-radius: 4px;
    border: 1px solid #ccc;
    /* Bạn có thể thay đổi màu viền tùy ý */
    width: 100%;
    max-width: 600px;
}

.send-button {
    padding: 8px 16px;
    background-color: #007bff;
    /* Màu nền của nút */
    color: white;
    /* Màu chữ */
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

.send-button:hover {
    background-color: #0056b3;
    /* Màu nền khi di chuột qua */
}

#chat-input {
    resize: none;
    width: 100%;
    max-width: 600px;
    /* flex: 1; */
    border: none;
    /* background-color: transparent; */
    margin: 10px;
    max-height: 50px;
    width: 100%;
    white-space: pre-wrap;
    /* Cho phép nội dung ngắt dòng và giữ khoảng trắng */
    word-wrap: break-word;
    overflow-y: auto;
    line-height: 1.5;
    overflow-y: hidden;
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
        width: 100%;
    }
}
</style>

<body>
    <div class="chat-container">
        <h2>Xin chào, <?php echo htmlspecialchars($_SESSION['username']); ?> ! <img
                src="https://i.pinimg.com/originals/23/1f/b5/231fb5027639114dd7cf3f8f3ef9cb86.gif" width="40px"
                height="40px" style="border-radius: 50px"></h2>
        <div id="chat-box" class="chat-box">
            <!-- Tin nhắn sẽ được hiển thị ở đây -->
            <?php foreach ($messages as $message): ?>
            <div
                class="message <?php echo $message['sender_username'] === $_SESSION['username'] ? 'my-message' : 'friend-message'; ?>">
                <strong><?php echo htmlspecialchars($message['sender_username']) . ':'; ?></strong>
                <?php echo htmlspecialchars($message['message']); ?>
                <?php if (!empty($message['image_path'])): ?>
                <img src=" <?php echo htmlspecialchars($message['image_path']); ?>" alt="Image"
                    style="max-width:200px; border-radius: 20px;">
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="input-group">
            <textarea id="chat-input" class="textarea" placeholder="Nhập tin nhắn của bạn"></textarea>
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
        setInterval(fetchNewMessages, 1000);


        //kéo và thả
        function setupDragAndDrop() {
            $('#chat-input, .chat-box').on('dragover', function(event) {
                event.preventDefault();
            }).on('drop', function(event) {
                event.preventDefault();
                handleImageDrop(event.originalEvent.dataTransfer.files);
            });
        }
        // dán ảnh
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


        // chọn tệp
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
                    e.preventDefault(); // Ngăn chặn xuống dòng mới
                    sendMessage();
                }
            });
        }




        function handleImage(file) {
            var reader = new FileReader();
            reader.onload = function(e) {
                imageData = e.target.result; // Chuyển file hình ảnh thành base64
                // Hiển thị ảnh tạm thời trong chat-box
                var tempImage = $('<img>').attr({
                    'src': e.target.result,
                    'class': 'temp-image', // Thêm class để có thể dễ dàng xóa sau đó nếu cần
                    'style': 'max-width: 200px; max-height: 200px;' // Thêm style tùy chỉnh
                });
                $('#chat-box').append(tempImage);
                scrollToBottom(); // Cuộn đến cuối chat-box để hiển thị ảnh mới
            };
            reader.readAsDataURL(file);
        }



        function sendMessage() {
            var messageContent = $('#chat-input').val().trim();
            if (messageContent.length === 0 && !imageData) {
                return; // Không gửi nếu không có nội dung
            }

            var formData = new FormData();
            formData.append('message', messageContent);
            if (imageData) {
                formData.append('imageData', imageData);
            }

            $.ajax({
                url: 'send_message.php',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    if (response.status === 'success') {
                        $('#chat-input').val(''); // Làm mới input
                        imageData = null; // Reset dữ liệu hình ảnh
                        // fetchNewMessages(); // Bỏ đi, để fetchNewMessages tự động cập nhật
                    } else {
                        console.error('Error:', response.message);
                    }
                },
                error: function() {
                    alert('Có lỗi xảy ra khi gửi tin nhắn.');
                }
            });
        }


        function addMessageToChatBox(data, isCurrentUser) {
            var messageClass = isCurrentUser ? 'my-message' : 'friend-message';
            var sender = isCurrentUser ? 'Me' : data.sender_username || "Unknown";
            var newMessageHtml = '<div class="message ' + messageClass + '"><strong>' + sender + ':</strong> ' +
                data.message;
            if (data.imagePath) {
                newMessageHtml += '<img src="' + data.imagePath +
                    '" style="max-width:200px; border-radius:20px;">';
            }
            newMessageHtml += '</div>';
            $('#chat-box').append(newMessageHtml);
            scrollToBottom();
        }

        var lastMessageId = 0; // Khởi tạo với 0 hoặc ID của tin nhắn cuối cùng đã biết

        function fetchNewMessages() {
            $.ajax({
                url: 'fetch_new_messages.php',
                type: 'GET',
                dataType: 'json',
                data: {
                    lastMessageId: lastMessageId
                },
                success: function(messages) {
                    messages.forEach(function(message) {
                        if (message.id > lastMessageId) {
                            var newMessageHtml = '<div class="message ' + (message
                                    .sender_username === currentUsername ? 'my-message' :
                                    'friend-message') + '"><strong>' + message
                                .sender_username + ':</strong> ' + message.message +
                                '</div>';
                            $('#chat-box').append(newMessageHtml);
                            lastMessageId = message.id; // Cập nhật lastMessageId
                        }
                    });
                    if (messages.length > 0) {
                        $('#chat-box').scrollTop($('#chat-box')[0]
                            .scrollHeight); // Cuộn đến cuối chat-box
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching new messages:", error);
                }
            });
        }
        setInterval(fetchNewMessages, 2000);

        scrollToBottom();

        function scrollToBottom() {
            var chatBox = $('#chat-box');
            chatBox.scrollTop(chatBox[0].scrollHeight);
        }

        function updateChatBox(message) {
            var messageClass = message.sender_username === currentUsername ? 'my-message' : 'friend-message';
            var messageHtml = '<div class="message ' + messageClass + '">';
            messageHtml += '<strong>' + message.sender_username + ':</strong> ' + message.message;
            if (message.image_path) {
                messageHtml += '<img src="' + message.image_path +
                    '" alt="Image" style="max-width:200px; border-radius:20px;">';
            }
            messageHtml += '</div>';
            $('#chat-box').append(messageHtml);
            $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight); // Cuộn đến tin nhắn mới nhất
        }
        setInterval(fetchNewMessages, 1000);
    });
    </script>
</body>

</html>