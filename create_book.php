<?php
session_start();
include 'db_connect.php';
include 'nav_admin.php';



if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $author = $_POST['author'];
    $isbn = $_POST['isbn'];
    $genre = $_POST['genre'];
    $price = $_POST['price'];
    $stock_quantity = $_POST['stock_quantity'];
    $publication_date = $_POST['publication_date'];
    $description = $_POST['description'];

    // Handle Image Upload
    $uploadDir = 'uploads/';
    $imagePath = $uploadDir . basename($_FILES['image_url']['name']);
    $imageTmpName = $_FILES['image_url']['tmp_name'];
    $imageType = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));

    // Check if the uploaded file is a valid image type
    $validTypes = ['jpg', 'jpeg', 'png'];
    if (!in_array($imageType, $validTypes)) {
        $message = "Invalid image type. Only JPG, JPEG, and PNG are allowed.";
    } else {
        // Resize the image using PHP's built-in functions
        list($width, $height) = getimagesize($imageTmpName);
        $newWidth = 800; // Desired width
        $newHeight = ($height / $width) * $newWidth; // Maintain aspect ratio

        // Create a new image resource based on the uploaded image
        switch ($imageType) {
            case 'jpg':
            case 'jpeg':
                $src = imagecreatefromjpeg($imageTmpName);
                break;
            case 'png':
                $src = imagecreatefrompng($imageTmpName);
                break;
        }

        // Create a new blank image with the desired dimensions
        $dst = imagecreatetruecolor($newWidth, $newHeight);

        // Resize the original image and copy it into the new image resource
        imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

        // Save the resized image back to the uploads folder
        switch ($imageType) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($dst, $imagePath, 90); // Save with 90% quality
                break;
            case 'png':
                imagepng($dst, $imagePath);
                break;
        }

        // Clean up the image resources
        imagedestroy($src);
        imagedestroy($dst);

        // Insert book details into the database
        try {
            $stmt = $pdo->prepare("INSERT INTO Books (title, author, ISBN, genre, price, stock_quantity, publication_date, description, image_path) 
                                   VALUES (:title, :author, :isbn, :genre, :price, :stock_quantity, :publication_date, :description, :image_path)");
            $stmt->execute([
                'title' => $title,
                'author' => $author,
                'isbn' => $isbn,
                'genre' => $genre,
                'price' => $price,
                'stock_quantity' => $stock_quantity,
                'publication_date' => $publication_date,
                'description' => $description,
                'image_path' => $imagePath
            ]);
            header("Location: dashboard.php");
            exit();
        } catch (PDOException $e) {
            $message = "Error adding book: " . $e->getMessage();
        }
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Book</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto mt-10 p-6 bg-white rounded-lg shadow-md">
        <h2 class="text-2xl font-bold text-gray-700 text-center mb-6">Add a New Book</h2>
        <?php if (isset($message)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $message; ?></p>
        <?php endif; ?>
        <form method="POST" action="" class="space-y-4" enctype="multipart/form-data">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-600">Title</label>
                <input type="text" id="title" name="title" placeholder="Book Title" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="author" class="block text-sm font-medium text-gray-600">Author</label>
                <input type="text" id="author" name="author" placeholder="Author Name" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="isbn" class="block text-sm font-medium text-gray-600">ISBN</label>
                <input type="text" id="isbn" name="isbn" placeholder="ISBN Number" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="genre" class="block text-sm font-medium text-gray-600">Genre</label>
                <select id="genre" name="genre" required class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
                    <option value="" disabled selected>Select a Genre</option>
                    <?php
                    // Fetch categories from the database
                    try {
                        $stmt = $pdo->query("SELECT name FROM Categories ORDER BY name");
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo "<option value=\"" . htmlspecialchars($row['name']) . "\">" . htmlspecialchars($row['name']) . "</option>";
                        }
                    } catch (PDOException $e) {
                        echo "<option disabled>Error loading categories</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="price" class="block text-sm font-medium text-gray-600">Price</label>
                <input type="number" step="0.01" id="price" name="price" placeholder="Price" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="stock_quantity" class="block text-sm font-medium text-gray-600">Stock Quantity</label>
                <input type="number" id="stock_quantity" name="stock_quantity" placeholder="Stock Quantity" required 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="publication_date" class="block text-sm font-medium text-gray-600">Publication Date</label>
                <input type="date" id="publication_date" name="publication_date" 
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <div>
                <label for="description" class="block text-sm font-medium text-gray-600">Description</label>
                <textarea id="description" name="description" placeholder="Description" 
                          class="w-full mt-1 p-2 border border-gray-300 rounded-lg"></textarea>
            </div>
            <div>
                <label for="image_url" class="block text-sm font-medium text-gray-600">Image (JPG, PNG, JPEG)</label>
                <input type="file" id="image_url" name="image_url" accept="image/jpeg, image/png, image/jpg" required
                       class="w-full mt-1 p-2 border border-gray-300 rounded-lg">
            </div>
            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600">
                Add Book
            </button>
        </form>
    </div>
</body>
</html>