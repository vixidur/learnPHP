<?php
session_start();

function getFullImagePath($filePath)
{
    // Đảm bảo rằng $filePath là đường dẫn tương đối từ thư mục gốc của dự án đến file hình ảnh
    $scheme = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $host = $_SERVER['HTTP_HOST']; // Có thể là localhost hoặc 127.0.0.1 hoặc tên máy chủ cục bộ của bạn
    // Đường dẫn đầy đủ từ gốc của host đến file hình ảnh
    return $scheme . '://' . $host . '/learnPHP/uploads/' . ltrim($filePath, '/');
}

function saveBase64Image($base64Image)
{
    // Đảm bảo thư mục "uploads" tồn tại trong thư mục "learnPHP"
    $uploadsDir = 'uploads/'; // Đường dẫn tương đối từ script hiện tại đến thư mục uploads
    if (!is_dir($uploadsDir)) {
        mkdir($uploadsDir, 0777, true); // Tạo thư mục nếu nó không tồn tại
    }

    if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
        $data = substr($base64Image, strpos($base64Image, ',') + 1);
        $type = strtolower($type[1]); // jpg, png, gif

        if (!in_array($type, ['jpg', 'jpeg', 'gif', 'png'])) {
            return false; // Loại file không hợp lệ
        }

        $data = base64_decode($data);
        if ($data === false) {
            return false; // Lỗi giải mã Base64
        }
    } else {
        return false; // Dữ liệu không đúng định dạng
    }

    $file = uniqid() . '.' . $type; // Tên file duy nhất
    if (file_put_contents($uploadsDir . $file, $data)) {
        return $file; // Trả về tên file để tạo đường dẫn đầy đủ sau này
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
        } else {
            $imagePath = getFullImagePath($imagePath);
        }
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($fileType, $allowedTypes)) {
            $imageFileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $imagePath = "uploads/" . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
                $imagePath = getFullImagePath($imagePath);
            } else {
                jsonResponse('error', 'Error uploading the image file.');
            }
        } else {
            jsonResponse('error', 'The file type is not allowed.');
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