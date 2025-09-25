-- 1. Create database
DROP DATABASE IF EXISTS librarydb;
CREATE DATABASE librarydb;
USE librarydb;

-- 2. Books table
CREATE TABLE books (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(255) NOT NULL,
  author VARCHAR(255) NOT NULL,
  publication_year YEAR NOT NULL,
  isbn VARCHAR(50) NOT NULL
);

-- 3. Borrowing history table
CREATE TABLE borrowings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  book_id INT NOT NULL,
  user_name VARCHAR(255) NOT NULL,
  borrow_date DATE NOT NULL,
  return_date DATE,
  FOREIGN KEY (book_id) REFERENCES books(id)
);

-- 4. Sample data for books
INSERT INTO books (title, author, publication_year, isbn) VALUES
('To Kill a Mockingbird', 'Harper Lee', 1960, '9780061120084'),
('1984', 'George Orwell', 1949, '9780451524935'),
('The Great Gatsby', 'F. Scott Fitzgerald', 1925, '9780743273565'),
('Pride and Prejudice', 'Jane Austen', 1913, '9780141439518'),
('The Hobbit', 'J.R.R. Tolkien', 1937, '9780345339683');

-- 5. Sample data for borrowings
INSERT INTO borrowings (book_id, user_name, borrow_date, return_date) VALUES
(1, 'Alice', '2025-09-01', '2025-09-15'),
(2, 'Bob', '2025-09-05', NULL),  -- Not returned yet
(3, 'Charlie', '2025-08-20', '2025-09-02'),
(5, 'Diana', '2025-09-10', NULL); -- Still borrowed
