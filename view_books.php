<?php
session_start();
include 'db_connect.php';
include 'nav_admin.php';

try {
    $stmt = $pdo->query("SELECT * FROM Books");
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $books = [];
    $error = "Error fetching books: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .navbar {
            background-color: #333;
        }
    </style>
</head>
<body class="bg-gray-100">

 

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto p-6">
        <h2 class="text-3xl font-semibold text-gray-800 mb-6">Books in Inventory</h2>
        <?php if (isset($error)) echo "<p class='text-red-500'>$error</p>"; ?>

        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="px-6 py-3 text-left">Book ID</th>
                    <th class="px-6 py-3 text-left">Title</th>
                    <th class="px-6 py-3 text-left">Author</th>
                    <th class="px-6 py-3 text-left">Genre</th>
                    <th class="px-6 py-3 text-left">Price</th>
                    <th class="px-6 py-3 text-left">Stock</th>
                    <th class="px-6 py-3 text-left">Publication Date</th>
                    <th class="px-6 py-3 text-left">Actions</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php foreach ($books as $book): ?>
                <tr class="border-t border-gray-200">
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['book_id']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['title']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['author']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['genre']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['price']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['stock_quantity']); ?></td>
                    <td class="px-6 py-3"><?php echo htmlspecialchars($book['publication_date'] ?? 'N/A'); ?></td>
                    <td class="px-6 py-3">
                        <div class="flex gap-2">
                            <!-- Edit Button -->
                            <form method="GET" action="edit_book.php">
                                <input type="hidden" name="book_id" value="<?php echo $book['book_id']; ?>">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white py-2 px-4 rounded-lg">Edit</button>
                            </form>
                            <!-- Delete Button -->
                            <button onclick="confirmDelete(<?php echo $book['book_id']; ?>)" class="bg-red-500 hover:bg-red-600 text-white py-2 px-4 rounded-lg">Delete</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function confirmDelete(bookId) {
            if (confirm("Are you sure you want to delete this book?")) {
                window.location.href = "delete_book.php?book_id=" + bookId;
            }
        }
    </script>
</body>
</html>
