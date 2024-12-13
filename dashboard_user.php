<?php
session_start();
include 'db_connect.php';
include 'nav.php';
// Initialize variables
$books = [];
$search_query = '';
$selected_genre = '';
$per_page = 8; // Number of books per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1; // Current page number
$offset = ($page - 1) * $per_page; // Offset for the SQL query
$genres_stmt = $pdo->query("SELECT DISTINCT genre FROM Books");
$genres = $genres_stmt->fetchAll(PDO::FETCH_ASSOC);



// Handle search and genre filter
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $search_query = $_POST['search'] ?? '';
    $selected_genre = $_POST['genre'] ?? '';

    // Build SQL query with optional genre filter
    $query = "SELECT * FROM Books WHERE title LIKE :title";
    
    // Add genre filter if selected
    if ($selected_genre != '') {
        $query .= " AND genre = :genre";
    }
    
    // Add pagination logic to the query
    $query .= " LIMIT :limit OFFSET :offset";
    
    try {
        // Prepare statement
        $stmt = $pdo->prepare($query);
        $stmt->bindValue(':title', '%' . $search_query . '%');
        if ($selected_genre != '') {
            $stmt->bindValue(':genre', $selected_genre);
        }
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        
        // Execute the query
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Get total number of books (for pagination)
        $count_query = "SELECT COUNT(*) FROM Books WHERE title LIKE :title";
        if ($selected_genre != '') {
            $count_query .= " AND genre = :genre";
        }
        $count_stmt = $pdo->prepare($count_query);
        $count_stmt->bindValue(':title', '%' . $search_query . '%');
        if ($selected_genre != '') {
            $count_stmt->bindValue(':genre', $selected_genre);
        }
        $count_stmt->execute();
        $total_books = $count_stmt->fetchColumn();
        $total_pages = ceil($total_books / $per_page); // Calculate total pages
    } catch (PDOException $e) {
        echo "Error fetching books: " . $e->getMessage();
    }
} else {
    // Fetch all books if no search or genre filter is provided
    try {
        $stmt = $pdo->prepare("SELECT * FROM Books LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get total number of books (for pagination)
        $count_stmt = $pdo->query("SELECT COUNT(*) FROM Books");
        $total_books = $count_stmt->fetchColumn();
        $total_pages = ceil($total_books / $per_page); // Calculate total pages
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
    <title>Dashboard</title>
    <!-- Add Tailwind CSS CDN link -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">





<div class="container mx-auto p-4">
    <div class="text-center">
        <h1 class="text-3xl font-bold mb-2">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</h1>
        <p class="text-lg">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'None'); ?></p>
        <a href="logout.php" class="text-blue-500 hover:text-blue-700 mt-2 inline-block">Logout</a>
    </div>

    <h2 class="text-2xl font-bold text-center mt-8 mb-6">Books in Inventory</h2>

   <!-- Search and Genre Filter Form -->
<div class="flex justify-center mb-8">
    <form method="POST" action="" class="flex items-center space-x-2">
        <input type="text" name="search" placeholder="Search by book name" value="<?php echo htmlspecialchars($search_query); ?>"
            class="p-2 border border-gray-300 rounded-lg w-1/2 md:w-1/3" />
        
            <select name="genre" class="p-2 border border-gray-300 rounded-lg w-1/3">
                <option value="">All Genres</option>
                <?php foreach ($genres as $genre): ?>
                    <option value="<?php echo htmlspecialchars($genre['genre']); ?>" <?php echo ($selected_genre == $genre['genre'] ? 'selected' : ''); ?>>
                        <?php echo htmlspecialchars($genre['genre']); ?>
                    </option>
                <?php endforeach; ?>
            </select>


        <button type="submit" class="bg-green-500 text-white py-2 px-6 rounded-lg hover:bg-green-600">Search</button>
    </form>
</div>

    <!-- Books Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
        <?php if (!empty($books)): ?>
            <?php foreach ($books as $book): ?>
                <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg transition-all">
                    <<a href="book_details.php?book_id=<?php echo $book['book_id']; ?>">
                        <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image" class="w-full h-64 object-cover rounded-lg mb-4">
                    </a>
                    <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($book['title']); ?></h3>
                    <p class="text-sm mb-2"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                    <p class="text-sm mb-2"><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
                    <p class="text-sm mb-2"><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                    <p class="text-sm mb-2"><strong>Stock:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?></p>
                    <p class="text-sm mb-4"><strong>Published on:</strong> <?php echo htmlspecialchars($book['publication_date']); ?></p>
                    <button onclick="confirmPurchase(<?php echo $book['book_id']; ?>)" class="bg-blue-500 text-white py-2 px-4 rounded-lg w-full hover:bg-blue-600 transition">Buy Now</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="col-span-full text-center text-lg text-gray-500">No books found in the inventory.</p>
        <?php endif; ?>
    </div>

<!-- Pagination Links -->
<div class="flex justify-center mt-8">
    <nav class="flex space-x-4">
        <!-- Previous Page Link -->
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Previous</a>
        <?php else: ?>
            <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg">Previous</span>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 <?php echo ($i == $page ? 'bg-gray-500 text-white' : ''); ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Next Page Link -->
        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>" class="px-4 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600">Next</a>
        <?php else: ?>
            <span class="px-4 py-2 bg-gray-300 text-gray-500 rounded-lg">Next</span>
        <?php endif; ?>
    </nav>
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
