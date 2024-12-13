<?php
include 'db_connect.php'; // Include your database connection file
include 'nav_admin.php';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize user input
    $username = htmlspecialchars(trim($_POST['username']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = htmlspecialchars(trim($_POST['password']));
    $confirm_password = htmlspecialchars(trim($_POST['confirm_password']));
    $role = $_POST['role'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        // Check if the username or email already exists
        try {
            // Check for existing username
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $username_exists = $stmt->fetchColumn();

            // Check for existing email
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM Users WHERE email = :email");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $email_exists = $stmt->fetchColumn();

            // If either username or email exists, show an error
            if ($username_exists > 0) {
                $error = "Username already exists!";
            } elseif ($email_exists > 0) {
                $error = "Email already exists!";
            } else {
                // Hash the password
                $password_hash = password_hash($password, PASSWORD_DEFAULT);

                // Insert user data into the database
                $stmt = $pdo->prepare("INSERT INTO Users (username, email, password_hash, role) VALUES (:username, :email, :password_hash, :role)");
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':password_hash', $password_hash);
                $stmt->bindParam(':role', $role);

                if ($stmt->execute()) {
                    // Send confirmation email
                    $subject = "User Created Successfully";
                    $message = "Hello $username,\n\nYour account has been successfully created on our website.\n\nBest regards,\nYour Website";
                    $headers = "From: saswatkumar787@gmail.com";

                    if (mail($email, $subject, $message, $headers)) {
                        $success_message = "User created successfully! A confirmation email has been sent to $email.";
                    } else {
                        $error = "Error sending the confirmation email.";
                    }
                    header("Location: dashboard.php");
                } else {
                    $error = "Error creating user.";
                }
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="max-w-4xl mx-auto p-8 bg-white rounded-lg shadow-xl my-10">
        <h2 class="text-3xl font-semibold mb-8 text-center text-gray-800">Create New User</h2>

        <?php if (isset($success_message)): ?>
            <div class="bg-green-500 text-white p-4 mb-6 rounded-lg shadow-md">
                <?php echo $success_message; ?>
            </div>
        <?php elseif (isset($error)): ?>
            <div class="bg-red-500 text-white p-4 mb-6 rounded-lg shadow-md">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="create_user.php" method="POST">
            <div class="mb-6">
                <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                <input type="text" id="username" name="username" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" id="email" name="email" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" id="password" name="password" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
            </div>

            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700">Role</label>
                <select id="role" name="role" class="mt-1 p-3 w-full border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                    <option value="admin">Admin</option>
                    <option value="staff">Staff</option>
                    <option value="editor">Editor</option>
                    <option value="viewer">Viewer</option>
                </select>
            </div>

            <button type="submit" class="w-full py-3 bg-blue-500 text-white font-semibold rounded-lg hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 transition duration-300">Create User</button>
        </form>
    </div>

</body>
</html>
