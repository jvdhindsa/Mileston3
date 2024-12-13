<?php
include 'db_connect.php'; // Include your database connection file
include 'nav_admin.php';
// Query to get all sales
$sql = "SELECT s.sale_id, s.sale_date, s.quantity, s.total_amount, b.title, u.username 
        FROM Sales s
        JOIN Books b ON s.book_id = b.book_id
        JOIN Users u ON s.user_id = u.user_id
        ORDER BY s.sale_date DESC";

$stmt = $pdo->query($sql);
$sales = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales List</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">

    <div class="max-w-6xl mx-auto p-8 bg-white rounded-lg shadow-xl">
        <h2 class="text-3xl font-semibold mb-8 text-center text-gray-800">Sales List</h2>

        <table class="min-w-full table-auto bg-white border-collapse shadow-lg rounded-lg overflow-hidden">
            <thead>
                <tr class="bg-blue-500 text-white text-left">
                    <th class="py-3 px-6">Sale ID</th>
                    <th class="py-3 px-6">Book Title</th>
                    <th class="py-3 px-6">User</th>
                    <th class="py-3 px-6">Quantity</th>
                    <th class="py-3 px-6">Total Amount</th>
                    <th class="py-3 px-6">Sale Date</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($sales) > 0): ?>
                    <?php foreach ($sales as $sale): ?>
                        <tr class="border-t border-gray-300">
                            <td class="py-3 px-6"><?= htmlspecialchars($sale['sale_id']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($sale['title']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($sale['username']) ?></td>
                            <td class="py-3 px-6"><?= htmlspecialchars($sale['quantity']) ?></td>
                            <td class="py-3 px-6"><?= number_format($sale['total_amount'], 2) ?> USD</td>
                            <td class="py-3 px-6"><?= htmlspecialchars($sale['sale_date']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="py-3 px-6 text-center text-gray-500">No sales found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
