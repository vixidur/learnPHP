$(document).ready(function () {
    var imageData = null;
    var lastMessageId = 0; // Biến mới để theo dõi ID của tin nhắn cuối cùng được fetch

    // Xử lý sự kiện kéo và thả
    $('#chat-input, .chat-box').on('dragover', function (event) {
        event.preventDefault();
    }).on('drop', function (event) {
        event.preventDefault();
        handleImageDrop(event.originalEvent.dataTransfer.files);
    });

    // Xử lý sự kiện dán hình ảnh
    $('#chat-input').on('paste', function (event) {
        var items = (event.clipboardData || event.originalEvent.clipboardData).items;
        for (var i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') === 0) {
                handleImage(items[i].getAsFile());
            }
        }
    });

    // Xử lý khi người dùng chọn tệp từ máy tính
    $('#image').on('change', function () {
        if (this.files.length > 0) {
            handleImage(this.files[0]);
        }
    });

    // Gửi tin nhắn
    $('#send-message').click(sendMessage);
    $('#chat-input').keypress(function (e) {
        if (e.which == 13 && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Hàm xử lý kéo thả và dán hình ảnh
    function handleImage(file) {
        var reader = new FileReader();
        reader.onload = function (e) {
            imageData = e.target.result;
        };
        reader.readAsDataURL(file);
    }

    function sendMessage() {
        var formData = new FormData();
        formData.append('message', $('#chat-input').text());

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
            success: function (response) {
                // Tạo và thêm tin nhắn mới vào chat-box với đúng class
                var messageClass = 'my-message'; // Giả sử tất cả tin nhắn gửi đi là 'my-message'
                var messageElement = $('<div>', {
                    class: 'message ' + messageClass
                }).html('Me: ' + $('#chat-input').text()); // Sử dụng .html() nếu bạn muốn chèn thẻ HTML

                $('#chat-box').append(messageElement);
                $('#chat-input').html(''); // Xóa nội dung tin nhắn sau khi gửi

                // Cuộn đến tin nhắn mới nhất
                $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
            }
        });
    }


    // Hàm polling tin nhắn mới
    function fetchNewMessages() {
        $.ajax({
            url: 'fetch_new_messages.php', // Thay thế bằng URL chính xác của bạn
            type: 'GET',
            dataType: 'json',
            data: {
                lastMessageId: lastMessageId
            },
            success: function (messages) {
                messages.forEach(function (message) {
                    // Giả sử bạn có một cách để xác định xem tin nhắn có phải từ người dùng hiện tại không
                    // Ví dụ: `message.sender_username` so sánh với tên người dùng lưu trữ ở client
                    var isMyMessage = message.sender_username === $('#username').val(); // Hoặc sử dụng một phương pháp khác để lấy tên người dùng hiện tại
                    var messageClass = isMyMessage ? 'my-message' : 'friend-message';

                    var messageElement = $('<div>', {
                        class: 'message ' + messageClass,
                        text: (isMyMessage ? 'Me: ' : message.sender_username + ': ') + message.message
                    });

                    $('#chat-box').append(messageElement);

                    // Cập nhật lastMessageId
                    lastMessageId = Math.max(lastMessageId, message.id);
                });

                if (messages.length > 0) {
                    // Cuộn đến tin nhắn mới nhất
                    $('#chat-box').scrollTop($('#chat-box')[0].scrollHeight);
                }
            }
        });
    }

    setInterval(fetchNewMessages, 5000);
});