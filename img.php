<?php
// Image upload processing
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $uploadDir = 'uploads/';
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // Check if file is an image
    $check = getimagesize($_FILES['image']['tmp_name']);
    if ($check === false) {
        $errorMessage = "File is not an image.";
    }

    // Check file size (5MB limit)
    elseif ($_FILES['image']['size'] > 5000000) {
        $errorMessage = "Sorry, your file is too large. Max allowed size is 5MB.";
    }

    // Allow only certain file formats
    elseif (!in_array($imageFileType, ['jpg', 'jpeg', 'png'])) {
        $errorMessage = "Sorry, only JPG, JPEG, and PNG files are allowed.";
    }

    // Try to upload the file
    elseif (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
        $successMessage = "The file " . htmlspecialchars(basename($_FILES['image']['name'])) . " has been uploaded.";
    } else {
        $errorMessage = "Sorry, there was an error uploading your file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Image</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="bg-white shadow-lg rounded-lg p-8 w-96">
        <h1 class="text-2xl font-bold text-center text-blue-600 mb-6">Upload Image</h1>

        <?php
        if (isset($successMessage)) {
            echo "<div class='bg-green-100 text-green-800 p-4 mb-4 rounded'>{$successMessage}</div>";
        }

        if (isset($errorMessage)) {
            echo "<div class='bg-red-100 text-red-800 p-4 mb-4 rounded'>{$errorMessage}</div>";
        }
        ?>

        <form action="" method="post" enctype="multipart/form-data">
            <label for="image" class="block text-gray-600 mb-2">Select image to upload:</label>
            <input type="file" name="image" id="image" class="block w-full text-gray-700 py-2 px-4 mb-4 border border-gray-300 rounded-md" accept="image/png, image/jpeg, image/jpg" required>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700">Upload Image</button>
        </form>
    </div>
</body>
</html>
