<?php
session_start();
include 'db_connect.php';

if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Books WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $book_id]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        die("Error deleting book: " . $e->getMessage());
    }
} else {
    header("Location: view_books.php?message=No+book+ID+provided");
    exit();
}
?>
