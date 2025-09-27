<?php
session_start();

// Connect to the database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===========================
CREATE NEW BOOK (Placeholder)
   =========================== */
// TODO: Student 1 will implement this feature.
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
    // Placeholder logic
    $_SESSION['message'] = "Add book feature not yet implemented.";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* ===========================
EDIT / REMOVE BOOK
   =========================== */

// Delete → Ask for confirmation
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_request'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];

    echo "<p>Are you sure you want to delete the book: <b>$title</b>?</p>";
    echo "<form method='post'>
            <input type='hidden' name='id' value='$id'>
            <button type='submit' name='confirm_delete'>Yes, Delete</button>
            <button type='submit' name='cancel_delete'>Cancel</button>
        </form>";
    exit;
}

// Delete → Confirm
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['confirm_delete'])) {
    $id = $conn->real_escape_string($_POST['id']);
    if ($conn->query("DELETE FROM books WHERE id='$id'")) {
        $_SESSION['message'] = "Book deleted successfully!";
    } else {
        $_SESSION['message'] = "Error deleting book!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Delete → Cancel
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_delete'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Edit → Request edit form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_request'])) {
    $id = $_POST['id'];

    $result = $conn->query("SELECT * FROM books WHERE id = '$id'");
    if ($result->num_rows == 1) {
        $book = $result->fetch_assoc();

        // Pre-filled form
        echo "<h2>Edit Book</h2>
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
        exit;
    }
}

// Edit → Save changes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_edit'])) {
    $id = $conn->real_escape_string($_POST['id']);
    $title = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year = $conn->real_escape_string($_POST['publication_year']);
    $isbn = $conn->real_escape_string($_POST['isbn']);

    if ($conn->query("UPDATE books SET title='$title', author='$author', publication_year='$year', isbn='$isbn' WHERE id='$id'")) {
        $_SESSION['message'] = "Book updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating book!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

/* ===========================
BROWSE / VIEW CATALOG
   =========================== */
// Implemented in the HTML section below

/* ===========================
SEARCH (Placeholder)
   =========================== */
// TODO: Student 4 will implement this feature.

/* ===========================
BORROW / RETURN (Placeholder)
   =========================== */
// TODO: Student 5 will implement this feature.
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Librarian - Features</title>
    <link rel="stylesheet" href="staile.css">
</head>
<body>
<div class="container">
    <h1>LIBRARY SYSTEM</h1>
    <h4>Librarian Panel</h4>

    <!-- Notifications -->
    <?php
    if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- Create New Book -->
    <div>
        <h2>Add New Book (Coming Soon)</h2>
        <form method="post">
            <input type="hidden" name="add_book" value="1">
            <button type="submit">+ Add Book</button>
        </form>
    </div>

    <!-- Edit/Remove -->
    <div>
        <h2>Catalog (Edit/Remove)</h2>
        <?php
        $result = $conn->query("SELECT * FROM books");
        if ($result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0'>
                    <tr>
                        <th>ID</th>
                        <th>TITLE</th>
                        <th>AUTHOR</th>
                        <th>YEAR</th>
                        <th>ISBN</th>
                        <th>EDIT</th>
                        <th>DELETE</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['publication_year']}</td>
                        <td>{$row['isbn']}</td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' name='edit_request'>Edit</button>
                            </form>
                        </td>
                        <td>
                            <form method='post' style='display:inline;'>
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <input type='hidden' name='title' value='{$row['title']}'>
                                <button type='submit' name='delete_request'>Delete</button>
                            </form>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "No books found.";
        }
        $conn->close();
        ?>
    </div>

    <!-- Search -->
    <div>
        <h2>Search (Coming Soon)</h2>
    </div>

    <!-- Borrow/Return -->
    <div>
        <h2>Borrow/Return (Coming Soon)</h2>
    </div>

    <a href="sign_in.php">LOG OUT</a>
</div>
</body>
</html>
