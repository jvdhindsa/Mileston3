<?php
session_start();
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM Categories WHERE category_id = :id");
        $stmt->execute(['id' => $id]);
        $_SESSION['message'] = "Category deleted successfully!";
    } catch (PDOException $e) {
        $_SESSION['message'] = "Error deleting category: " . $e->getMessage();
    }
    header('Location: view_categories.php');
    exit;
}
?>
