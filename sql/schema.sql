CREATE DATABASE IF NOT EXISTS sokofind CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sokofind;

CREATE TABLE IF NOT EXISTS users (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS items (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  user_id BIGINT UNSIGNED NOT NULL,
  kind ENUM('Lost','Found') NOT NULL,
  title VARCHAR(120) NOT NULL,
  category ENUM('Electronics','Books','Clothing','Keys & Cards','Bags','Documents','Other') NOT NULL,
  description TEXT NOT NULL,
  location VARCHAR(160) NOT NULL,
  event_date DATE NOT NULL,
  status ENUM('Open','Claimed','Returned') NOT NULL DEFAULT 'Open',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  INDEX idx_items_browse (status, kind, category, created_at),
  INDEX idx_items_owner (user_id, created_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS claims (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  item_id BIGINT UNSIGNED NOT NULL,
  claimant_id BIGINT UNSIGNED NOT NULL,
  message VARCHAR(700) NOT NULL,
  status ENUM('Pending','Accepted','Declined') NOT NULL DEFAULT 'Pending',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_claim_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
  CONSTRAINT fk_claim_user FOREIGN KEY (claimant_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uq_claim_item_user (item_id, claimant_id),
  INDEX idx_claims_item_status (item_id, status)
) ENGINE=InnoDB;
