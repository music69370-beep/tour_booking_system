<?php
$tables = [
    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_name VARCHAR(255) NOT NULL,
        price DECIMAL(15,2) NOT NULL,
        duration VARCHAR(100),
        image VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    )",
    "customers" => "CREATE TABLE IF NOT EXISTS customers (
        customer_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100)
    )",
    "bookings" => "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT PRIMARY KEY AUTO_INCREMENT,
        customer_id INT,
        tour_id INT,
        num_people INT,
        total_price DECIMAL(15,2),
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )"
];