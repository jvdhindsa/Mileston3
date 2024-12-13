<?php
include 'db_connect.php'; // Include your database connection file

// Handle delete operation
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    try {
        // Prepare and execute the DELETE query
        $delete_stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = :user_id");
        $delete_stmt->bindParam(':user_id', $user_id);
        $delete_stmt->execute();
        
        // Redirect back to the users list after deletion
        header("Location: view_users.php");
        exit();
    } catch (PDOException $e) {
        echo "<div class='text-red-500 font-semibold'>Error deleting user: " . $e->getMessage() . "</div>";
    }
} else {
    echo "<div class='text-red-500 font-semibold'>No user ID provided!</div>";
}
?>
