CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);


CREATE TABLE messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_username VARCHAR(255) NOT NULL,
    message TEXT,
    image_path VARCHAR(512), -- Sử dụng VARCHAR để lưu trữ đường dẫn của hình ảnh
    sent_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

