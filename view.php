<?php
session_start();
include 'db_connect.php';
include 'nav.php';

try {
    $stmt = $pdo->query("SELECT * FROM Books");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
    $error = "Error fetching books: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="navbar">
    <a href="index.php">Dashboard</a>
    <a href="create.php">Add Book</a>
    <a href="read.php">View Books</a>
    <a href="logout.php">Logout</a>
</div>
    <h2>Books in Inventory</h2>
    <?php if (isset($error)) echo "<p>$error</p>"; ?>
    <table border="1">
        <tr>
            <th>Book ID</th>
            <th>Title</th>
            <th>Author</th>
            <th>Genre</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Publication Date</th>
        </tr>
        <?php foreach ($books as $book): ?>
        <tr>
            <td><?php echo htmlspecialchars($book['book_id']); ?></td>
            <td><?php echo htmlspecialchars($book['title']); ?></td>
            <td><?php echo htmlspecialchars($book['author']); ?></td>
            <td><?php echo htmlspecialchars($book['genre']); ?></td>
            <td><?php echo htmlspecialchars($book['price']); ?></td>
            <td><?php echo htmlspecialchars($book['stock_quantity']); ?></td>
            <td><?php echo htmlspecialchars($book['publication_date'] ?? 'N/A'); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>

