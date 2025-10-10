-- Create database if not exists
CREATE DATABASE IF NOT EXISTS crud_app
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE crud_app;

-- Drop existing table for clean setup (optional during initial dev)
DROP TABLE IF EXISTS articles;

-- Create articles table
CREATE TABLE articles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  author VARCHAR(100) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed data
INSERT INTO articles (title, content, author) VALUES
('Welcome to the CRUD App', 'This is a sample article to verify the setup works.', 'Admin'),
('Second Article', 'You can create, edit, and delete articles using this app.', 'Editor'),
('Styling and UX', 'A touch of CSS makes everything look better.', 'Designer');
