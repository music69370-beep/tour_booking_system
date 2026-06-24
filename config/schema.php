<?php
// ໄຟລ໌ລວມໂຄງສ້າງຖານຂໍ້ມູນທັງໝົດ - ປັບປຸງ (ຮັກສາຂອງເກົ່າໄວ້ທັງໝົດ ແລະ ເພີ່ມລະບົບຫ້ອງພັກ)
$tables = [
    "vehicles" => "CREATE TABLE IF NOT EXISTS vehicles (
        vehicle_id INT PRIMARY KEY AUTO_INCREMENT,
        plate_number VARCHAR(20) NOT NULL,
        model VARCHAR(100),
        vehicle_type VARCHAR(50),
        capacity INT,
        insurance_expiry DATE,
        amenities TEXT,
        status ENUM('Available', 'Busy', 'Maintenance') DEFAULT 'Available'
    )", 

    "drivers" => "CREATE TABLE IF NOT EXISTS drivers (
        driver_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100) NOT NULL,
        phone VARCHAR(20) NOT NULL,
        id_card_no VARCHAR(50),
        license_number VARCHAR(50),
        license_type VARCHAR(20),
        license_expiry DATE,
        experience_years INT,
        emergency_phone VARCHAR(20),
        address TEXT,
        image VARCHAR(255),
        license_image VARCHAR(255),
        id_card_image VARCHAR(255),
        status ENUM('Available', 'Busy', 'Off') DEFAULT 'Available',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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

    "coupons" => "CREATE TABLE IF NOT EXISTS coupons (
        coupon_id INT PRIMARY KEY AUTO_INCREMENT,
        code VARCHAR(50) NOT NULL UNIQUE,
        discount_type ENUM('Fixed', 'Percent') NOT NULL,
        discount_value DECIMAL(15,2) NOT NULL,
        min_spend DECIMAL(15,2) DEFAULT 0,
        max_discount DECIMAL(15,2) DEFAULT 0,
        total_limit INT DEFAULT 0,
        limit_per_user INT DEFAULT 1,
        specific_tour_id INT DEFAULT NULL,
        expiry_date DATE NOT NULL,
        status ENUM('Active', 'Inactive') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",
    
    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_code VARCHAR(50),
        tour_name VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        price DECIMAL(15,2) NOT NULL,
        cost_per_person DECIMAL(15,2) DEFAULT 0,
        start_date DATE,
        end_date DATE,
        duration VARCHAR(100),
        meeting_point VARCHAR(255),
        itinerary TEXT,
        highlights TEXT,
        meals INT DEFAULT 0,
        activities TEXT,
        whats_included TEXT,
        whats_excluded TEXT,
        cancellation_policy TEXT,
        max_seats INT DEFAULT 10,
        min_pax INT DEFAULT 1,
        image VARCHAR(255),
        status ENUM('Active', 'Inactive') DEFAULT 'Active'
    )",

    "customers" => "CREATE TABLE IF NOT EXISTS customers (
        customer_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100),
        gender ENUM('Male', 'Female', 'Other'),
        birthday DATE,
        nationality VARCHAR(50),
        id_card_no VARCHAR(50),
        id_card_image VARCHAR(255),
        phone VARCHAR(20) UNIQUE,
        email VARCHAR(100),
        address TEXT,
        emergency_name VARCHAR(100),
        emergency_phone VARCHAR(20)
    )",

    "bookings" => "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT PRIMARY KEY AUTO_INCREMENT,
        customer_id INT,
        tour_id INT,
        coupon_id INT,
        travel_date DATE NOT NULL,
        num_people INT,
        room_type ENUM('Twin', 'Single') DEFAULT 'Twin', -- ເພີ່ມໃໝ່
        total_price DECIMAL(15,2),
        single_supplement_fee DECIMAL(15,2) DEFAULT 0, -- ເພີ່ມໃໝ່
        discount_amount DECIMAL(15,2) DEFAULT 0,
        refund_amount DECIMAL(15,2) DEFAULT 0,
        cancellation_cost DECIMAL(15,2) DEFAULT 0,
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        cancel_reason TEXT,
        note TEXT,
        selected_seats VARCHAR(255),
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
        FOREIGN KEY (coupon_id) REFERENCES coupons(coupon_id) ON DELETE SET NULL
    )",
    
    // ຕາຕະລາງໃໝ່ສຳລັບຢັດເບີຫ້ອງແຍກຕາມໂຮງແຮມ (Pro Option 2)
    "booking_room_assignments" => "CREATE TABLE IF NOT EXISTS booking_room_assignments (
        assignment_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        hotel_name VARCHAR(255),
        participant_name VARCHAR(255),
        room_number VARCHAR(50),
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    "booking_tasks" => "CREATE TABLE IF NOT EXISTS booking_tasks (
        task_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        task_label VARCHAR(255) NOT NULL,
        is_completed TINYINT(1) DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    "booking_participants" => "CREATE TABLE IF NOT EXISTS booking_participants (
        part_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        participant_name VARCHAR(255),
        participant_id_card VARCHAR(50),
        participant_phone VARCHAR(50),
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
        employee_code VARCHAR(50) UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT,
        dob DATE,
        profile_pic VARCHAR(255),
        id_card_no VARCHAR(50),
        id_card_img VARCHAR(255),
        role ENUM('Admin', 'Staff') DEFAULT 'Staff',
        status ENUM('Active', 'Resigned') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    "reviews" => "CREATE TABLE IF NOT EXISTS reviews (
        review_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        customer_id INT,
        rating INT CHECK (rating >= 1 AND rating <= 5),
        comment TEXT,
        review_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('Pending', 'Approved') DEFAULT 'Approved',
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE
    )",
        "tour_expenses" => "CREATE TABLE IF NOT EXISTS tour_expenses (
        expense_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        travel_date DATE NOT NULL,
        category ENUM('Hotel', 'Fuel', 'Maintenance', 'Food', 'Guide_Fee', 'Other') NOT NULL,
        amount DECIMAL(15,2) NOT NULL,
        description TEXT,
        expense_date DATE, -- ເພີ່ມ Column ນີ້ເພື່ອເກັບວັນທີທີ່ຈ່າຍແທ້
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
    )",
        "tour_images" => "CREATE TABLE IF NOT EXISTS tour_images (
        image_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        image_name VARCHAR(255),
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
    )",
    "vehicle_outings" => "CREATE TABLE IF NOT EXISTS vehicle_outings (
        outing_id INT PRIMARY KEY AUTO_INCREMENT,
        vehicle_id INT,
        tour_id INT,
        driver_id INT,
        start_date DATE,
        return_date DATE,
        start_mileage INT DEFAULT 0,
        end_mileage INT DEFAULT 0,
        status ENUM('On Trip', 'Completed', 'Cancelled') DEFAULT 'On Trip',
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (vehicle_id) REFERENCES vehicles(vehicle_id) ON DELETE CASCADE,
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
        FOREIGN KEY (driver_id) REFERENCES drivers(driver_id) ON DELETE SET NULL
    )",
];