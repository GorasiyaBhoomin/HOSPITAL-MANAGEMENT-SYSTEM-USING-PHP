<?php

try {
    // Establishing connection
    $con = new mysqli("localhost", "root","", "KK_PATEL_HOSPITAL2");

    // if ($con->connect_error) {
    //     throw new Exception("Connection failed: " . $con->connect_error);
    // }
    // echo "Connected successfully<br>";

    // Create database (uncomment if needed)
    // $db = "CREATE DATABASE KK_PATEL_HOSPITAL";
    // if ($con->query($db) === TRUE) {
    //     echo "Database created successfully";
    // } else {
    //     throw new Exception("Error creating database: " . $con->error);
    // }

    // Selecting database
    $con->select_db("KK_PATEL_HOSPITAL2");


    // $doctors = "CREATE TABLE IF NOT EXISTS doctors (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     username VARCHAR(255) NOT NULL,
    //     email VARCHAR(255) NOT NULL UNIQUE,
    //     dob DATE NOT NULL,
    //     gender ENUM('male', 'female') NOT NULL,
    //     departmentname VARCHAR(255) NOT NULL,
    //     address TEXT NOT NULL,
    //     city VARCHAR(255) NOT NULL,
    //     postal_code VARCHAR(10) NOT NULL,
    //     cv VARCHAR(255) NOT NULL, -- Stores the file path
    //     avatar VARCHAR(255) NOT NULL, -- Stores the image file path
    //     phone VARCHAR(15) NOT NULL,
    //     password VARCHAR(255) NOT NULL,
    //     consultancy_fees DECIMAL(10,2) NOT NULL,
    //     bio TEXT NOT NULL,
    //     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    // )";
    
// if ($con->query($doctors) === TRUE) {
    //     echo "Table 'register' is ready.<br>";
    // } else {
    //     throw new Exception("Error creating table: " . $con->error);
    // }

    // CREATE TABLE doctor_time_slots (
    //     id INT AUTO_INCREMENT PRIMARY KEY,
    //     doctor_id INT NOT NULL,
    //     time_slot TIME NOT NULL,
    //     FOREIGN KEY (doctor_id) REFERENCES doctors(id) ON DELETE CASCADE
    // );
    

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}

// Closing connection
?>
