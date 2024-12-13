<?php
include 'db_connect.php'; // Include your database connection file
include 'nav_admin.php';
// Fetch the user details based on the user_id from URL
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Fetch user details from the database
    $query = "SELECT * FROM Users WHERE user_id = :user_id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        die("User not found.");
    }
}

// Handle form submission to update the user
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];

    // If password is provided, hash it, otherwise keep the existing password
    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
    } else {
        // Keep the old password if no new password is provided
        $password_hash = $user['password_hash'];
    }

    // Update the user in the database
    try {
        $update_query = "UPDATE Users SET username = :username, email = :email, role = :role, password_hash = :password_hash, updated_at = NOW() WHERE user_id = :user_id";
        $update_stmt = $pdo->prepare($update_query);
        $update_stmt->bindParam(':username', $username);
        $update_stmt->bindParam(':email', $email);
        $update_stmt->bindParam(':role', $role);
        $update_stmt->bindParam(':password_hash', $password_hash);
        $update_stmt->bindParam(':user_id', $user_id);
        $update_stmt->execute();
        echo "<div class='text-green-500 font-semibold'>User updated successfully!</div>";
        header('Location: view_users.php');
    } catch (PDOException $e) {
        echo "<div class='text-red-500 font-semibold'>Error updating user: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-8">
        <h2 class="text-3xl font-semibold mb-6 text-gray-700">Edit User</h2>

        <form action="edit_user.php?user_id=<?php echo $user_id; ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-md mx-auto">
            <div class="mb-4">
                <label for="username" class="block text-gray-700">Username</label>
                <input type="text" id="username" name="username" value="<?php echo $user['username']; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-700">Email</label>
                <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" class="w-full px-4 py-2 border rounded-lg" required>
            </div>

            <div class="mb-4">
                <label for="role" class="block text-gray-700">Role</label>
                <select id="role" name="role" class="w-full px-4 py-2 border rounded-lg">
                    <option value="admin" <?php if ($user['role'] == 'admin') echo 'selected'; ?>>Admin</option>
                    <option value="staff" <?php if ($user['role'] == 'staff') echo 'selected'; ?>>Staff</option>
                    <option value="editor" <?php if ($user['role'] == 'editor') echo 'selected'; ?>>Editor</option>
                    <option value="viewer" <?php if ($user['role'] == 'viewer') echo 'selected'; ?>>Viewer</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-700">Password (Leave blank to keep current password)</label>
                <input type="password" id="password" name="password" class="w-full px-4 py-2 border rounded-lg">
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-700">Update User</button>
        </form>
    </div>
</body>
</html>
