<?php
session_start();
include 'db_connect.php';
include 'nav.php';
// Fetch book details based on book_id passed in the URL
if (isset($_GET['book_id'])) {
    $book_id = $_GET['book_id'];

    // Prepare SQL query to fetch book details
    $stmt = $pdo->prepare("SELECT * FROM Books WHERE book_id = :book_id");
    $stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $stmt->execute();
    
    // Fetch the book data
    $book = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Fetch comments for the book
    $comments_stmt = $pdo->prepare("SELECT c.comment_text, c.created_at, u.username FROM Comments c 
                                    JOIN Users u ON c.user_id = u.user_id 
                                    WHERE c.book_id = :book_id AND c.moderated = 'approved' 
                                    ORDER BY c.created_at DESC");
    $comments_stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
    $comments_stmt->execute();
    $comments = $comments_stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    die("Book ID not provided.");
}

// Handle comment submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['comment_text'])) {
    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        die("You must be logged in to comment.");
    }

    $user_id = $_SESSION['user_id']; // Retrieve logged-in user's ID
    $comment_text = trim($_POST['comment_text']); // Sanitize input

    if (!empty($comment_text)) {
        // Insert new comment into the database
        $insert_stmt = $pdo->prepare("INSERT INTO Comments (book_id, user_id, comment_text, moderated) 
                                      VALUES (:book_id, :user_id, :comment_text, 'approved')");
        $insert_stmt->bindParam(':book_id', $book_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $insert_stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);
        $insert_stmt->execute();

        // Redirect to the same page to show the new comment
        header("Location: book_details.php?book_id=$book_id");
        exit();
    } else {
        echo "Comment text cannot be empty.";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Details</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-900">



<div class="container mx-auto p-6">
    <?php if ($book): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
            <!-- Book Image -->
            <div>
                <img src="<?php echo htmlspecialchars($book['image_path']); ?>" alt="Book Image" class="w-full h-96 object-cover rounded-lg shadow-md">
            </div>

            <!-- Book Details -->
            <div>
                <h2 class="text-3xl font-bold mb-4"><?php echo htmlspecialchars($book['title']); ?></h2>
                <p class="text-lg text-gray-700 mb-4"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Genre:</strong> <?php echo htmlspecialchars($book['genre']); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Price:</strong> $<?php echo number_format($book['price'], 2); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Stock Quantity:</strong> <?php echo htmlspecialchars($book['stock_quantity']); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Published on:</strong> <?php echo htmlspecialchars($book['publication_date']); ?></p>
                <p class="text-lg text-gray-700 mb-4"><strong>Description:</strong> <?php echo nl2br(htmlspecialchars($book['description'])); ?></p>

                <!-- Buy Button -->
                <button onclick="confirmPurchase(<?php echo $book['book_id']; ?>)" class="bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition">
                    Buy Now
                </button>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="mt-12">
            <h3 class="text-2xl font-semibold mb-4">Comments</h3>

            <?php if (!empty($comments)): ?>
                <div class="space-y-4">
                    <?php foreach ($comments as $comment): ?>
                        <div class="bg-white p-4 rounded-lg shadow-md">
                            <p class="text-sm text-gray-600"><strong><?php echo htmlspecialchars($comment['username']); ?></strong> 
                                on <?php echo date("F j, Y, g:i a", strtotime($comment['created_at'])); ?>
                            </p>
                            <p class="mt-2"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-gray-500">No comments yet. Be the first to comment!</p>
            <?php endif; ?>

            <!-- Comment Form -->
            <div class="mt-8">
                <h3 class="text-xl font-semibold mb-4">Leave a Comment</h3>
                <form action="book_details.php?book_id=<?php echo $book_id; ?>" method="POST">
                    <textarea name="comment_text" class="w-full p-4 border border-gray-300 rounded-lg" rows="4" placeholder="Write your comment here..." required></textarea>
                    <button type="submit" class="mt-4 bg-blue-500 text-white py-2 px-6 rounded-lg hover:bg-blue-600 transition">
                        Submit Comment
                    </button>
                </form>
            </div>
        </div>
    <?php else: ?>
        <p class="text-center text-lg text-red-500">Book details not found.</p>
    <?php endif; ?>
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
