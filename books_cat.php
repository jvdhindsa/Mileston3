<?php
// db_connect.php to include the database connection
include 'db_connect.php';
include 'nav.php';
// Fetch distinct categories from the Books table
$categories_stmt = $pdo->query("SELECT DISTINCT genre FROM Books");
$categories = $categories_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Selection</title>
    <!-- Add Tailwind CSS for styling -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">


<!-- Category Menu -->
<div class="container mx-auto p-4 text-center">
    <h2 class="text-3xl font-bold mb-6">Select a Category</h2>

    <div class="flex justify-center space-x-6">
        <?php foreach ($categories as $category): ?>
            <a href="category.php?category=<?php echo urlencode($category['genre']); ?>"
               class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition">
                <?php echo htmlspecialchars($category['genre']); ?>
            </a>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>
