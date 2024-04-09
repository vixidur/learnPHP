<?php
    session_start();

    // Xóa tất cả dữ liệu của phiên
    $_SESSION = array();

    // Hủy phiên hiện tại
    session_destroy();

    // Chuyển hướng về trang đăng nhập hoặc trang chính
    header("Location: index.php"); // hoặc index.php tùy theo trang chính của bạn
    exit;
?>
