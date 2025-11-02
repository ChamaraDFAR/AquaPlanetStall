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

-- Seed restaurant categories for U section stalls
INSERT IGNORE INTO categories (id, name, price) VALUES
  (1, 'General Restaurant', 200000),
  (2, 'Special Restaurant', 400000);

-- Seed categories for P-T section stalls
INSERT IGNORE INTO categories (id, name, price) VALUES
  (3, 'Banking partner', 3500000),
  (4, 'Platinum sponsor', 3200000),
  (5, 'Gold sponsor', 3000000),
  (6, 'Silver sponsor', 2500000),
  (7, 'Bronze sponsor', 2000000),
  (8, 'Co sponsor stalls', 1500000),
  (9, 'General Exhibition stall', 200000);

-- Seed categories for V section stalls (Ornamental Fish Stalls)
INSERT IGNORE INTO categories (id, name, price) VALUES
  (10, 'Ornamental Fish Stall(A)', 500000),
  (11, 'Ornamental Fish Stall(B)', 400000),
  (12, 'Ornamental Fish Stall(C)', 300000),
  (13, 'Ornamental Fish Stall(D)', 200000);

CREATE TABLE IF NOT EXISTS bookings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  reference VARCHAR(32) NOT NULL UNIQUE,
  category ENUM('Ornamental','Other','General Restaurant','Special Restaurant','Banking partner','Platinum sponsor','Gold sponsor','Silver sponsor','Bronze sponsor','Co sponsor stalls','General Exhibition stall','Ornamental Fish Stall(A)','Ornamental Fish Stall(B)','Ornamental Fish Stall(C)','Ornamental Fish Stall(D)') NOT NULL,
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
