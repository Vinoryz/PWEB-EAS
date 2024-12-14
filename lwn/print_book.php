<?php
include './components/db-connect.php';
session_start();

// Check if ISBN is provided
if (!isset($_GET['isbn']) || empty($_GET['isbn'])) {
    echo "<p>No ISBN provided. Please go back and select a book.</p>";
    exit;
}

$isbn = htmlspecialchars($_GET['isbn'], ENT_QUOTES, 'UTF-8');

try {
    // Retrieve book details from the database
    $query = "SELECT ISBN, Picture, Title, Author, Publisher, PublicationYear, Genre, Synopsis 
              FROM book 
              WHERE ISBN = :isbn";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);
    $stmt->execute();
    $book = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$book) {
        echo "<p>Book not found. Please check the ISBN and try again.</p>";
        exit;
    }
} catch (PDOException $e) {
    echo "<p>Database error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Book Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .book-details {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .book-details img {
            max-width: 150px;
            height: auto;
            display: block;
            margin-bottom: 20px;
        }
        .book-details h1 {
            margin-top: 0;
            font-size: 24px;
            color: #333;
        }
        .book-details .info {
            margin-top: 20px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .print-button, .back-button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
        }
        .back-button {
            background-color: #f44336;
        }
        .print-button:hover {
            background-color: #45a049;
        }
        .back-button:hover {
            background-color: #d7372e;
        }
    </style>
</head>
<body>
    <div class="book-details">
        <h1><?= htmlspecialchars($book['Title'], ENT_QUOTES, 'UTF-8') ?></h1>
        <?php if (!empty($book['Picture'])): ?>
            <img src="data:image/jpeg;base64,<?= base64_encode($book['Picture']) ?>" alt="Book Image">
        <?php else: ?>
            <p>No image available.</p>
        <?php endif; ?>
        <div class="info">
            <div class="info-item"><strong>ISBN:</strong> <?= htmlspecialchars($book['ISBN'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-item"><strong>Author:</strong> <?= htmlspecialchars($book['Author'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-item"><strong>Publisher:</strong> <?= htmlspecialchars($book['Publisher'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-item"><strong>Year:</strong> <?= htmlspecialchars($book['PublicationYear'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-item"><strong>Genre:</strong> <?= htmlspecialchars($book['Genre'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="info-item"><strong>Synopsis:</strong> <?= nl2br(htmlspecialchars($book['Synopsis'], ENT_QUOTES, 'UTF-8')) ?></div>
        </div>
        <button class="print-button" onclick="window.print();">Print</button>
        <a href="./book_list.php" class="back-button">Back</a>
    </div>
</body>
</html>
