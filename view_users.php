<?php
include 'db_connect.php'; // Include your database connection file
include 'nav_admin.php';
// Fetch all users from the database
$query = "SELECT * FROM Users";
$stmt = $pdo->query($query);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle delete operation
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    
    try {
        $delete_stmt = $pdo->prepare("DELETE FROM Users WHERE user_id = :user_id");
        $delete_stmt->bindParam(':user_id', $delete_id);
        $delete_stmt->execute();
        echo "<div class='alert alert-success'>User deleted successfully!</div>";
    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Error deleting user: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-semibold mb-6 text-gray-700">Users List</h2>
        
        <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md">
            <thead>
                <tr class="bg-gray-100">
                    <th class="px-4 py-2 text-left text-gray-600">User ID</th>
                    <th class="px-4 py-2 text-left text-gray-600">Username</th>
                    <th class="px-4 py-2 text-left text-gray-600">Email</th>
                    <th class="px-4 py-2 text-left text-gray-600">Role</th>
                    <th class="px-4 py-2 text-left text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                <tr class="border-b border-gray-200">
                    <td class="px-4 py-2"><?php echo $user['user_id']; ?></td>
                    <td class="px-4 py-2"><?php echo $user['username']; ?></td>
                    <td class="px-4 py-2"><?php echo $user['email']; ?></td>
                    <td class="px-4 py-2"><?php echo ucfirst($user['role']); ?></td>
                    <td class="px-4 py-2">
                        <!-- Edit Button -->
                        <a href="edit_user.php?user_id=<?php echo $user['user_id']; ?>" class="text-blue-500 hover:text-blue-700 px-4 py-2">Edit</a>
                        
                        <!-- Delete Button -->
                        <a href="view_users.php?delete_id=<?php echo $user['user_id']; ?>" class="text-red-500 hover:text-red-700 px-4 py-2" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
