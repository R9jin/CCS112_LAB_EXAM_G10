<?php
session_start();

// Connect to the database
$conn = new mysqli("db", "root", "rootpassword", "librarydb", 3306);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/* ===========================
BORROW / RETURN (Placeholder)
   =========================== */
// TODO: Students will implement this feature (for users only).
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
    if ($result->num_rows > 0) {
        echo "<table border='1' cellpadding='5' cellspacing='0'>
                <tr>
                    <th>ID</th>
                    <th>TITLE</th>
                    <th>AUTHOR</th>
                    <th>YEAR</th>
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
</body>
</html>
