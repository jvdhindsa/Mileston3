<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];
    
    // Delete the comment from the database
    $stmt = $pdo->prepare("DELETE FROM Comments WHERE comment_id = :comment_id");
    $stmt->execute(['comment_id' => $comment_id]);

    // Redirect back to the comments page
    header('Location: comments.php');
    exit();
}
?>
