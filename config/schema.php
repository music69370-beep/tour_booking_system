<?php
// ໄຟລ໌ລວມໂຄງສ້າງຖານຂໍ້ມູນທັງໝົດຂອງລະບົບ Tour Booking System
$tables = [
    // 1. ຕາຕະລາງພາຫະນະ (ລົດທົວ)
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

    // 2. ຕາຕະລາງໄກ້ຜູ້ນຳທ່ຽວ
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

    // 3. ຕາຕະລາງຄູປອງສ່ວນຫຼຸດ
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
    
    // 4. ຕາຕະລາງແພັກເກັດທົວ
    "tours" => "CREATE TABLE IF NOT EXISTS tours (
        tour_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_code VARCHAR(50),
        vehicle_id INT,
        guide_id INT,
        tour_name VARCHAR(255) NOT NULL,
        category VARCHAR(100),
        price DECIMAL(15,2) NOT NULL,
        start_date DATE,          -- ວັນທີເລີ່ມທົວ
        end_date DATE,            -- ວັນທີສິ້ນສຸດທົວ
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

    // 5. ຕາຕະລາງເກັບຮູບພາບ Gallery ຂອງທົວ
    "tour_images" => "CREATE TABLE IF NOT EXISTS tour_images (
        image_id INT PRIMARY KEY AUTO_INCREMENT,
        tour_id INT,
        image_name VARCHAR(255),
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE
    )",

    // 6. ຕາຕະລາງຂໍ້ມູນລູກຄ້າ
    "customers" => "CREATE TABLE IF NOT EXISTS customers (
        customer_id INT PRIMARY KEY AUTO_INCREMENT,
        fullname VARCHAR(100),
        phone VARCHAR(20),
        email VARCHAR(100),
        address TEXT
    )",

    // 7. ຕາຕະລາງການຈອງ (Bookings)
    "bookings" => "CREATE TABLE IF NOT EXISTS bookings (
        booking_id INT PRIMARY KEY AUTO_INCREMENT,
        customer_id INT,
        tour_id INT,
        coupon_id INT,
        travel_date DATE NOT NULL,
        num_people INT,
        total_price DECIMAL(15,2),
        discount_amount DECIMAL(15,2) DEFAULT 0,
        refund_amount DECIMAL(15,2) DEFAULT 0,
        cancellation_cost DECIMAL(15,2) DEFAULT 0,
        status ENUM('Pending', 'Confirmed', 'Cancelled') DEFAULT 'Pending',
        cancel_reason TEXT,
        note TEXT,
        booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES customers(customer_id) ON DELETE CASCADE,
        FOREIGN KEY (tour_id) REFERENCES tours(tour_id) ON DELETE CASCADE,
        FOREIGN KEY (coupon_id) REFERENCES coupons(coupon_id) ON DELETE SET NULL
    )",
    
    // 8. ຕາຕະລາງ Checklist ຄວາມພ້ອມ
    "booking_tasks" => "CREATE TABLE IF NOT EXISTS booking_tasks (
        task_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        task_label VARCHAR(255) NOT NULL,
        is_completed TINYINT(1) DEFAULT 0,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    // 9. ຕາຕະລາງລາຍຊື່ຜູ້ຮ່ວມເດີນທາງ
    "booking_participants" => "CREATE TABLE IF NOT EXISTS booking_participants (
        part_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        participant_name VARCHAR(255),
        participant_phone VARCHAR(20),
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    // 10. ຕາຕະລາງການຊຳລະເງິນ
    "payments" => "CREATE TABLE IF NOT EXISTS payments (
        payment_id INT PRIMARY KEY AUTO_INCREMENT,
        booking_id INT,
        amount DECIMAL(15,2),
        payment_method VARCHAR(50),
        payment_slip VARCHAR(255),
        payment_date DATETIME,
        FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
    )",

    // 11. ຕາຕະລາງຜູ້ໃຊ້ລະບົບ (Admin/Staff)
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
        role ENUM('Admin', 'Staff') DEFAULT 'Staff',
        status ENUM('Active', 'Resigned') DEFAULT 'Active',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )",

    // 12. ຕາຕະລາງຄຳຍ້ອງຍໍ ແລະ ຄະແນນ (Reviews)
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
    )"
];