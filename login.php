<?php
// login.php
session_start();
$login_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "messaging_app");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $username;
            header('Location: welcome.php');
            exit();
        } else {
            $login_error = "Mật khẩu không đúng.";
        }
    } else {
        $login_error = "Tên người dùng không tồn tại.";
    }

    mysqli_close($conn);
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