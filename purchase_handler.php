<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch book price for total_amount
    try {
        $stmt = $pdo->prepare("SELECT price FROM Books WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $book_id]);
        $book = $stmt->fetch();

        if ($book) {
            $price = $book['price'];
            $quantity = 1; // Default purchase quantity

            // Insert into Sales table
            $stmt = $pdo->prepare("INSERT INTO Sales (book_id, user_id, quantity, total_amount) 
                                   VALUES (:book_id, :user_id, :quantity, :total_amount)");
            $stmt->execute([
                'book_id' => $book_id,
                'user_id' => $user_id,
                'quantity' => $quantity,
                'total_amount' => $price * $quantity
            ]);

            header('Location: sales.php?message=Purchase+successful');
            exit();
        } else {
            die("Book not found!");
        }
    } catch (PDOException $e) {
        die("Error processing purchase: " . $e->getMessage());
    }
} else {
    header('Location: dashboard_user.php');
    exit();
}
?>
