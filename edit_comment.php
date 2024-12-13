<?php
session_start();
include 'db_connect.php';

if (isset($_GET['id'])) {
    $comment_id = $_GET['id'];
    
    // Fetch the comment data
    $stmt = $pdo->prepare("SELECT * FROM Comments WHERE comment_id = :comment_id");
    $stmt->execute(['comment_id' => $comment_id]);
    $comment = $stmt->fetch();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Update the comment text and moderated status in the database
        $comment_text = $_POST['comment_text'];
        $moderated = $_POST['moderated'];

        // Prepare the update statement
        $stmt = $pdo->prepare("UPDATE Comments SET comment_text = :comment_text, moderated = :moderated WHERE comment_id = :comment_id");
        $stmt->execute(['comment_text' => $comment_text, 'moderated' => $moderated, 'comment_id' => $comment_id]);

        // Redirect back to the comments page
        header('Location: comments.php');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Comment</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">Edit Comment</h1>
        
        <form method="POST">
            <!-- Comment Text Area -->
            <textarea name="comment_text" class="w-full p-4 border border-gray-300 rounded-md" rows="6" required><?php echo htmlspecialchars($comment['comment_text']); ?></textarea>

            <!-- Moderation Status -->
            <div class="mt-4">
                <label for="moderated" class="block text-gray-700 font-medium mb-2">Moderation Status</label>
                <select name="moderated" id="moderated" class="w-full p-4 border border-gray-300 rounded-md">
                    <option value="pending" <?php echo $comment['moderated'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $comment['moderated'] == 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $comment['moderated'] == 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="mt-4 w-full py-3 bg-blue-500 text-white rounded-md hover:bg-blue-600 focus:outline-none">Save Changes</button>
        </form>
    </div>

</body>
</html>
