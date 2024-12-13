<?php
session_start();
include 'db_connect.php'; // Ensure this file contains the PDO connection to the database.
include 'nav_admin.php'; // Include your navigation bar.

$message = "";

// Handle delete request
if (isset($_GET['delete'])) {
    try {
        $category_id = $_GET['delete'];
        
        // Delete the category
        $stmt = $pdo->prepare("DELETE FROM Categories WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $message = "Category deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error deleting category: " . $e->getMessage();
    }
}

try {
    // Fetch all categories from the database
    $stmt = $pdo->prepare("SELECT * FROM Categories ORDER BY category_id ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching categories: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Categories</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">All Categories</h2>

        <?php if ($message): ?>
            <p class="text-red-500 text-center mb-4"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if (!empty($categories)): ?>
            <table class="min-w-full bg-white border border-gray-300 rounded-lg">
                <thead>
                    <tr>
                        <th class="px-4 py-2 border-b text-left">ID1</th>
                        <th class="px-4 py-2 border-b text-left">Name1</th>
                        <th class="px-4 py-2 border-b text-left">Description</th>
                        <th class="px-4 py-2 border-b text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($category['category_id']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($category['name']); ?></td>
                            <td class="px-4 py-2 border-b"><?php echo htmlspecialchars($category['description']); ?></td>
                            <td class="px-4 py-2 border-b">
                                <!-- Edit and Delete buttons -->
                                <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a> |
                                <a href="?delete=<?php echo $category['category_id']; ?>" 
                                   class="text-red-500 hover:text-red-700"
                                   onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="text-gray-500 text-center">No categories found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
