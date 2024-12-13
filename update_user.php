<?php
include 'db.php';
include 'User.php';

if (isset($_GET['id'])) {
    $user_id = $_GET['id'];

    $db = new Database();
    $pdo = $db->connect();
    $user = new User($pdo);

    $userData = $user->getUserById($user_id);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $username = $_POST['username'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        if ($user->updateUser($user_id, $username, $email, $password, $role)) {
            echo "User updated successfully!";
        } else {
            echo "Failed to update user.";
        }
    }
}
?>

<form method="POST" action="update_user.php?id=<?php echo $userData['user_id']; ?>">
    <input type="text" name="username" value="<?php echo $userData['username']; ?>" required><br>
    <input type="email" name="email" value="<?php echo $userData['email']; ?>" required><br>
    <input type="password" name="password" placeholder="New Password"><br>
    <select name="role">
        <option value="admin" <?php if ($userData['role'] == 'admin') echo 'selected'; ?>>Admin</option>
        <option value="staff" <?php if ($userData['role'] == 'staff') echo 'selected'; ?>>Staff</option>
        <option value="editor" <?php if ($userData['role'] == 'editor') echo 'selected'; ?>>Editor</option>
        <option value="viewer" <?php if ($userData['role'] == 'viewer') echo 'selected'; ?>>Viewer</option>
    </select><br>
    <button type="submit">Update User</button>
</form>
