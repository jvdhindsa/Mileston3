<?php
session_start();
include 'db_connect.php'; // Ensure this file contains the PDO connection to the database.
include 'nav_admin.php'; // Include your navigation bar.

$message = "";

// Handle update request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['category_id'])) {
    try {
        $category_id = $_POST['category_id'];
        $name = $_POST['name'];
        $description = $_POST['description'];

        // Update the category
        $stmt = $pdo->prepare("UPDATE Categories SET name = :name, description = :description WHERE category_id = :category_id");
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();

        $message = "Category updated successfully!";
        header("Location: categories.php");
    } catch (PDOException $e) {
        $message = "Error updating category: " . $e->getMessage();
    }
}

if (isset($_GET['id'])) {
    $category_id = $_GET['id'];

    try {
        // Fetch category details
        $stmt = $pdo->prepare("SELECT * FROM Categories WHERE category_id = :category_id");
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->execute();
        $category = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $message = "Error fetching category details: " . $e->getMessage();
    }
} else {
    // Redirect if no category ID is passed
    header('Location: view_categories.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Edit Category</h2>

        <?php if ($message): ?>
            <p class="text-green-500 text-center mb-4"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($category): ?>
            <form method="POST" action="">
                <input type="hidden" name="category_id" value="<?php echo $category['category_id']; ?>">
                <div class="mb-4">
                    <label for="name" class="block text-gray-700">Category Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-md" required>
                </div>
                <div class="mb-4">
                    <label for="description" class="block text-gray-700">Description</label>
                    <textarea id="description" name="description" class="w-full px-4 py-2 border border-gray-300 rounded-md" required><?php echo htmlspecialchars($category['description']); ?></textarea>
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-md">Update Category</button>
            </form>
        <?php else: ?>
            <p class="text-gray-500 text-center">Category not found.</p>
        <?php endif; ?>
    </div>
</body>
</html>
