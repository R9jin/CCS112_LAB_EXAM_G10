<?php
session_start(); // Start session for login info and notifications

// Connect to the database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get logged-in username from session
$username = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// Redirect if not logged in
if (!$username) {
    header("Location: index.php");
    exit;
}

// ✅ Fetch the logged-in user's ID
$userResult = $conn->query("SELECT id FROM users WHERE username='$username'");
if ($userResult && $userResult->num_rows > 0) {
    $userRow = $userResult->fetch_assoc();
    $user_id = $userRow['id'];
} else {
    die("User not found in database.");
}

// ===========================
// BORROW LOGIC
// ===========================
if (isset($_POST['borrow']) && isset($_POST['book_id'])) {
    $book_id = intval($_POST['book_id']);
    $check = $conn->query("SELECT * FROM borrowings WHERE book_id=$book_id AND return_date IS NULL");
    
    if ($check->num_rows > 0) {
        $_SESSION['message'] = "Book is already borrowed!";
    } else {
        // ✅ FIXED: Use user_id, not user_name
        $conn->query("INSERT INTO borrowings (book_id, user_id, borrow_date) 
                      VALUES ($book_id, $user_id, CURDATE())");
        $_SESSION['message'] = "Book borrowed successfully!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// ===========================
// RETURN LOGIC
// ===========================
if (isset($_GET['return_id'])) {
    $return_id = intval($_GET['return_id']);
    $conn->query("UPDATE borrowings SET return_date=CURDATE() WHERE id=$return_id");
    $_SESSION['message'] = "Book returned successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Library System (User)</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="library-container">

    <!-- Notifications -->
    <?php
    if (isset($_SESSION['message'])) {
        echo "<p>{$_SESSION['message']}</p>";
        unset($_SESSION['message']);
    }
    ?>

    <h1>ONLINE LIBRARY SYSTEM</h1>
    <h2>Welcome, <?= htmlspecialchars($username) ?></h2>

    <!-- LOG OUT button -->
    <form method="post" action="index.php">
        <button type="submit">LOG OUT</button>
    </form>

    <!-- Book Catalog -->
    <div class="box">
        <h3>Book Catalog</h3>
        <?php
        $result = $conn->query("SELECT id, title, author, publication_year, isbn FROM books");
        if ($result && $result->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Year</th>
                        <th>ISBN</th>
                        <th>Status</th>
                    </tr>";
            while ($row = $result->fetch_assoc()) {
                $book_id = $row['id'];
                $check = $conn->query("SELECT * FROM borrowings WHERE book_id=$book_id AND return_date IS NULL");
                $status = ($check->num_rows > 0) ? "Borrowed" : "Available";
                echo "<tr>
                        <td>{$row['id']}</td>
                        <td>{$row['title']}</td>
                        <td>{$row['author']}</td>
                        <td>{$row['publication_year']}</td>
                        <td>{$row['isbn']}</td>
                        <td>$status</td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No books found.</p>";
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
        if (isset($_POST['search']) && !empty($_POST["to_be_searched"])) {
            $search = $_POST["to_be_searched"];
            $column = $_POST["column"];

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
        }
        ?>
    </div>

    <!-- Borrow Section -->
    <div class="box">
        <h3>Borrow a Book</h3>
        <?php
        $available = $conn->query("SELECT * FROM books WHERE id NOT IN
                    (SELECT book_id FROM borrowings WHERE return_date IS NULL)");
        if ($available && $available->num_rows > 0) {
            echo "<ul>";
            while ($row = $available->fetch_assoc()) {
                echo "<li>
                        {$row['title']} 
                        <form method='post' style='display:inline'>
                            <input type='hidden' name='book_id' value='{$row['id']}'>
                            <button type='submit' name='borrow'>Borrow</button>
                        </form>
                    </li>";
            }
            echo "</ul>";
        } else {
            echo "<p>No books available to borrow.</p>";
        }
        ?>
    </div>

    <!-- Your Borrowed Books -->
    <div class="box">
        <h2>Your Borrowed Books</h2>
        <?php
        $borrowed = $conn->query("
            SELECT br.id as borrow_id, b.title, br.borrow_date
            FROM borrowings br
            JOIN books b ON br.book_id = b.id
            WHERE br.return_date IS NULL AND br.user_id = $user_id
        ");
        if ($borrowed && $borrowed->num_rows > 0) {
            echo "<table border='1' cellpadding='5' cellspacing='0' width='100%'>
                    <tr>
                        <th>Book Title</th>
                        <th>Borrow Date</th>
                        <th>Return</th>
                    </tr>";
            while ($row = $borrowed->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['title']}</td>
                        <td>{$row['borrow_date']}</td>
                        <td>
                            <a href='?return_id={$row['borrow_id']}' onclick='return confirm(\"Return this book?\")'>Return</a>
                        </td>
                    </tr>";
            }
            echo "</table>";
        } else {
            echo "<p>You have no borrowed books.</p>";
        }
        ?>
    </div>

</div>
<?php $conn->close(); ?>
</body>
</html>
