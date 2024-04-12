$(document).ready(function () {
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
        $('#chat-input, .chat-box').on('dragover', function (event) {
            event.preventDefault();
        }).on('drop', function (event) {
            event.preventDefault();
            handleImageDrop(event.originalEvent.dataTransfer.files);
        });
    }
    // dán ảnh
    function setupPasteImage() {
        $('#chat-input').on('paste', function (event) {
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
        $('#image').on('change', function () {
            if (this.files && this.files[0]) {
                handleImage(this.files[0]);
            }
        });
    }

    function setupMessageSending() {
        $('#send-message').click(sendMessage);
        $('#chat-input').keypress(function (e) {
            if (e.which == 13 && !e.shiftKey) {
                e.preventDefault(); // Ngăn chặn xuống dòng mới
                sendMessage();
            }
        });
    }




    function handleImage(file) {
        var reader = new FileReader();
        reader.onload = function (e) {
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
            url: '../send_message.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function (response) {
                if (response.status === 'success') {
                    $('#chat-input').val(''); // Làm mới input
                    imageData = null; // Reset dữ liệu hình ảnh
                    // fetchNewMessages(); // Bỏ đi, để fetchNewMessages tự động cập nhật
                } else {
                    console.error('Error:', response.message);
                }
            },
            error: function () {
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
            url: '../fetch_new_messages.php',
            type: 'GET',
            dataType: 'json',
            data: {
                lastMessageId: lastMessageId
            },
            success: function (messages) {
                messages.forEach(function (message) {
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
            error: function (xhr, status, error) {
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

function toggleSidebar() {
    var sidebar = document.getElementById('sidebar');
    if (sidebar.style.left === '0px') {
        sidebar.style.left = '-250px';
    } else {
        sidebar.style.left = '0px';
    }
}