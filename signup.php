<?php
session_start();
include 'db_connect.php'; // Include your database connection file

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'viewer'; // Default role for new users

    // Check if the passwords match
    if ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        try {
            // Check if username or email already exists
            $stmt = $pdo->prepare("SELECT * FROM Users WHERE username = :username OR email = :email");
            $stmt->execute(['username' => $username, 'email' => $email]);
            $existingUser = $stmt->fetch();

            if ($existingUser) {
                $error = "Username or email already exists!";
            } else {
                // Hash the password before storing it in the database
                $password_hash = password_hash($password, PASSWORD_DEFAULT); // Hashing the password

                // Insert new user into the database
                $stmt = $pdo->prepare("INSERT INTO Users (username, email, password_hash, role, created_at) 
                                        VALUES (:username, :email, :password_hash, :role, NOW())");
                $stmt->execute([
                    'username' => $username,
                    'email' => $email,
                    'password_hash' => $password_hash, // Store the hashed password
                    'role' => $role,
                ]);

                $success = "Account created successfully! You can now log in.";
            }
        } catch (PDOException $e) {
            $error = "Error creating account: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
        <h2 class="text-2xl font-semibold text-center mb-6 text-gray-700">Create an Account</h2>

        <!-- Error or Success Message -->
        <?php if (isset($error)): ?>
            <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
        <?php elseif (isset($success)): ?>
            <p class="text-green-500 text-center mb-4"><?php echo $success; ?></p>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label for="username" class="block text-gray-600">Username:</label>
                <input type="text" name="username" id="username" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400" placeholder="Enter your username" required>
            </div>

            <div class="mb-4">
                <label for="email" class="block text-gray-600">Email:</label>
                <input type="email" name="email" id="email" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400" placeholder="Enter your email" required>
            </div>

            <div class="mb-4">
                <label for="password" class="block text-gray-600">Password:</label>
                <input type="password" name="password" id="password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400" placeholder="Enter your password" required>
            </div>

            <div class="mb-4">
                <label for="confirm_password" class="block text-gray-600">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-400" placeholder="Confirm your password" required>
            </div>

            <button type="submit" class="w-full bg-blue-500 text-white py-2 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">Signup</button>
        </form>

        <div class="text-center mt-4">
            <p class="text-gray-600">Already have an account? <a href="login.php" class="text-blue-500 hover:underline">Login here</a>.</p>
        </div>
    </div>
</body>
</html>
