<?php
session_start();
include 'db_connect.php';
include 'nav.php'; // Navigation bar

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Handle deletion if requested
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    try {
        $stmt = $pdo->prepare("DELETE FROM Sales WHERE sale_id = :sale_id AND user_id = :user_id");
        $stmt->execute([
            'sale_id' => $_POST['delete_id'],
            'user_id' => $_SESSION['user_id']
        ]);
        $success = "Sale deleted successfully.";
    } catch (PDOException $e) {
        $error = "Error deleting sale: " . $e->getMessage();
    }
}

// Fetch sales for the logged-in user
$sales = [];
try {
    $stmt = $pdo->prepare("
        SELECT Sales.sale_id, Books.title AS book_title, Sales.quantity, Sales.total_amount, Sales.sale_date 
        FROM Sales
        JOIN Books ON Sales.book_id = Books.book_id
        WHERE Sales.user_id = :user_id
        ORDER BY Sales.sale_date DESC
    ");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Error fetching sales: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Sales</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">

<!-- Main Container -->
<div class="container mx-auto p-6 mt-8 bg-white rounded-lg shadow-lg">
    <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">My Sales</h2>

    <!-- Success message -->
    <?php if (isset($success)): ?>
        <p class="text-green-500 text-center mb-4"><?php echo $success; ?></p>
    <?php endif; ?>

    <!-- Error message -->
    <?php if (isset($error)): ?>
        <p class="text-red-500 text-center mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <!-- Sales Table -->
    <?php if (!empty($sales)): ?>
        <table class="min-w-full table-auto bg-white shadow-md rounded-lg">
            <thead class="bg-gray-100">
                <tr>
                    <th class="py-3 px-6 text-left text-gray-600">Sale ID</th>
                    <th class="py-3 px-6 text-left text-gray-600">Book Title</th>
                    <th class="py-3 px-6 text-left text-gray-600">Quantity</th>
                    <th class="py-3 px-6 text-left text-gray-600">Total Amount</th>
                    <th class="py-3 px-6 text-left text-gray-600">Sale Date</th>
                    <th class="py-3 px-6 text-left text-gray-600">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales as $sale): ?>
                    <tr class="border-t">
                        <td class="py-3 px-6 text-gray-800"><?php echo htmlspecialchars($sale['sale_id']); ?></td>
                        <td class="py-3 px-6 text-gray-800"><?php echo htmlspecialchars($sale['book_title']); ?></td>
                        <td class="py-3 px-6 text-gray-800"><?php echo htmlspecialchars($sale['quantity']); ?></td>
                        <td class="py-3 px-6 text-gray-800"><?php echo htmlspecialchars($sale['total_amount']); ?></td>
                        <td class="py-3 px-6 text-gray-800"><?php echo htmlspecialchars($sale['sale_date']); ?></td>
                        <td class="py-3 px-6 text-gray-800">
                            <form method="POST" onsubmit="return confirm('Are you sure you want to delete this sale?');">
                                <input type="hidden" name="delete_id" value="<?php echo htmlspecialchars($sale['sale_id']); ?>">
                                <button type="submit" class="text-red-500 hover:text-red-700">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="text-gray-600 text-center mt-4">No sales found for your account.</p>
    <?php endif; ?>
</div>

</body>
</html>
