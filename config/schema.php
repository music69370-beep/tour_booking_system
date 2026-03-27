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

    "guides" => "CREATE TABLE IF NOT EXISTS guides (
        guide_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100) NOT NULL,
        license_id VARCHAR(50),
        license_expiry DATE,
        languages VARCHAR(255),
        specialization VARCHAR(255),
        exp_years INT,
        phone VARCHAR(20) NOT NULL,
        email VARCHAR(100),
        address TEXT,
        bank_name VARCHAR(100),
        bank_account VARCHAR(50),
        emergency_contact_name VARCHAR(100),
        emergency_contact_phone VARCHAR(20),
        first_aid_certified TINYINT(1) DEFAULT 0,
        image VARCHAR(255),
        doc_attachment VARCHAR(255),
        status ENUM('Available', 'Busy') DEFAULT 'Available'
    )",
    
    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        vehicle_id INT,
        guide_id INT,
        tour_name VARCHAR(255) NOT NULL,
        price DECIMAL(15,2) NOT NULL,
        cost_per_person DECIMAL(15,2) DEFAULT 0,
        duration VARCHAR(100),
        itinerary TEXT,
        meals INT DEFAULT 0,
        activities TEXT,
        max_seats INT DEFAULT 10,
        image VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    )",

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
        travel_date DATE NOT NULL,
        num_people INT,
        total_price DECIMAL(15,2),
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
    )",

    "booking_participants" => "CREATE TABLE IF NOT EXISTS booking_participants (
        part_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        participant_name VARCHAR(255),
        participant_phone VARCHAR(20),
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    "payments" => "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        amount DECIMAL(15,2),
        payment_method VARCHAR(50),
        payment_slip VARCHAR(255),
        payment_date DATETIME,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    "users" => "CREATE TABLE IF NOT EXISTS users (
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        role ENUM('Admin', 'Staff') DEFAULT 'Staff'
    )",

    // --- ເພີ່ມຕາຕະລາງ Reviews ບ່ອນນີ້ ---
    "reviews" => "CREATE TABLE IF NOT EXISTS reviews (
        review_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        customer_id INT,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Pending', 'Approved') DEFAULT 'Pending',
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
    )"
];