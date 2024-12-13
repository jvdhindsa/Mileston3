<?php
session_start();
include 'db_connect.php'; // Include your database connection file
include 'nav_admin.php';
// Fetch all comments from the database
$stmt = $pdo->prepare("SELECT c.comment_id, c.comment_text, c.created_at, c.moderated, u.username, b.title 
                       FROM Comments c
                       JOIN Users u ON c.user_id = u.user_id
                       JOIN Books b ON c.book_id = b.book_id
                       ORDER BY c.created_at DESC");
$stmt->execute();
$comments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comments</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">

    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-6">All Comments</h1>
        
        <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md">
            <thead>
                <tr class="text-left bg-gray-200">
                    <th class="px-6 py-4 text-gray-700">Book Title</th>
                    <th class="px-6 py-4 text-gray-700">Username</th>
                    <th class="px-6 py-4 text-gray-700">Comment</th>
                    <th class="px-6 py-4 text-gray-700">Created At</th>
                    <th class="px-6 py-4 text-gray-700">Status</th>
                    <th class="px-6 py-4 text-gray-700">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($comments as $comment): ?>
                <tr class="border-b">
                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($comment['title']); ?></td>
                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($comment['username']); ?></td>
                    <td class="px-6 py-4 text-gray-800"><?php echo htmlspecialchars($comment['comment_text']); ?></td>
                    <td class="px-6 py-4 text-gray-800"><?php echo date('Y-m-d H:i:s', strtotime($comment['created_at'])); ?></td>
                    <td class="px-6 py-4 text-gray-800">
                        <span class="inline-block px-3 py-1 text-sm font-semibold rounded-full 
                            <?php echo $comment['moderated'] == 'approved' ? 'bg-green-200 text-green-800' : ($comment['moderated'] == 'rejected' ? 'bg-red-200 text-red-800' : 'bg-yellow-200 text-yellow-800'); ?>">
                            <?php echo ucfirst($comment['moderated']); ?>
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <a href="edit_comment.php?id=<?php echo $comment['comment_id']; ?>" 
                           class="text-blue-500 hover:text-blue-700 mr-3">Edit</a>
                        <a href="delete_comment.php?id=<?php echo $comment['comment_id']; ?>" 
                           class="text-red-500 hover:text-red-700">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
