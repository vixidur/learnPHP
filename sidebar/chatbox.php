<?php
session_start();

// Redirect the user to the login page if not logged in.
if (!isset($_SESSION['username'])) {
    header('Location: ../login.php');
    exit();
}

// Connect to the database using PDO
try {
    $conn = new PDO("mysql:host=localhost;dbname=messaging_app", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Function to fetch messages from the database
function fetchMessages($conn)
{
    $sql = "SELECT * FROM messages ORDER BY sent_at ASC"; // Sort messages from old to new
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$messages = fetchMessages($conn);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@2.1.0/css/boxicons.min.css">
    <link rel="icon" href="../img/LOGO-DKN-VER2.png" type="image/png" sizes="16x16">
</head>
<style>
    /* width */
    ::-webkit-scrollbar {
        width: 5px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #ea7e96;
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

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
        background: linear-gradient(217deg, #80cdfb, rgba(255, 0, 0, 0) 70.71%), linear-gradient(127deg, #ffaad7, rgba(0, 255, 0, 0) 70.71%), linear-gradient(336deg, #ffa7b4, rgba(0, 0, 255, 0) 70.71%);

        padding: 20px;
    }

    .chat-container {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
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
        width: 0.1px;
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

    .name {
        cursor: pointer;
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

        #image {
            margin-left: 32px;
        }

        .chat-box {
            width: 100%;
            height: 400px;
        }

        .input-group,
        .logout-button {
            width: 100%;
            max-width: 600px;
            margin-top: 10px;
        }

        .chat-box {
            /* margin-left: -79px; */
            /* margin: 0 auto; */
            width: 100%;
            height: 400px;
        }
    }

    /* side bar đẹp */
    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        height: 100%;
        width: 78px;
        background: #11101d;
        padding: 6px 14px;
        z-index: 99;
        transition: all 0.5s ease;
    }

    .sidebar.open {
        width: 250px;
    }

    .sidebar .logo-details {
        z-index: -99999;
        height: 60px;
        display: flex;
        align-items: center;
        position: relative;
    }

    .sidebar .logo-details .icon {
        opacity: 0;
        transition: all 0.5s ease;
    }

    .sidebar .logo-details .logo_name {
        color: #fff;
        font-size: 20px;
        font-weight: 600;
        opacity: 0;
        transition: all 0.5s ease;
    }

    .sidebar.open .logo-details .icon,
    .sidebar.open .logo-details .logo_name {
        opacity: 1;
    }

    .sidebar .logo-details #btn {
        background-color: #11101d;
        z-index: 9999;
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        font-size: 22px;
        transition: all 0.4s ease;
        font-size: 23px;
        text-align: center;
        cursor: pointer;
        transition: all 0.5s ease;
    }

    .sidebar.open .logo-details #btn {
        text-align: right;
    }

    .sidebar i {
        color: #fff;
        height: 60px;
        min-width: 50px;
        font-size: 28px;
        text-align: center;
        line-height: 60px;
    }

    .sidebar .nav-list {
        margin-top: 20px;
        height: 100%;
    }

    .sidebar li {
        position: relative;
        margin: 8px 0;
        list-style: none;
    }

    .sidebar li .tooltip {
        position: absolute;
        top: -20px;
        left: calc(100% + 15px);
        z-index: 3;
        background: #fff;
        box-shadow: 0 5px 10px rgba(0, 0, 0, 0.3);
        padding: 6px 12px;
        border-radius: 4px;
        font-size: 15px;
        font-weight: 400;
        opacity: 0;
        white-space: nowrap;
        pointer-events: none;
        transition: 0s;
    }

    .sidebar li:hover .tooltip {
        opacity: 1;
        pointer-events: auto;
        transition: all 0.4s ease;
        top: 50%;
        transform: translateY(-50%);
    }

    .sidebar.open li .tooltip {
        display: none;
    }

    .sidebar input {
        font-size: 15px;
        color: #fff;
        font-weight: 400;
        outline: none;
        height: 50px;
        width: 100%;
        width: 50px;
        border: none;
        border-radius: 12px;
        transition: all 0.5s ease;
        background: #1d1b31;
    }

    .bx-log-out {
        cursor: pointer;
        /* Thiết lập con trỏ chuột thành hình bàn tay khi trỏ vào */
    }

    .sidebar.open input {
        padding: 0 20px 0 50px;
        width: 100%;
    }

    .sidebar .bx-search {
        position: absolute;
        top: 50%;
        left: 0;
        transform: translateY(-50%);
        font-size: 22px;
        background: #1d1b31;
        color: #fff;
    }

    .sidebar.open .bx-search:hover {
        background: #1d1b31;
        color: #fff;
    }

    .sidebar .bx-search:hover {
        background: #fff;
        color: #11101d;
    }

    .sidebar li a {
        display: flex;
        height: 100%;
        width: 100%;
        border-radius: 12px;
        align-items: center;
        text-decoration: none;
        transition: all 0.4s ease;
        background: #11101d;
    }

    .sidebar li a:hover {
        background: #fff;
    }

    .sidebar li a .links_name {
        color: #fff;
        font-size: 15px;
        font-weight: 400;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: 0.4s;
    }

    .sidebar.open li a .links_name {
        opacity: 1;
        pointer-events: auto;
    }

    .sidebar li a:hover .links_name,
    .sidebar li a:hover i {
        transition: all 0.5s ease;
        color: #11101d;
    }

    .sidebar li i {
        height: 50px;
        line-height: 50px;
        font-size: 18px;
        border-radius: 12px;
    }

    .sidebar li.profile {
        position: fixed;
        height: 60px;
        width: 78px;
        left: 0;
        bottom: -8px;
        padding: 10px 14px;
        background: #1d1b31;
        transition: all 0.5s ease;
        overflow: hidden;
    }

    .sidebar.open li.profile {
        width: 250px;
    }

    .sidebar li .profile-details {
        display: flex;
        align-items: center;
        flex-wrap: nowrap;
    }

    .sidebar li.profile .name,
    .sidebar li.profile .job {
        font-size: 15px;
        font-weight: 400;
        color: #fff;
        white-space: nowrap;
    }

    .sidebar li.profile .job {
        font-size: 12px;
    }

    .sidebar .profile #log_out {
        position: absolute;
        top: 50%;
        right: 0;
        transform: translateY(-50%);
        background: #1d1b31;
        width: 100%;
        height: 60px;
        line-height: 60px;
        border-radius: 0px;
        transition: all 0.5s ease;
    }

    .sidebar.open .profile #log_out {
        width: 50px;
        background: none;
    }

    .home-section {
        position: relative;
        background: transparent;
        min-height: 100vh;
        top: 0;
        /* left: 78px; */
        transition: all 0.5s ease;
        z-index: 2;
    }

    .home-section .text {
        display: inline-block;
        color: #11101d;
        font-size: 25px;
        font-weight: 500;
        margin: 18px;
    }

    .logo-img {
        position: relative;
        z-index: 1;
        /* Đảm bảo hình ảnh LOGO-DKN-VER2 luôn nằm phía trước icon menu khi sidebar mở */
    }

    @media (max-width: 420px) {
        .sidebar {
            display: none;
        }
    }

    /****** CODE ******/

    .file-upload {
        display: block;
        text-align: center;
        font-family: Helvetica, Arial, sans-serif;
        font-size: 12px;
    }

    .file-upload .file-select {
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select .file-select-button {
        background: #dce4ec;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }

    .file-upload .file-select:hover {
        border-color: #34495e;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select:hover .file-select-button {
        background: #34495e;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select {
        border-color: #3fa46a;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload.active .file-select .file-select-button {
        background: #3fa46a;
        color: #FFFFFF;
        transition: all .2s ease-in-out;
        -moz-transition: all .2s ease-in-out;
        -webkit-transition: all .2s ease-in-out;
        -o-transition: all .2s ease-in-out;
    }

    .file-upload .file-select input[type=file] {
        z-index: 100;
        cursor: pointer;
        position: absolute;
        height: 100%;
        width: 100%;
        top: 0;
        left: 0;
        opacity: 0;
        filter: alpha(opacity=0);
    }

    .file-upload .file-select.file-select-disabled {
        opacity: 0.65;
    }

    .file-upload .file-select.file-select-disabled:hover {
        cursor: default;
        display: block;
        border: 2px solid #dce4ec;
        color: #34495e;
        cursor: pointer;
        height: 40px;
        line-height: 40px;
        margin-top: 5px;
        text-align: left;
        background: #FFFFFF;
        overflow: hidden;
        position: relative;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-button {
        background: #dce4ec;
        color: #666666;
        padding: 0 10px;
        display: inline-block;
        height: 40px;
        line-height: 40px;
    }

    .file-upload .file-select.file-select-disabled:hover .file-select-name {
        line-height: 40px;
        display: inline-block;
        padding: 0 10px;
    }
</style>

<body>
    <div class="sidebar">
        <div class="logo-details">
            <div class="logo-img">
                <img src="../img/LOGO-DKN-VER2.png" alt="" width="20px" height="20px"
                    style="position: relative; margin-right: 20px; z-index: -99999">
            </div>
            <div class="logo_name">DUONGKENH</div>
            <i class='bx bx-menu' id="btn"></i>
        </div>
        <ul class="nav-list">
            <li>
                <i class='bx bx-search'></i>
                <input type="text" placeholder="Search...">
                <span class="tooltip">Search</span>
            </li>
            <li>
                <a href="../welcome.php">
                    <i class='bx bx-home-alt'></i>
                    <span class="links_name">Trang chủ</span>
                </a>
                <span class="tooltip">Trang chủ</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-user'></i>
                    <span class="links_name">Bạn bè</span>
                </a>
                <span class="tooltip">Bạn bè</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-chat'></i>
                    <span class="links_name">Chat Box</span>
                </a>
                <span class="tooltip">Chat Box</span>
            </li>
            <li>
                <a href="../learnresponsive">
                    <i class='bx bx-spa'></i>
                    <span class="links_name">Du lịch</span>
                </a>
                <span class="tooltip">Du lịch</span>
            </li>
            <li>
                <a href="#">
                    <i class='bx bx-folder'></i>
                    <span class="links_name">File Manager</span>
                </a>
                <span class="tooltip">Files</span>
            </li>
            <li class="profile">
                <div class="profile-details">
                    <i class='bx bx-export'></i>
                    <div class="name_job">
                        <div class="name">Logout</div>
                    </div>
                </div>
                <i class='bx bx-log-out' id="log_out"></i>
            </li>
        </ul>
    </div>
    <!-- FULL SOURCE CODE CHAT BOX -->
    <section class="home-section">

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
                <button id="send-message" class="send-button"><i class="fa fa-paper-plane"
                        aria-hidden="true"></i></button>
            </div>
            <script type="text/javascript" src="//code.jquery.com/jquery-1.10.2.min.js"></script>

            <div class="file-upload">
                <div class="file-select">
                    <div class="file-select-button" id="fileName">Choose File</div>
                    <div class="file-select-name" id="noFile">No file chosen...</div>
                    <input type="file" name="chooseFile" id="chooseFile">
                </div>
            </div>
        </div>
        <script>
            $(document).ready(function () {
                $('#chat-input').focus();
                var imageData = null;
                var lastMessageId = 0; // ID của tin nhắn cuối cùng được lấy
                var currentUsername =
                    '<?php echo addslashes($_SESSION["username"]); ?>'; // Lấy tên người dùng từ PHP session

                // Gửi tin nhắn khi nhấn enter hoặc nhấp vào nút gửi
                $('#send-message').click(sendMessage);
                $('#chat-input').keypress(function (e) {
                    if (e.which == 13 && !e.shiftKey) {
                        e.preventDefault(); // Ngăn chặn xuống dòng mới
                        sendMessage();
                    }
                });

                // Handle image upload and paste events
                $('#image').change(handleImageUpload);
                $('#chat-input').on('paste', handlePaste);

                // Fetch new messages periodically
                setInterval(fetchNewMessages, 2000);

                function handleImageUpload() {
                    var file = this.files[0];
                    if (file) {
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            imageData = e.target.result;
                            displayImagePreview(imageData);
                        };
                        reader.readAsDataURL(file);
                    }
                }

                function handlePaste(e) {
                    var items = (e.clipboardData || e.originalEvent.clipboardData).items;
                    for (var index in items) {
                        var item = items[index];
                        if (item.kind === 'file' && item.type.startsWith('image/')) {
                            var file = item.getAsFile();
                            var reader = new FileReader();
                            reader.onload = function (e) {
                                imageData = e.target.result;
                                displayImagePreview(imageData);
                            };
                            reader.readAsDataURL(file);
                        }
                    }
                }

                function displayImagePreview(imageData) {
                    var imgHtml =
                        `<img src="${imageData}" class="temp-image" style="max-width: 200px; max-height: 200px;">`;
                    $('#chat-box').append(imgHtml);
                    scrollToBottom();
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
                        url: '../send_message.php',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function (response) {
                            $('#chat-input').val(''); // Làm mới input
                            $('.temp-image').remove(); // Remove temporary image
                            imageData = null; // Reset image data
                            if (response.status === 'success') {
                                fetchNewMessages(); // Fetch new messages including the one just sent
                            } else {
                                console.error('Error:', response.message);
                            }
                        },
                        error: function () {
                            alert('Có lỗi xảy ra khi gửi tin nhắn.');
                        }
                    });
                }

                function fetchNewMessages() {
                    $.ajax({
                        url: '../fetch_new_messages.php',
                        type: 'GET',
                        dataType: 'json',
                        data: {
                            lastMessageId: lastMessageId
                        },
                        success: function (messages) {
                            messages.forEach(function (message) {
                                if (message.id > lastMessageId) {
                                    var messageHtml =
                                        `<div class="message ${message.sender_username === currentUsername ? 'my-message' : 'friend-message'}"><strong>${message.sender_username}:</strong> ${message.message}</div>`;
                                    $('#chat-box').append(messageHtml);
                                    lastMessageId = message.id; // Update lastMessageId
                                }
                            });
                            if (messages.length > 0) {
                                scrollToBottom(); // Scroll to bottom if new messages were added
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error("Error fetching new messages:", error);
                        }
                    });
                }

                function scrollToBottom() {
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            });
        </script>

    </section>
</body>

</html>