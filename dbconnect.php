<?php
function connectDB()
{
    // Lấy thông tin từ biến môi trường hoặc sử dụng giá trị mặc định hợp lý
    $host = getenv('DB_SERVER') ?: 'localhost';
    $dbname = getenv('DB_NAME') ?: 'messaging_app'; // Đảm bảo tên DB này chính xác
    $username = getenv('DB_USERNAME') ?: 'root'; // Tên người dùng mặc định là 'root', chỉ dùng cho môi trường phát triển!
    $password = getenv('DB_PASSWORD') ?: ''; // Mật khẩu cần được bảo mật, không để trống và không nên dùng mật khẩu mặc định

    try {
        $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
}
?>