# CCS112_LAB_EXAM_G10
Simple Library Management System (Web-based)
============================================
Project Overview
----------------
This web application allows librarians and users to manage a library catalog efficiently.
Key functionalities include:
- Add, edit, and delete books (librarian)
- View and search catalog (both librarian and user)
- Borrow and return books (user)
- Notifications after every action
- Tracking borrowing history
Objectives
----------
1. Apply fundamental concepts of web development: HTML, CSS, JavaScript, PHP.
2. Perform CRUD operations with a MySQL database.
3. Collaborate using Git/GitHub with branching, merging, and pull requests.
4. Demonstrate proper version control and conflict resolution.
Database Setup
--------------
1. Create the database:
CREATE DATABASE librarydb;
USE librarydb;
2. Books table structure:
CREATE TABLE books (
id INT AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255) NOT NULL,
author VARCHAR(255) NOT NULL,
publication_year YEAR NOT NULL,
isbn VARCHAR(50) NOT NULL
);
3. Borrowing history table:
CREATE TABLE borrowings (
id INT AUTO_INCREMENT PRIMARY KEY,
book_id INT NOT NULL,
user_name VARCHAR(255) NOT NULL,
borrow_date DATE NOT NULL,
return_date DATE,
FOREIGN KEY (book_id) REFERENCES books(id)
);
Folder and File Structure
-------------------------
library_system/
│
├─ index.php # Login page
├─ librarian.php # Librarian dashboard
├─ user.php # User dashboard
├─ style.css # Styling
├─ db.php # Database connection
Important Code Snippets
-----------------------
1. Database connection (db.php):
$conn = new mysqli("db", "root", "rootpassword", "librarydb");
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }
2. Student 1 – Add New Books
- Form fields: Title, Author, Publication Year, ISBN
- PHP inserts into `books` table
- Notification: "New Book Added Successfully!"
- HTML snippet:
<form method="post">
<input type="text" name="title" placeholder="Enter title" required>
<input type="text" name="author" placeholder="Enter author" required>
<input type="text" name="publication_year" placeholder="Enter year" required>
<input type="text" name="isbn" placeholder="Enter ISBN" required>
<input type="submit" value="Add Book">
</form>
- PHP snippet:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
$title = $_POST['title'];
$author = $_POST['author'];
$publication_year = $_POST['publication_year'];
$isbn = $_POST['isbn'];
$conn->query("INSERT INTO books (title, author, publication_year, isbn) VALUES ('$title','$author','$publication_year','$isbn')");
$_SESSION['message'] = "New Book Added Successfully!";
header("Location: " . $_SERVER['PHP_SELF']); exit;
}
3. Student 2 – Edit/Remove Books
- Edit: Populate form with selected book details, update database on submit
- Delete: Request confirmation, delete on confirm
- Notifications:
- Edit: "Book updated successfully!"
- Delete: "Book deleted successfully!"
- HTML table snippet with Edit/Delete buttons:
<table border="1">
<tr><th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>ISBN</th><th>Edit</th><th>Delete</th></tr>
<tr>
<td>1</td>
<td>Book Title</td>
<td>Author Name</td>
<td>2025</td>
<td>12345</td>
<td>
<form method="post">
<input type="hidden" name="id" value="1">
<button type="submit" name="edit_request">Edit</button>
</form>
</td>
<td>
<form method="post">
<input type="hidden" name="id" value="1">
<input type="hidden" name="title" value="Book Title">
<button type="submit" name="delete_request">Delete</button>
</form>
</td>
</tr>
</table>
- PHP snippets for update/delete:
// Edit
$id = $_POST['id'];
$result = $conn->query("SELECT * FROM books WHERE id='$id'");
// On submit:
$conn->query("UPDATE books SET title='$title', author='$author', publication_year='$year', isbn='$isbn' WHERE id='$id'");
$_SESSION['message'] = "Book updated successfully!";
header("Location: " . $_SERVER['PHP_SELF']); exit;
// Delete
if (isset($_POST['confirm_delete'])) {
$conn->query("DELETE FROM books WHERE id='$id'");
$_SESSION['message'] = "Book deleted successfully!";
header("Location: " . $_SERVER['PHP_SELF']); exit;
}
4. Student 3 – Browse/View Catalog
- Display all books in a table for both librarians and users
- Librarian table includes Edit/Delete buttons; users see view-only
- PHP snippet:
$result = $conn->query("SELECT * FROM books");
while ($row = $result->fetch_assoc()) {
echo "<tr>
<td>{$row['id']}</td>
<td>{$row['title']}</td>
<td>{$row['author']}</td>
<td>{$row['publication_year']}</td>
<td>{$row['isbn']}</td>
</tr>";
}
5. Student 4 – Search Books
- Search form: text field + dropdown to select column
- PHP filters database by selected column and search term
- Notification if no results: "No books found."
- HTML snippet:
<form method="post">
<input type="text" name="to_be_searched" placeholder="Enter keyword" required>
<select name="column">
<option value="id">ID</option>
<option value="title">Title</option>
<option value="author">Author</option>
<option value="publication_year">Publication Year</option>
<option value="isbn">ISBN</option>
</select>
<input type="submit" value="Search">
</form>
- PHP snippet:
$search = $_POST['to_be_searched'];
$column = $_POST['column'];
$allowed_columns = ['id','title','author','publication_year','isbn'];
if (in_array($column, $allowed_columns)) {
$result = $conn->query("SELECT * FROM books WHERE $column LIKE '%$search%'");
}
6. Student 5 – Borrow/Return Books
- Each book row has Borrow or Return button
- PHP updates `borrowings` table
- Notifications:
- Borrow: "Book borrowed successfully!"
- Return: "Book returned successfully!"
- PHP snippet:
// Borrow
$conn->query("INSERT INTO borrowings (book_id, user_name, borrow_date) VALUES (...)");
$_SESSION['message'] = "Book borrowed successfully!";
// Return
$conn->query("UPDATE borrowings SET return_date=NOW() WHERE id=...");
$_SESSION['message'] = "Book returned successfully!";
Librarian Notifications and Behavior
-----------------------------------
- Add book: Shows alert "New Book Added Successfully!".
- Edit book: Shows alert "Book updated successfully!" or error.
- Delete book: Prompts for confirmation. On confirm: "Book deleted successfully!".
- Cancel deletion returns to catalog without changes.
User Notifications and Behavior
-------------------------------
- Search with no result: Shows "No books found."
- Borrow book: Shows "Book borrowed successfully!"
- Return book: Shows "Book returned successfully!"
HTML and Form Structure
-----------------------
1. Add Book Form (librarian.php):
<form method="post">
<input type="text" name="title" placeholder="Enter title" required>
<input type="text" name="author" placeholder="Enter author" required>
<input type="text" name="publication_year" placeholder="Enter year" required>
<input type="text" name="isbn" placeholder="Enter ISBN" required>
<input type="submit" value="Add Book">
</form>
2. Catalog Display:
- Table showing ID, Title, Author, Year, ISBN.
- Each row has Edit and Delete buttons for librarian.
- Users see only book info.
3. Search Form (user.php & librarian.php):
<form method="post">
<input type="text" name="to_be_searched" placeholder="Enter keyword">
<select name="column"> ... </select>
<input type="submit" value="Search">
</form>
4. Borrow/Return Buttons (user.php):
- Each book row has Borrow or Return button.
- Triggers PHP to update borrowings table.
Coding Structure Recommendations
--------------------------------
- Separate HTML from PHP logic.
- Use sessions for logged-in users and notifications.
- Always check request method:
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) { ... }
- Sanitize inputs: $conn->real_escape_string(...)
- Redirect after operations:
header("Location: " . $_SERVER['PHP_SELF']); exit;
GitHub Collaboration Instructions
---------------------------------
1. Repository Setup:
- One member creates the central GitHub repository.
- Clone locally: git clone <repository_url>
- Create develop branch: git checkout -b develop
- Main branch is for production-ready code only.
2. Branching and Task Assignment:
- Each member is assigned a feature:
1. Add new books
2. Edit/remove books
3. Browse/view catalog
4. Search books
5. Borrow/return books
- Before starting, update develop:
git checkout develop
git pull origin develop
- Create personal feature branch:
git checkout -b feature/<your_feature_name>
3. Development and Committing:
- Work on your branch.
- Stage changes: git add .
- Commit: git commit -m "Brief description"
- Push: git push origin feature/<your_feature_name>
4. Pull Requests and Merging:
- Create a PR from feature branch to develop.
- Team reviews code.
- Merge approved code into develop and delete feature branch.
5. Handling Merge Conflicts:
- Git marks conflicts:
<<<<<<< HEAD
your code
=======
other branch code
>>>>>>> branch_name
- Manually edit, remove markers, and stage:
git add <file_name>
- Commit resolution:
git commit -m "Resolve merge conflict"
- Push develop: git push origin develop
6. Finalizing the Project:
- After all features merged, PR from develop → main.
- Main branch becomes production-ready version.
7. Check-In Instructions:
- Bring laptops with internet on check-in date.
- Demonstrate progress directly from GitHub repository.
