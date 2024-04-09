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

if (isset($_SESSION['username']) && $_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli("localhost", "root", "", "messaging_app");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $message = trim($_POST['message']);
    $imagePath = '';

    if (!empty($_POST['imageData'])) {
        $imagePath = saveBase64Image($_POST['imageData']);
    } elseif (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['image']['tmp_name']);
        if (in_array($fileType, $allowedTypes)) {
            $imagePath = 'uploads/' . basename($_FILES['image']['name']);
            move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
        }
    }

    if (!empty($message) || !empty($imagePath)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_username, message, image_path) VALUES (?, ?, ?)");
        if ($stmt === false) {
            die("Prepare failed: " . htmlspecialchars($conn->error));
        }
        $stmt->bind_param("sss", $_SESSION['username'], $message, $imagePath);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $response = "<div class='message my-message'><strong>Me:</strong> " . htmlspecialchars($message);
            if (!empty($imagePath)) {
                $safeImagePath = htmlspecialchars($imagePath);
                $response .= "<br><img src='{$safeImagePath}' alt='Image' style='max-width: 200px;'>";
            }
            $response .= "</div>";
            echo $response;
        } else {
            echo "Failed to send message.";
        }

        $stmt->close();
    }

    $conn->close();
}
?>