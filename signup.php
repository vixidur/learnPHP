<?php
// signup.php
session_start();
$signup_error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = mysqli_connect("localhost", "root", "", "messaging_app");

    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (username, password) VALUES (?, ?)";

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: login.php');
        exit();
    } else {
        $signup_error = "Tên người dùng đã tồn tại hoặc không thể đăng ký.";
    }

    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="./css/signup.css">
</head>
<body>
    <h2>Đăng ký</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <label for="username">Tên người dùng:</label><br>
        <input placeholder="Tên người dùng" type="text" id="username" name="username" required><br>
        <label for="password">Mật khẩu:</label><br>
        <input placeholder="Mật khẩu" type="password" id="password" name="password" required><br><br>
        <input type="submit" value="Đăng ký">
    </form>
    <?php if (!empty($signup_error)) echo "<p>$signup_error</p>"; ?>
     <p class="login-link">Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
</body>
</html>
