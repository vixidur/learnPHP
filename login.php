<?php
session_start();
require_once 'dbconnect.php'; // Đường dẫn đến file dbconnect.php của bạn

// Lấy thông tin đăng nhập từ form
$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = connectDB();  // Gọi hàm kết nối cơ sở dữ liệu

    // Chuẩn bị và thực thi truy vấn SQL sử dụng PDO
    $stmt = $conn->prepare("SELECT password FROM users WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    // Kiểm tra kết quả truy vấn
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        // Kiểm tra mật khẩu
        if (password_verify($password, $user['password'])) {
            // Đăng nhập thành công, thiết lập session
            $_SESSION['username'] = $username;
            header("Location: welcome.php"); // Chuyển hướng người dùng đến welcome.php
            exit();
        } else {
            echo "Mật khẩu không chính xác";
        }
    } else {
        echo "Tên đăng nhập không tồn tại";
    }

    $conn = null; // Đóng kết nối cơ sở dữ liệu
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <link rel="stylesheet" href="./css/login.css">
    <link rel="icon" href="./img/LOGO-DKN-VER2.png" type="image/png" sizes="16x16">
</head>

<body>
    <h2>Đăng nhập</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Tên người dùng:</label><br>
        <input placeholder="Tên người dùng" type="text" id="username" name="username" required><br>
        <label for="password">Mật khẩu:</label><br>
        <input placeholder="Mật khẩu" type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Đăng nhập">
    </form>
    <?php if (!empty($login_error))
        echo "<p>$login_error</p>"; ?>
    <p>Chưa có tài khoản? <a href="signup.php">Đăng ký ngay</a></p>
</body>

</html>