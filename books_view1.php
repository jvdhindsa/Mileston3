<?php
session_start();
include 'db_connect.php';

// Number of books per page
$books_per_page = 8;

// Get the current page number from the query string (default to 1 if not set)
$current_page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($current_page - 1) * $books_per_page;

// Search functionality
$search_query = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_query = $_POST['search'];
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE title LIKE :title LIMIT :offset, :books_per_page");
    $stmt->bindValue(':title', '%' . $search_query . '%');
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':books_per_page', $books_per_page, PDO::PARAM_INT);
} else {
    // Fetch books for the current page
    $stmt = $pdo->prepare("SELECT * FROM Books LIMIT :offset, :books_per_page");
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':books_per_page', $books_per_page, PDO::PARAM_INT);
}

$stmt->execute();
$books = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of books to calculate total pages
$stmt = $pdo->query("SELECT COUNT(*) FROM Books");
$total_books = $stmt->fetchColumn();
$total_pages = ceil($total_books / $books_per_page);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Inventory</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">

<div class="bg-gray-800 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
        <a href="dashboard_user.php" class="text-xl font-semibold">Dashboard</a>
    </div>
</div>

<div class="container mx-auto p-4">
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</h1>
        <p class="text-lg">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'None'); ?></p>
        <a href="logout.php" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">Logout</a>
    </div>

    <h2 class="text-2xl font-semibold text-center mt-8 mb-6">Books Inventory</h2>

    <!-- Search Form -->
    <div class="flex justify-center mb-8">
        <form method="POST" action="" class="flex items-center space-x-2">
            <input type="text" name="search" placeholder="Search by book name" value="<?php echo htmlspecialchars($search_query); ?>"
                class="p-2 border border-gray-300 rounded-lg w-1/2 md:w-1/3" />
            <button type="submit" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600">Search</button>
        </form>
    </div>

    <!-- Book Table -->
    <div class="overflow-x-auto shadow-md rounded-lg bg-white">
        <table class="min-w-full table-auto">
            <thead>
                <tr class="bg-gray-200">
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Author</th>
                    <th class="py-2 px-4 border-b">Genre</th>
                    <th class="py-2 px-4 border-b">Price</th>
                    <th class="py-2 px-4 border-b">Stock</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($book['title']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($book['author']); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($book['genre']); ?></td>
                        <td class="py-2 px-4 border-b">$<?php echo number_format($book['price'], 2); ?></td>
                        <td class="py-2 px-4 border-b"><?php echo htmlspecialchars($book['stock_quantity']); ?></td>
                        <td class="py-2 px-4 border-b">
                            <button class="bg-blue-500 text-white py-1 px-4 rounded hover:bg-blue-600" onclick="confirmPurchase(<?php echo $book['book_id']; ?>)">Buy Now</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-center mt-6">
        <nav class="inline-flex items-center space-x-4">
            <a href="?page=1" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">First</a>
            <a href="?page=<?php echo max(1, $current_page - 1); ?>" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">Previous</a>
            <span class="px-4 py-2 text-sm"><?php echo $current_page; ?> / <?php echo $total_pages; ?></span>
            <a href="?page=<?php echo min($total_pages, $current_page + 1); ?>" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">Next</a>
            <a href="?page=<?php echo $total_pages; ?>" class="px-4 py-2 text-sm bg-gray-200 hover:bg-gray-300 rounded">Last</a>
        </nav>
    </div>
</div>

<script>
    function confirmPurchase(bookId) {
        if (confirm("Do you want to buy this book?")) {
            window.location.href = `purchase_handler.php?book_id=${bookId}`;
        }
    }
</script>

</body>
</html>
