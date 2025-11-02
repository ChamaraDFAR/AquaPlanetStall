-- Database schema for Exhibition Stall Booking

-- Create database if not exists (optional)
-- CREATE DATABASE IF NOT EXISTS aqua_planet_reward CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE aqua_planet_reward;

CREATE TABLE IF NOT EXISTS stalls (
  id VARCHAR(10) PRIMARY KEY,
  status ENUM('available','selected','booked') NOT NULL DEFAULT 'available',
  price INT NOT NULL,
  category_id INT NULL,
  organization ENUM('DFAR','NAQDA') NULL,
  booking_ref VARCHAR(32) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Categories table to unify category definitions and pricing
CREATE TABLE IF NOT EXISTS categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL UNIQUE,
  price INT NOT NULL
);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(32) NOT NULL UNIQUE,
  category ENUM('Ornamental','Other') NOT NULL,
  total_price INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS booking_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  stall_id VARCHAR(10) NOT NULL,
  organization ENUM('DFAR','NAQDA') NULL,
  price INT NOT NULL,
  CONSTRAINT fk_booking_items_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  CONSTRAINT fk_booking_items_stall FOREIGN KEY (stall_id) REFERENCES stalls(id) ON DELETE RESTRICT
);

-- Seed stalls if empty (run via a PHP seeder or manually)
-- Seeding will be handled in PHP if table is empty.
