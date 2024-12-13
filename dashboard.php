<?php
session_start();
include 'db_connect.php';
include 'nav_admin.php';
// Initialize variables
$books = [];
$search_query = '';
$sort_column = 'title';  // Default sort by title
$sort_order = 'ASC';     // Default sort order (ascending)

// Handle search functionality
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle search query
    if (isset($_POST['search'])) {
        $search_query = $_POST['search'];
    }

    // Handle sorting options
    if (isset($_POST['sort_column'])) {
        $sort_column = $_POST['sort_column'];
    }
    if (isset($_POST['sort_order'])) {
        $sort_order = $_POST['sort_order'];
    }

    try {
        // Use a prepared statement to prevent SQL injection
        $stmt = $pdo->prepare("SELECT * FROM Books WHERE title LIKE :title ORDER BY $sort_column $sort_order");
        $stmt->execute(['title' => '%' . $search_query . '%']);
        $books = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        echo "Error fetching books: " . $e->getMessage();
    }
} else {
    // Fetch all books if no search query is provided
    try {
        $stmt = $pdo->query("SELECT * FROM Books ORDER BY $sort_column $sort_order");
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
    <title>Dashboard1</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">
    <div class="max-w-7xl mx-auto p-6">
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h1 class="text-2xl font-bold">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Guest'); ?>!</h1>
            <p class="text-lg text-gray-700">Role: <?php echo htmlspecialchars($_SESSION['role'] ?? 'None'); ?></p>

            <h2 class="mt-6 text-xl font-semibold">Books in Inventory</h2>

            <!-- Search Form -->
            <div class="mt-4 flex items-center space-x-4">
                <form method="POST" action="" class="w-full md:w-1/3 flex">
                    <input type="text" name="search" placeholder="Search by book name" value="<?php echo htmlspecialchars($search_query); ?>" class="w-full px-4 py-2 border border-gray-300 rounded-md" />
                    <button type="submit" class="ml-2 px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Search</button>
                </form>
            </div>

            <!-- Sorting Options -->
            <div class="mt-6 flex items-center space-x-6">
                <form method="POST" action="" class="flex items-center">
                    <div class="flex items-center space-x-4">
                        <label for="sort_column" class="text-lg">Sort by:</label>
                        <div class="flex items-center space-x-2">
                            <label for="sort_title" class="text-sm">Title</label>
                            <input type="radio" id="sort_title" name="sort_column" value="title" <?php echo ($sort_column == 'title') ? 'checked' : ''; ?> class="form-radio">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="sort_bookid" class="text-sm">Book ID</label>
                            <input type="radio" id="sort_bookid" name="sort_column" value="book_id" <?php echo ($sort_column == 'book_id') ? 'checked' : ''; ?> class="form-radio">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="sort_price" class="text-sm">Price</label>
                            <input type="radio" id="sort_price" name="sort_column" value="price" <?php echo ($sort_column == 'price') ? 'checked' : ''; ?> class="form-radio">
                        </div>
                    </div>

                    <div class="flex items-center space-x-4 ml-6">
                        <label for="sort_order" class="text-lg">Order:</label>
                        <div class="flex items-center space-x-2">
                            <label for="asc_order" class="text-sm">Ascending</label>
                            <input type="radio" id="asc_order" name="sort_order" value="ASC" <?php echo ($sort_order == 'ASC') ? 'checked' : ''; ?> class="form-radio">
                        </div>
                        <div class="flex items-center space-x-2">
                            <label for="desc_order" class="text-sm">Descending</label>
                            <input type="radio" id="desc_order" name="sort_order" value="DESC" <?php echo ($sort_order == 'DESC') ? 'checked' : ''; ?> class="form-radio">
                        </div>
                    </div>

                    <button type="submit" class="ml-6 px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Sort</button>
                </form>
            </div>

            <!-- Books Cards -->
            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if (!empty($books)): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="bg-white p-4 rounded-lg shadow-md hover:shadow-lg">
                            <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image" class="w-full h-48 object-cover rounded-md mb-4">
                            <div>
                                <h3 class="text-lg font-semibold"><?php echo htmlspecialchars($book['title']); ?></h3>
                                <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                                <p><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
                                <p><strong>Price:</strong> $<?php echo htmlspecialchars($book['price']); ?></p>
                                <p><strong>Stock:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?></p>
                                <p><strong>Published:</strong> <?php echo htmlspecialchars($book['publication_date'] ?? 'N/A'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No books found in the inventory.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
