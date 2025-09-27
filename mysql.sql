-- 1. Create database
DROP DATABASE IF EXISTS librarydb;
CREATE DATABASE librarydb;
USE librarydb;

-- 2. Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  role VARCHAR(255) NOT NULL
);

-- 3. Books table
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  publication_year YEAR NOT NULL,
  isbn VARCHAR(50) NOT NULL
);

-- 4. Borrowings table
CREATE TABLE borrowings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  user_id INT NOT NULL,
  borrow_date DATE NOT NULL,
  return_date DATE,
  FOREIGN KEY (book_id) REFERENCES books(id),
  FOREIGN KEY (user_id) REFERENCES users(id)
);

-- 5. Sample data for users
INSERT INTO users (username, password, role) VALUES
('alice', 'password123', 'user'),
('bob', 'password123', 'user'),
('charlie', 'password123', 'user'),
('diana', 'password123', 'user'),
('admin', 'adminpass', 'admin');

-- 6. Sample data for books
INSERT INTO books (title, author, publication_year, isbn) VALUES
('To Kill a Mockingbird', 'Harper Lee', 1960, '9780061120084'),
('1984', 'George Orwell', 1949, '9780451524935'),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, '9780743273565'),
('Pride and Prejudice', 'Jane Austen', 1813, '9780141439518'),
('The Hobbit', 'J.R.R. Tolkien', 1937, '9780345339683');

-- 7. Sample data for borrowings
INSERT INTO borrowings (book_id, user_id, borrow_date, return_date) VALUES
(1, 1, '2025-09-01', '2025-09-15'),
(2, 2, '2025-09-05', NULL),  -- Not returned yet
(3, 3, '2025-08-20', '2025-09-02'),
(5, 4, '2025-09-10', NULL); -- Still borrowed
