CREATE DATABASE IF NOT EXISTS quanlyphonghoc CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE quanlyphonghoc;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fullname VARCHAR(100) NOT NULL,
  email VARCHAR(150) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(50) DEFAULT 'user',
  requested_role VARCHAR(50) DEFAULT 'user',
  status ENUM('pending','active','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
