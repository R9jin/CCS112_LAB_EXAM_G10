<?php
session_start();

// Connect to database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// --- Add new book ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    $title = $conn->real_escape_string($_POST["title"]);
    $author = $conn->real_escape_string($_POST["author"]);
    $year = $conn->real_escape_string($_POST["publication_year"]);
    $isbn = $conn->real_escape_string($_POST["isbn"]);

    $conn->query("INSERT INTO books (title, author, publication_year, isbn) VALUES ('$title', '$author', '$year', '$isbn')");
    $_SESSION['message'] = "New Book Added Successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Delete book ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    $id = $conn->real_escape_string($_POST['id']);
    if ($conn->query("DELETE FROM books WHERE id='$id'")) {
        $_SESSION['message'] = "Book deleted successfully!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Cancel delete ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_delete'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Edit book ---
$edit_form = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_request'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $result = $conn->query("SELECT * FROM books WHERE id='$id'");
    if ($result->num_rows == 1) {
        $book = $result->fetch_assoc();
        $edit_form = "
            <h2>Edit Book</h2>
            <form method='post'>
                <input type='hidden' name='id' value='{$book['id']}'>
                <label>Title:</label><br>
                <input type='text' name='title' value='{$book['title']}' required><br>
                <label>Author:</label><br>
                <input type='text' name='author' value='{$book['author']}' required><br>
                <label>Year:</label><br>
                <input type='text' name='publication_year' value='{$book['publication_year']}' required><br>
                <label>ISBN:</label><br>
                <input type='text' name='isbn' value='{$book['isbn']}' required><br><br>
                <button type='submit' name='save_edit'>Save Changes</button>
            </form>";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_edit'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = $conn->real_escape_string($_POST['publication_year']);
    $isbn = $conn->real_escape_string($_POST['isbn']);

    if ($conn->query("UPDATE books SET title='$title', author='$author', publication_year='$year', isbn='$isbn' WHERE id='$id'")) {
        $_SESSION['message'] = "Book updated successfully!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Return borrowed book ---
if (isset($_GET['return_id'])) {
    $return_id = intval($_GET['return_id']);
    $check = $conn->query("SELECT * FROM borrowings WHERE id=$return_id AND return_date IS NULL");
    if ($check->num_rows > 0) {
        $conn->query("UPDATE borrowings SET return_date = CURDATE() WHERE id=$return_id");
        $_SESSION['message'] = "Book returned successfully!";
    } else {
        $_SESSION['message'] = "This book is not currently borrowed.";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// --- Search books ---
$search_results = "";
if (isset($_POST['search']) && !empty($_POST["to_be_searched"])) {
    $search = $conn->real_escape_string($_POST["to_be_searched"]);
    $column = $_POST["column"];
    $allowed_columns = ['id', 'title', 'author', 'publication_year', 'isbn'];

    if (in_array($column, $allowed_columns)) {
        $result = $conn->query("SELECT * FROM books WHERE $column LIKE '%$search%'");
        if ($result && $result->num_rows > 0) {
            $search_results = "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                <tr>
                    <th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>ISBN</th>
                </tr>";
            while ($row = $result->fetch_assoc()) {
                $search_results .= "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['author']}</td>
                    <td>{$row['publication_year']}</td>
                    <td>{$row['isbn']}</td>
                </tr>";
            }
            $search_results .= "</table>";
        } else {
            $search_results = "No books found.";
        }
    } else {
        $search_results = "Invalid search field.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Library System (Librarian)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<?php
if (isset($_SESSION['message'])) {
    echo "<script>alert('{$_SESSION['message']}');</script>";
    unset($_SESSION['message']);
}
?>

<div class="library-container">
    <h1>ONLINE LIBRARY SYSTEM</h1>
    <h2>Welcome, Librarian</h2>
    <button onclick="window.location.href='index.php'">LOG OUT</button>

    <!-- Add Book -->
    <div class="box">
        <h2>Add Book</h2>
        <form action="" method="post">
            <label>Title:</label><br>
            <input type="text" name="title" required><br>
            <label>Author:</label><br>
            <input type="text" name="author" required><br>
            <label>Year:</label><br>
            <input type="text" name="publication_year" required><br>
            <label>ISBN:</label><br>
            <input type="text" name="isbn" required><br><br>
            <input type="submit" name="add_book" value="Add Book">
        </form>
    </div>

    <!-- Browse Catalog -->
    <div class="box">
        <h2>Browse Catalog</h2>
        <?php
        $result = $conn->query("SELECT id, title, author, publication_year, isbn FROM books");
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                <tr>
                    <th>ID</th><th>Title</th><th>Author</th><th>Year</th><th>ISBN</th><th>Status</th><th>Edit</th><th>Delete</th>
                </tr>";
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['id'];
                $check = $conn->query("SELECT * FROM borrowings WHERE book_id = $book_id AND return_date IS NULL");
                $status = ($check->num_rows > 0) ? "Borrowed" : "Available";
                $title_js = addslashes($row['title']);

                echo "<tr>
                    <td>{$row['id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['author']}</td>
                    <td>{$row['publication_year']}</td>
                    <td>{$row['isbn']}</td>
                    <td>$status</td>
                    <td>
                        <form method='post' style='display:inline;'>
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='edit_request'>Edit</button>
                        </form>
                    </td>
                    <td>
                        <form method='post' style='display:inline;' onsubmit=\"return confirm('Delete: $title_js?');\">
                            <input type='hidden' name='id' value='{$row['id']}'>
                            <button type='submit' name='confirm_delete'>Delete</button>
                        </form>
                    </td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "No books found.";
        }
        ?>
    </div>

    <!-- Edit Book -->
    <div class="box">
        <h2>Edit/Remove Book</h2>
        <?php echo $edit_form ?: "<p>Select a book to edit above.</p>"; ?>
    </div>

    <!-- Search Book -->
    <div class="box">
        <h2>Search Book</h2>
        <form method="post">
            <label>Search term:</label><br>
            <input type="text" name="to_be_searched" required><br><br>
            <label>Search by:</label><br>
            <select name="column" required>
                <option value="id">ID</option>
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="publication_year">Publication Year</option>
                <option value="isbn">ISBN</option>
            </select><br><br>
            <input type="submit" name="search" value="Search">
        </form>
        <?php echo $search_results; ?>
    </div>

    <!-- Borrowed Books -->
    <div class="box">
        <h2>Borrowed Books</h2>
        <?php
        $borrowed = $conn->query("
            SELECT br.id as borrow_id, b.title, br.user_id, br.borrow_date
            FROM borrowings br
            JOIN books b ON br.book_id = b.id
            WHERE br.return_date IS NULL
        ");
        if ($borrowed && $borrowed->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                <tr><th>Book Title</th><th>User ID</th><th>Borrow Date</th><th>Return</th></tr>";
            while ($row = $borrowed->fetch_assoc()) {
                echo "<tr>
                    <td>{$row['title']}</td>
                    <td>{$row['user_id']}</td>
                    <td>{$row['borrow_date']}</td>
                    <td><a href='?return_id={$row['borrow_id']}' onclick='return confirm(\"Return this book?\")'>Return</a></td>
                </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No books are currently borrowed.</p>";
        }
        ?>
    </div>
</div>
</body>
</html>
