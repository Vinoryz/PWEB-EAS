<?php
include './components/db-connect.php';
session_start();
include './components/header.php';

try {
    // Get the sort column and order from URL parameters
    $sortColumn = isset($_GET['sort']) ? $_GET['sort'] : 'Title';
    $sortOrder = isset($_GET['order']) ? $_GET['order'] : 'ASC';

    // Validate sort column to prevent SQL injection
    $allowedColumns = ['ISBN', 'Title', 'Author', 'PublicationYear', 'Genre', 'b_status'];
    if (!in_array($sortColumn, $allowedColumns)) {
        $sortColumn = 'Title';
    }

    // Validate sort order
    $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';

    // Prepare and execute query
    $query = "SELECT ISBN, Picture, Title, Author, PublicationYear, Genre, b_status 
              FROM book 
              ORDER BY $sortColumn $sortOrder";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $books = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Function to generate sort URL
function getSortUrl($column, $currentSort, $currentOrder) {
    $newOrder = ($currentSort === $column && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    return "?sort=" . urlencode($column) . "&order=" . $newOrder;
}
?>

<?php include './components/confirmation-modal.php'; ?>


<!DOCTYPE html>
<html>
<head>
    <title>Book List</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .sort-icon {
            margin-left: 5px;
            color: #666;
        }
        
        .book-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
        }
        
        .status-available {
            color: #4CAF50;
            font-weight: bold;
        }
        
        .status-borrowed {
            color: #f44336;
            font-weight: bold;
        }
        
        .container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .search-bar {
            margin-bottom: 20px;
        }
        
        .search-input {
            padding: 8px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .add-book-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        
        .add-book-btn:hover {
            background-color: #45a049;
        }
        
        .add-book-btn i {
            font-size: 16px;
        }
        body {
            padding-top: 100px;
        }
        .book-list {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        .book-list th, .book-list td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .book-list th {
            background-color: #f5f5f5;
            font-weight: bold;
            cursor: pointer;
        }
        .book-list th:hover {
            background-color: #e0e0e0;
        }
        .book-list tr:hover {
            background-color: #f9f9f9;
        }
        .book-image {
            width: 60px;
            height: 80px;
            object-fit: cover;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        .action-buttons a, .action-buttons form button {
            background-color: #4CAF50;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 12px;
            transition: background-color 0.3s;
            cursor: pointer;
        }
        .action-buttons a:hover, .action-buttons form button:hover {
            background-color: #45a049;
        }
        .delete-button {
            background-color: #f44336;
        }
        .delete-button:hover {
            background-color: #e53935;
        }
        .print-button {
            background-color: #2196F3;
        }
        .print-button:hover {
            background-color: #1E88E5;
        }
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

    </style>
</head>
<body>
    <div class="container">
        <div class="header-container">
            <div class="search-bar">
                <input type="text" id="searchInput" class="search-input" placeholder="Search books..." onkeyup="searchTable()">
            </div>
            <?php if ($_SESSION['role'] === 'librarian'): ?>
            <a href="add_book.php" class="add-book-btn">
                <i class="fas fa-plus"></i>
                Add New Book
            </a>
            <?php endif; ?>
        </div>
        <table class="book-list">
            <thead>
                <tr>
                    <th onclick="window.location.href='<?= getSortUrl('ISBN', $sortColumn, $sortOrder) ?>'">
                        ISBN
                        <?php if ($sortColumn === 'ISBN'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th>Picture</th>
                    <th onclick="window.location.href='<?= getSortUrl('Title', $sortColumn, $sortOrder) ?>'">
                        Title
                        <?php if ($sortColumn === 'Title'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th onclick="window.location.href='<?= getSortUrl('Author', $sortColumn, $sortOrder) ?>'">
                        Author
                        <?php if ($sortColumn === 'Author'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th onclick="window.location.href='<?= getSortUrl('PublicationYear', $sortColumn, $sortOrder) ?>'">
                        Year
                        <?php if ($sortColumn === 'PublicationYear'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th onclick="window.location.href='<?= getSortUrl('Genre', $sortColumn, $sortOrder) ?>'">
                        Genre
                        <?php if ($sortColumn === 'Genre'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th onclick="window.location.href='<?= getSortUrl('b_status', $sortColumn, $sortOrder) ?>'">
                        Status
                        <?php if ($sortColumn === 'b_status'): ?>
                            <i class="fas fa-sort-<?= $sortOrder === 'ASC' ? 'up' : 'down' ?> sort-icon"></i>
                        <?php endif; ?>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr>
                        <td><?= htmlspecialchars($book['ISBN']) ?></td>
                        <td>
                            <?php if ($book['Picture']): ?>
                                <img src="data:image/jpeg;base64,<?= base64_encode($book['Picture']) ?>" class="book-image">
                            <?php else: ?>
                                <img src="placeholder.jpg" alt="No image" class="book-image">
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($book['Title']) ?></td>
                        <td><?= htmlspecialchars($book['Author']) ?></td>
                        <td><?= htmlspecialchars($book['PublicationYear']) ?></td>
                        <td><?= htmlspecialchars($book['Genre']) ?></td>
                        <td><?= htmlspecialchars($book['b_status']) ?></td>
                        <td>
                            <div class="action-buttons">
                                <?php if ($_SESSION['role'] === 'librarian'): ?>
                                    <form id="deleteForm_<?= htmlspecialchars($book['ISBN']) ?>" action="delete_book.php" method="POST" onsubmit="event.preventDefault(); showModal('deleteForm_<?= htmlspecialchars($book['ISBN']) ?>');">
                                        <input type="hidden" name="isbn" value="<?= htmlspecialchars($book['ISBN']) ?>">
                                        <button type="submit" class="delete-button">Delete</button>
                                    </form>
                                <?php endif; ?>
                                <a href="print_book.php?isbn=<?= urlencode($book['ISBN']) ?>" class="print-button">Print</a>
                            </div>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

<script>
let formToSubmit = null;

function showModal(formId) {
    formToSubmit = document.getElementById(formId);
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'flex';
}

function closeModal() {
    const modal = document.getElementById('confirmationModal');
    modal.style.display = 'none';
    formToSubmit = null;
}

function confirmAction() {
    if (formToSubmit) {
        formToSubmit.submit(); // Submit the form
    }
    closeModal();
    setTimeout(() => {
        window.location.reload(); // Reload the page after a brief delay
    }, 500); // Adjust delay as needed
}

window.onclick = function(event) {
    const modal = document.getElementById('confirmationModal');
    if (event.target === modal) {
        closeModal();
    }
}

window.onclick = function(event) {
    const modal = document.getElementById('confirmationModal');
    if (event.target === modal) {
        closeModal();
    }
}
</script>

</html>
