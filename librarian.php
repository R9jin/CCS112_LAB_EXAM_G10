<?php
session_start(); // start session for messages

// connect to database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// add new book when form submitted
if (
    $_SERVER["REQUEST_METHOD"] == "POST"
    && isset($_POST["title"], $_POST["author"], $_POST["publication_year"], $_POST["isbn"])
    && !isset($_POST['delete_request'])
    && !isset($_POST['confirm_delete'])
) {
    $title = $_POST["title"];
    $author = $_POST["author"];
    $publication_year = $_POST["publication_year"];
    $isbn = $_POST["isbn"];

    // insert book into database
    if ($conn->query("INSERT INTO books (title, author, publication_year, isbn)
                        VALUES ('$title', '$author', '$publication_year', '$isbn')")) {
        $_SESSION['message'] = "New Book Added Successfully!";
    } else {
        $_SESSION['message'] = "Error: Something went wrong!";
    }

    // reload page to show message
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ask for delete confirmation
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

// delete the book if confirmed
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

// cancel delete, just reload page
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_delete'])) {
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// load edit form with existing book data
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_request'])) {
    $id = $_POST['id'];
    $result = $conn->query("SELECT * FROM books WHERE id = '$id'");
    if ($result->num_rows == 1) {
        $book = $result->fetch_assoc();

        // create edit form
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

// save edited book
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save_edit'])) {
    $id     = $conn->real_escape_string($_POST['id']);
    $title  = $conn->real_escape_string($_POST['title']);
    $author = $conn->real_escape_string($_POST['author']);
    $year   = $conn->real_escape_string($_POST['publication_year']);
    $isbn   = $conn->real_escape_string($_POST['isbn']);

    if ($conn->query("UPDATE books SET title='$title', author='$author', publication_year='$year', isbn='$isbn' WHERE id='$id'")) {
        $_SESSION['message'] = "Book updated successfully!";
    } else {
        $_SESSION['message'] = "Error updating book!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// placeholder for search results
$search_results = "";
if (!empty($_GET['search'])) {
    $field = $_GET['field'];
    $keyword = $_GET['keyword'];
    $allowed = ['title','author','publication_year','isbn'];

    if (in_array($field, $allowed, true)) {
        $keyword = $conn->real_escape_string($keyword);
        $result = $conn->query("SELECT * FROM books WHERE $field LIKE '%$keyword%'");

        if ($result && $result->num_rows) {
            $search_results = "<table border='1' cellpadding='5' cellspacing='0'>
                                <tr>
                                    <th>ID</th>
                                    <th>Title</th>
                                    <th>Author</th>
                                    <th>Year</th>
                                    <th>ISBN</th>
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
            $search_results = "No matching books found.";
        }
    } else {
        $search_results = "Invalid search field.";
    }
}

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
// show notification if message exists
if (isset($_SESSION['message'])) {
    echo "<script>alert('{$_SESSION['message']}');</script>";
    unset($_SESSION['message']);
}
?>

<div class="library-container">
    <h1>ONLINE LIBRARY SYSTEM</h1>
    <h2>Welcome, Librarian</h2>

    <!-- logout button -->
    <button onclick="window.location.href='index.php'">LOG OUT</button>

    <!-- add new book form -->
    <div class="box">
        <h2>Add Book</h2>
        <form action="" method="post">
            <label for="title">Title:</label><br>
            <input type="text" size="50" id="title" name="title" placeholder="Enter title" required><br>

            <label for="author">Author:</label><br>
            <input type="text" size="50" id="author" name="author" placeholder="Enter author" required><br>

            <label for="publication_year">Year:</label><br>
            <input type="text" size="50" id="publication_year" name="publication_year" placeholder="Enter publication year" required><br>

            <label for="isbn">ISBN:</label><br>
            <input type="text" size="50" id="isbn" name="isbn" placeholder="Enter isbn" required><br><br>

            <input type="submit" name="add_book" value="Add Book">
        </form>
    </div>

    <!-- browse all books -->
    <div class="box">
        <h2>Browse Catalog</h2>
        <?php
        // get all books
        $result = $conn->query("SELECT id, title, author, publication_year, isbn FROM books");
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                    <tr>
                        <th>ID</th>
                        <th>TITLE</th>
                        <th>AUTHOR</th>
                        <th>YEAR</th>
                        <th>ISBN</th>
                        <th>STATUS</th>
                        <th>EDIT</th>
                        <th>DELETE</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['id'];

                // check if book is borrowed
                $check = $conn->query("SELECT * FROM borrowings WHERE book_id = $book_id AND return_date IS NULL");
                $status = ($check->num_rows > 0) ? "Borrowed" : "Available";

                $title_js = addslashes($row['title']);

                // show book row
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
                                <button type='submit' name='edit_request' style='width: 100%;'>Edit</button>
                            </form>
                        </td>
                        <td>
                            <form method='post' style='display:inline;' onsubmit=\"return confirm('Are you sure you want to delete the book: $title_js?');\">
                                <input type='hidden' name='id' value='{$row['id']}'>
                                <button type='submit' name='confirm_delete' style='width: 100%;'>Delete</button>
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

    <!-- edit or remove selected book -->
    <div class="box">
        <h2>Edit/Remove Book</h2>
        <?php
        if (!empty($edit_form)) {
            echo $edit_form;
        } else {
            echo "<p>Select a book to edit above.</p>";
        }
        ?>
    </div>

    <!-- search books -->
    <div class="box">
        <h2>Search Book</h2>
        <form action="" method="post">
            <label for="to_be_searched">Search term:</label><br>
            <input type="text" size="50" id="to_be_searched" name="to_be_searched" placeholder="Enter keyword" required><br><br>

            <label for="column">Search by:</label><br>
            <select name="column" id="column" required>
                <option value="id">ID</option>
                <option value="title">Title</option>
                <option value="author">Author</option>
                <option value="publication_year">Publication Year</option>
                <option value="isbn">ISBN</option>
            </select><br><br>

            <input type="submit" name="search" value="Search">
        </form>

        <?php
        // handle search form
        if (isset($_POST['search']) && !empty($_POST["to_be_searched"])) {
            $search = $_POST["to_be_searched"];
            $column = $_POST["column"];

            $conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
            if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

            $allowed_columns = ['id', 'title', 'author', 'publication_year', 'isbn'];
            if (!in_array($column, $allowed_columns)) die("Invalid column selected.");

            $search_escaped = $conn->real_escape_string($search);
            $result = $conn->query("SELECT * FROM books WHERE $column LIKE '%$search_escaped%'");

            if ($result && $result->num_rows > 0) {
                echo "<br><table border='1' cellpadding='5' cellspacing='0' width='100%'>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Author</th>
                            <th>Publication Year</th>
                            <th>ISBN</th>
                        </tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['id']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['author']}</td>
                            <td>{$row['publication_year']}</td>
                            <td>{$row['isbn']}</td>
                        </tr>";
                }
                echo "</table>";
            } else {
                echo "No books found.";
            }

            $conn->close();
        }
        ?>
    </div>

    <div class="box">
    <h2>Borrowed Books</h2>
    <?php
    $borrowed = $conn->query("
        SELECT br.id as borrow_id, b.title, br.user_name, br.borrow_date
        FROM borrowings br
        JOIN books b ON br.book_id = b.id
        WHERE br.return_date IS NULL
    ");

    if ($borrowed && $borrowed->num_rows > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                <tr>
                    <th>Book Title</th>
                    <th>Borrower</th>
                    <th>Borrow Date</th>
                    <th>Return</th>
                </tr>";
        while ($row = $borrowed->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['title']}</td>
                    <td>{$row['user_name']}</td>
                    <td>{$row['borrow_date']}</td>
                    <td>
                        <a href='?return_id={$row['borrow_id']}' onclick='return confirm(\"Return this book?\")'>Return</a>
                    </td>
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
