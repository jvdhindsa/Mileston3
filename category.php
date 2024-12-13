<?php
session_start();
include 'db_connect.php';
include 'nav.php';
// Get the category from the URL
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Fetch books for the selected category
$books = [];
if ($category != '') {
    try {
        $stmt = $pdo->prepare("SELECT * FROM Books WHERE genre = :genre");
        $stmt->bindValue(':genre', $category);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching books: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books in <?php echo htmlspecialchars($category); ?> Category</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">



<div class="container mx-auto p-4">
    <h2 class="text-3xl font-bold text-center mb-8">Books in "<?php echo htmlspecialchars($category); ?>" Category</h2>

    <!-- Display Books -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                    <a href="book_details.php?book_id=<?php echo $book['book_id']; ?>">
                        <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image" class="w-full h-64 object-cover rounded-lg mb-4">
                    </a>
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-sm mb-2"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="text-sm mb-2"><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                    <p class="text-sm mb-2"><strong>Stock:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?></p>
                    <p class="text-sm mb-4"><strong>Published on:</strong> <?php echo htmlspecialchars($book['publication_date']); ?></p>
                    <button onclick="confirmPurchase(<?php echo $book['book_id']; ?>)" class="bg-blue-500 text-white py-2 px-4 rounded-lg w-full hover:bg-blue-600 transition">Buy Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="col-span-full text-center text-lg text-gray-500">No books found in this category.</p>
        <?php endif; ?>
    </div>
</div>

<script>
    function confirmPurchase(bookId) {
        if (confirm("Do you want to buy this book?")) {
            // Redirect to purchase handler
            window.location.href = `purchase_handler.php?book_id=${bookId}`;
        }
    }
</script>

</body>
</html>
