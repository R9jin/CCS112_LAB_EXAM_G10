#CCS112_LAB_EXAM_G10

````markdown
# Simple Library Management System (Web-based)

## Project Overview
This web application allows librarians and users to manage a library catalog efficiently.  
Key functionalities include:
- Add, edit, and delete books (librarian)
- View and search catalog (both librarian and user)
- Borrow and return books (user)
- Notifications after every action
- Tracking borrowing history

## Objectives
1. Apply fundamental concepts of web development: HTML, CSS, JavaScript, PHP.  
2. Perform CRUD operations with a MySQL database.  
3. Collaborate using Git/GitHub with branching, merging, and pull requests.  
4. Demonstrate proper version control and conflict resolution.  

## Database Setup
```sql
-- 1. Create the database
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
````

## Folder and File Structure

```
library_system/
│
├─ index.php       # Login page
├─ librarian.php   # Librarian dashboard
├─ user.php        # User dashboard
├─ style.css       # Styling
├─ db.php          # Database connection
```

## Important Code Snippets

### 1. Database Connection (`db.php`)

```php
$conn = new mysqli("db", "root", "rootpassword", "librarydb");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
```

---

### 2. Student 1 – Add New Books (Librarian Only)

* **Form fields**: Title, Author, Publication Year, ISBN
* **Notification**: `New Book Added Successfully!`

**HTML Form**

```html
<form method="post">
  <input type="text" name="title" placeholder="Enter title" required>
  <input type="text" name="author" placeholder="Enter author" required>
  <input type="text" name="publication_year" placeholder="Enter year" required>
  <input type="text" name="isbn" placeholder="Enter ISBN" required>
  <input type="submit" value="Add Book">
</form>
```

**PHP Insert**

```php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['title'])) {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $publication_year = $_POST['publication_year'];
    $isbn = $_POST['isbn'];

    $conn->query("INSERT INTO books (title, author, publication_year, isbn)
                  VALUES ('$title','$author','$publication_year','$isbn')");

    $_SESSION['message'] = "New Book Added Successfully!";
    header("Location: " . $_SERVER['PHP_SELF']); 
    exit;
}
```

---

### 3. Student 2 – Edit/Remove Books (Librarian Only)

* **Notifications**:

  * Edit → `Book updated successfully!`
  * Delete → `Book deleted successfully!`

**HTML Table**

```html
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
      <button type="submit" name="delete_request">Delete</button>
    </form>
  </td>
</tr>
</table>
```

**PHP Update/Delete** 

```php
// Edit
$id = $_POST['id'];
$result = $conn->query("SELECT * FROM books WHERE id='$id'");
// On form submit:
$conn->query("UPDATE books SET title='$title', author='$author', publication_year='$year', isbn='$isbn' WHERE id='$id'");
$_SESSION['message'] = "Book updated successfully!";
header("Location: " . $_SERVER['PHP_SELF']); exit;

// Delete
if (isset($_POST['confirm_delete'])) {
    $conn->query("DELETE FROM books WHERE id='$id'");
    $_SESSION['message'] = "Book deleted successfully!";
    header("Location: " . $_SERVER['PHP_SELF']); exit;
}
```

---

### 4. Student 3 – Browse/View Catalog (Librarian and User)

**PHP Display**

```php
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
```

---

### 5. Student 4 – Search Books

* **Notification if no results**: `No books found.`

**HTML Form**

```html
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
```

**PHP Search**

```php
$search = $_POST['to_be_searched'];
$column = $_POST['column'];
$allowed_columns = ['id','title','author','publication_year','isbn'];

if (in_array($column, $allowed_columns)) {
    $result = $conn->query("SELECT * FROM books WHERE $column LIKE '%$search%'");
}
```

---

### 6. Student 5 – Borrow/Return Books (Librarian Only)

* **Notifications**:

  * Borrow → `Book borrowed successfully!`
  * Return → `Book returned successfully!`

**PHP Snippet**

```php
// Borrow
$conn->query("INSERT INTO borrowings (book_id, user_name, borrow_date)
              VALUES (...)");
$_SESSION['message'] = "Book borrowed successfully!";

// Return
$conn->query("UPDATE borrowings SET return_date=NOW() WHERE id=...");
$_SESSION['message'] = "Book returned successfully!";
```

---

## Notifications and Behavior

### Librarian

* Add book → alert `"New Book Added Successfully!"`
* Edit book → alert `"Book updated successfully!"`
* Delete book → prompt confirm → `"Book deleted successfully!"`
* Cancel delete → return to catalog with no change

### User

* Search with no result → `"No books found."`
* Borrow → `"Book borrowed successfully!"`
* Return → `"Book returned successfully!"`

---

## HTML and Form Structure

### Add Book Form (librarian.php)

```html
<form method="post">
  <input type="text" name="title" placeholder="Enter title" required>
  <input type="text" name="author" placeholder="Enter author" required>
  <input type="text" name="publication_year" placeholder="Enter year" required>
  <input type="text" name="isbn" placeholder="Enter ISBN" required>
  <input type="submit" value="Add Book">
</form>
```

### Catalog Display

* Table shows **ID, Title, Author, Year, ISBN**
* Librarian → Edit/Delete buttons
* User → View-only

### Search Form

```html
<form method="post">
  <input type="text" name="to_be_searched" placeholder="Enter keyword">
  <select name="column"> ... </select>
  <input type="submit" value="Search">
</form>
```

### Borrow/Return Buttons

* Each book row has Borrow/Return
* Calls PHP → updates `borrowings` table

---

## Coding Structure Recommendations

* Separate **HTML from PHP logic**
* Use **sessions** for login + notifications
* Always check request method:

```php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) { ... }
```

* Sanitize inputs:

```php
$conn->real_escape_string(...)
```

* Redirect after operations:

```php
header("Location: " . $_SERVER['PHP_SELF']); exit;
```

---

## GitHub Collaboration Instructions

### 1. Repository Setup

* One member creates central repo
* Clone locally:

  ```bash
  git clone <repository_url>
  ```
* Create `develop` branch:

  ```bash
  git checkout -b develop
  ```
* `main` branch → production-ready only

### 2. Branching & Task Assignment

Each student works on:

1. Add books
2. Edit/remove books
3. Browse/view catalog
4. Search books
5. Borrow/return books

Before starting:

```bash
git checkout develop
git pull origin develop
```

Create feature branch:

```bash
git checkout -b feature/<your_feature>
```

### 3. Development & Committing

```bash
git add .
git commit -m "Describe changes"
git push origin feature/<your_feature>
```

### 4. Pull Requests & Merging

* PR from feature → develop
* Team review → approve → merge
* Delete feature branch

### 5. Handling Merge Conflicts

Git marks conflicts:

```diff
<<<<<<< HEAD
your code
=======
other branch code
>>>>>>> branch_name
```

Resolve → stage → commit:

```bash
git add <file_name>
git commit -m "Resolve merge conflict"
git push origin develop
```

### 6. Finalizing the Project

Make sure `develop` is updated:

```bash
git checkout develop
git pull origin develop
Switch to main and update:
```

```bash
git checkout main
git pull origin main
Push both branches to GitHub:
```

```bash
git push origin develop
git push origin main
Open a Pull Request on GitHub to merge develop → main.
main branch should always stay production-ready.
```

