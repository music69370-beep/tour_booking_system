<?php
$tables = [
    "vehicles" => "CREATE TABLE IF NOT EXISTS vehicles (
        vehicle_id INT PRIMARY KEY AUTO_INCREMENT,
        plate_number VARCHAR(20) NOT NULL,
        model VARCHAR(100),
        vehicle_type VARCHAR(50),
        capacity INT,
        insurance_expiry DATE,
        amenities TEXT,
        driver_name VARCHAR(100),
        driver_phone VARCHAR(20),
        license_number VARCHAR(50),
        license_expiry DATE,
        experience_years INT,
        emergency_contact VARCHAR(255),
        driver_image VARCHAR(255),
        license_image VARCHAR(255),
        status ENUM('Available', 'Busy', 'Maintenance') DEFAULT 'Available'
    )",

    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        vehicle_id INT,
        tour_name VARCHAR(255) NOT NULL,
        price DECIMAL(15,2) NOT NULL,
        duration VARCHAR(100),
        itinerary TEXT,
        meals INT DEFAULT 0,
        activities TEXT,
        max_seats INT DEFAULT 10,
        image VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    )",

    // --- ເພີ່ມຕາຕະລາງເກັບຮູບພາບ Gallery ບ່ອນນີ້ ---
    "tour_images" => "CREATE TABLE IF NOT EXISTS tour_images (
        image_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        image_name VARCHAR(255),
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
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
    )",
    "booking_participants" => "CREATE TABLE IF NOT EXISTS booking_participants (
        part_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        participant_name VARCHAR(255),
        participant_phone VARCHAR(20), -- ເພີ່ມ Column ເບີໂທ
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )"
];