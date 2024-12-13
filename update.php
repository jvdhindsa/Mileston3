<?php
session_start();
include 'db_connect.php';
include 'nav.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $publication_date = $_POST['publication_date'];

    try {
        $stmt = $pdo->prepare("UPDATE Books SET title = :title, author = :author, genre = :genre, price = :price, 
                               stock_quantity = :stock_quantity, publication_date = :publication_date 
                               WHERE book_id = :book_id");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'price' => $price,
            'stock_quantity' => $stock_quantity,
            'publication_date' => $publication_date,
            'book_id' => $book_id
        ]);
        $message = "Book updated successfully!";
    } catch (PDOException $e) {
        $message = "Error updating book: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Book</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="navbar">
    <a href="index.php">Dashboard</a>
    <a href="create.php">Add Book</a>
    <a href="read.php">View Books</a>
    <a href="logout.php">Logout</a>
</div>
    <h2>Update Book</h2>
    <?php if (isset($message)) echo "<p>$message</p>"; ?>
    <form method="POST" action="">
        <input type="number" name="book_id" placeholder="Book ID" required>
        <input type="text" name="title" placeholder="Book Title" required>
        <input type="text" name="author" placeholder="Author" required>
        <input type="text" name="genre" placeholder="Genre" required>
        <input type="number" name="price" placeholder="Price" required>
        <input type="number" name="stock_quantity" placeholder="Stock Quantity" required>
        <input type="date" name="publication_date" placeholder="Publication Date">
        <button type="submit">Update</button>
    </form>
</body>
</html>
