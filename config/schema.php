<?php
$tables = [
    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_name VARCHAR(255) NOT NULL,
        price DECIMAL(15,2) NOT NULL,
        duration VARCHAR(100),
        max_seats INT DEFAULT 10, -- ເພີ່ມແຖວນີ້ເຂົ້າໄປ
        image VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    )",
    "customers" => "CREATE TABLE IF NOT EXISTS customers (
        customer_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT
    )",
    "bookings" => "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT PRIMARY KEY AUTO_INCREMENT,
        customer_id INT,
        tour_id INT,
        num_people INT,
        total_price DECIMAL(15,2),
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    // --- ເພີ່ມສ່ວນນີ້ເຂົ້າໄປ ---
    "payments" => "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        amount DECIMAL(15,2),
        payment_method VARCHAR(50),
        payment_slip VARCHAR(255),
        payment_date DATETIME
    )",
    "users" => "CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        role ENUM('Admin', 'Staff') DEFAULT 'Staff'
    )"
];