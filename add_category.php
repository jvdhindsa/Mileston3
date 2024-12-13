<?php
session_start();
include 'db_connect.php'; // Ensure this file contains the PDO connection to the database.
include 'nav_admin.php';
$message = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];

    try {
        // Insert the category into the database
        $stmt = $pdo->prepare("INSERT INTO Categories (name, description) VALUES (:name, :description)");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description
        ]);
        $message = "Category added successfully!";
    } catch (PDOException $e) {
        $message = "Error adding category: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Add a New Category</h2>
        <?php if ($message): ?>
            <p class="text-center <?php echo strpos($message, 'successfully') !== false ? 'text-green-500' : 'text-red-500'; ?>">
                <?php echo $message; ?>
            </p>
        <?php endif; ?>
        <form method="POST" action="" class="space-y-4">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-600">Category Name</label>
                <input type="text" id="name" name="name" placeholder="Category Name" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
                <textarea id="description" name="description" placeholder="Category Description" 
                          class="w-full mt-1 p-2 border border-gray-300 rounded-lg"></textarea>
            </div>
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Add Category
            </button>
        </form>
    </div>
</body>
</html>
