<?php
session_start();

function saveBase64Image($base64Image)
{
    // Xác định MIME type từ chuỗi base64
    if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
        $data = substr($base64Image, strpos($base64Image, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            return false; // Không phải file hình ảnh
        }

        $data = base64_decode($data);

        if ($data === false) {
            return false; // Base64 decode lỗi
        }
    } else {
        return false; // Không khớp với pattern
    }

    $file = 'uploads/' . uniqid() . '.' . $type;
    if (file_put_contents($file, $data)) {
        return $file;
    }

    return false;
}
// Hàm trả về JSON response
function jsonResponse($status, $message, $data = null)
{
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'message' => $message, 'data' => $data]);
    exit();
}

if (isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "messaging_app");
    if ($conn->connect_error) {
        jsonResponse('error', 'Connection failed: ' . $conn->connect_error);
    }

    $message = trim($_POST['message']);
    $imagePath = '';

    // xử lý hình ảnh
    if (!empty($_POST['imageData'])) {
        $imagePath = saveBase64Image($_POST['imageData']);
        if (!$imagePath) {
            jsonResponse('error', 'Invalid image data.');
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($fileType, $allowedTypes)) {
            $imagePath = 'uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }
    }
    // thêm tin nhắn từ cơ sở dữ liệu
    // Thêm tin nhắn vào cơ sở dữ liệu
    if (!empty($message) || !empty($imagePath)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_username, message, image_path) VALUES (?, ?, ?)");
        if ($stmt === false) {
            jsonResponse('error', 'Prepare failed: ' . $conn->error);
        }
        $stmt->bind_param("sss", $_SESSION['username'], $message, $imagePath);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $lastId = $conn->insert_id; // Lấy ID của tin nhắn vừa được thêm
            $responseData = [
                'messageId' => $lastId,
                'message' => $message,
                'imagePath' => $imagePath
            ];
            jsonResponse('success', 'Message sent.', $responseData);
        } else {
            jsonResponse('error', 'Failed to send message.');
        }

        $stmt->close();
    } else {
        jsonResponse('error', 'No message or image provided.');
    }

    $conn->close();
} else {
    jsonResponse('error', 'User not logged in or invalid request.');
}
?>