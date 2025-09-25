<?php
session_start();

// Connect to the database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===========================
   BORROW LOGIC
   =========================== */
if (isset($_GET['borrow_id'])) {
    $book_id = intval($_GET['borrow_id']);
    $book = $conn->query("SELECT * FROM books WHERE id=$book_id")->fetch_assoc();

    if (isset($_POST['borrow'])) {
        $user_name = $_POST['user_name'];
        $check = $conn->query("SELECT * FROM borrowings WHERE book_id=$book_id AND return_date IS NULL");

        if ($check->num_rows > 0) {
            $_SESSION['message'] = "Book is already borrowed!";
        } else {
            $conn->query("INSERT INTO borrowings (book_id, user_name, borrow_date) VALUES ($book_id, '$user_name', CURDATE())");
            $_SESSION['message'] = "Book borrowed successfully!";
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Library Catalog</title>
    <link rel="stylesheet" href="staile.css">
</head>
<body>
    <h1>LIBRARY SYSTEM</h1>
    <h4>User Panel</h4>

    <!-- Notifications -->
    <?php
    if (isset($_SESSION['message'])) {
        echo "<script>alert('{$_SESSION['message']}');</script>";
        unset($_SESSION['message']);
    }
    ?>

    <!-- View Catalog -->
    <div>
        <h2>Book Catalog</h2>
        <?php
        $result = $conn->query("SELECT * FROM books");

        if ($result && $result->num_rows > 0) {
            echo "
            <table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>ID</th>
                    <th>TITLE</th>
                    <th>AUTHOR</th>
                    <th>YEAR</th>
                    <th>ISBN</th>
                    <th>Status</th>
                </tr>
            ";

            while ($row = $result->fetch_assoc()) {
                $book_id = $row['id'];
                $check = $conn->query("SELECT * FROM borrowings WHERE book_id=$book_id AND return_date IS NULL");
                $is_borrowed = $check->num_rows > 0;

                echo "
                <tr>
                    <td>{$row['id']}</td>
                    <td>{$row['title']}</td>
                    <td>{$row['author']}</td>
                    <td>{$row['publication_year']}</td>
                    <td>{$row['isbn']}</td>
                    <td>" . ($is_borrowed ? "Borrowed" : "Available") . "</td>
                </tr>
                ";
            }

            echo "</table>";
        } else {
            echo "No books found.";
        }
        ?>
    </div>

    <!-- Search -->
    <div>
        <h2>HEHEHEH TRY BOISD</h2>
    </div>

    <!-- Borrow Section -->
    <!-- Borrow Section -->
<div>
    <h2>Borrow a Book</h2>
    <style>
        .book-card {
            display: inline-block;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            width: 200px;
            text-align: center;
            background: #f8f8f8;
            cursor: pointer;
            transition: 0.3s;
        }
        .book-card:hover {
            background: #e0e0e0;
            transform: scale(1.05);
        }
        .book-link {
            text-decoration: none;
            color: #000;
            font-weight: bold;
        }
    </style>

    <?php if (!isset($_GET['borrow_id'])): ?>
        <div style="display:flex; flex-wrap:wrap;">
            <?php
            $available = $conn->query("SELECT * FROM books WHERE id NOT IN 
                (SELECT book_id FROM borrowings WHERE return_date IS NULL)");
            if ($available->num_rows > 0):
                while ($row = $available->fetch_assoc()): ?>
                    <a class="book-link" href="?borrow_id=<?= $row['id'] ?>">
                        <div class="book-card">
                            <?= $row['title'] ?>
                        </div>
                    </a>
                <?php endwhile;
            else:
                echo "<p>No books available to borrow.</p>";
            endif;
            ?>
        </div>
    <?php else: ?>
        <?php if ($book): ?>
            <div style="border:1px solid #444; padding:20px; border-radius:10px; width:400px; background:#fafafa;">
                <h3><?= $book['title'] ?></h3>
                <p><strong>Author:</strong> <?= $book['author'] ?></p>
                <p><strong>Year:</strong> <?= $book['publication_year'] ?></p>
                <p><strong>ISBN:</strong> <?= $book['isbn'] ?></p>

                <form method="post">
                    <label>Your Name: </label>
                    <input type="text" name="user_name" required>
                    <button type="submit" name="borrow">Confirm Borrow</button>
                </form>

                <p><a href="<?= $_SERVER['PHP_SELF'] ?>">Back to Available Books</a></p>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>
<br><br>
    <a href="sign_in.php">LOG OUT</a>
</body>
</html>
<?php $conn->close(); ?>
