<?php
session_start();
include 'db_connect.php';
include 'nav.php';

// Fetch book details based on book_id
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    try {
        $stmt = $pdo->prepare("SELECT * FROM Books WHERE book_id = :book_id");
        $stmt->execute(['book_id' => $book_id]);
        $book = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$book) {
            die("Book not found!");
        }
    } catch (PDOException $e) {
        die("Error fetching book details: " . $e->getMessage());
    }
}

// Update book details
// Update book details including image URL
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $book_id = $_POST['book_id'];
    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $publication_date = $_POST['publication_date'];
    $image_path = $_POST['image_path'];  // Get image URL from the form

    // Handle image removal if checked
    if (isset($_POST['remove_image']) && $_POST['remove_image'] == 'on') {
        $image_path = '';  // Remove the image by setting path to empty string
    }

    // If a new image is uploaded, replace the image path
    if (isset($_FILES['new_image']) && $_FILES['new_image']['error'] == 0) {
        // Handle file upload (add validation as needed)
        $upload_dir = 'uploads/';
        $image_path = $upload_dir . basename($_FILES['new_image']['name']);
        move_uploaded_file($_FILES['new_image']['tmp_name'], $image_path);
    }

    try {
        $stmt = $pdo->prepare("UPDATE Books SET title = :title, author = :author, genre = :genre, price = :price, 
                               stock_quantity = :stock_quantity, publication_date = :publication_date, image_path = :image_path
                               WHERE book_id = :book_id");
        $stmt->execute([
            'title' => $title,
            'author' => $author,
            'genre' => $genre,
            'price' => $price,
            'stock_quantity' => $stock_quantity,
            'publication_date' => $publication_date,
            'image_path' => $image_path,  // Save the updated image URL
            'book_id' => $book_id
        ]);
        header("Location: dashboard.php");
        exit();
    } catch (PDOException $e) {
        $error = "Error updating book: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

<div class="navbar bg-gray-800 p-4">
    <a href="dashboard.php" class="text-white text-lg">Dashboard</a>
    <a href="create.php" class="text-white text-lg ml-4">Add Book</a>
    <a href="read.php" class="text-white text-lg ml-4">View Books</a>
    <a href="logout.php" class="text-white text-lg ml-4">Logout</a>
</div>

<div class="max-w-2xl mx-auto p-6 bg-white rounded-lg shadow-md mt-10">
    <h2 class="text-2xl font-semibold text-center mb-6">Edit Book</h2>
    <?php if (isset($error)) echo "<p class='text-red-500 text-center'>$error</p>"; ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book['book_id']); ?>">

        <label class="block text-sm font-medium text-gray-700">Title</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" required class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <label class="block text-sm font-medium text-gray-700 mt-4">Author</label>
        <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" required class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <label class="block text-sm font-medium text-gray-700 mt-4">Genre</label>
        <input type="text" name="genre" value="<?php echo htmlspecialchars($book['genre']); ?>" required class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <label class="block text-sm font-medium text-gray-700 mt-4">Price</label>
        <input type="number" step="0.01" name="price" value="<?php echo htmlspecialchars($book['price']); ?>" required class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <label class="block text-sm font-medium text-gray-700 mt-4">Stock Quantity</label>
        <input type="number" name="stock_quantity" value="<?php echo htmlspecialchars($book['stock_quantity']); ?>" required class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <label class="block text-sm font-medium text-gray-700 mt-4">Publication Date</label>
        <input type="date" name="publication_date" value="<?php echo htmlspecialchars($book['publication_date']); ?>" class="w-full p-3 mt-1 border border-gray-300 rounded-lg">

        <div class="mt-4">
            <label class="block text-sm font-medium text-gray-700">Current Image</label>
            <div class="mb-4">
                <?php if ($book['image_path']): ?>
                    <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Current Image" class="w-48 h-48 object-cover rounded-lg">
                    <div class="mt-2">
                        <label class="inline-flex items-center text-sm font-medium text-gray-700">
                            <input type="checkbox" name="remove_image" class="form-checkbox h-4 w-4 text-red-500">
                            <span class="ml-2">Remove Image</span>
                        </label>
                    </div>
                <?php else: ?>
                    <p>No image available.</p>
                <?php endif; ?>
            </div>

            <label class="block text-sm font-medium text-gray-700">Upload New Image</label>
            <input type="file" name="new_image" accept="image/*" class="w-full p-3 mt-1 border border-gray-300 rounded-lg">
        </div>

        <button type="submit" class="w-full p-3 mt-6 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-700">Update Book</button>
    </form>
</div>

</body>
</html>
