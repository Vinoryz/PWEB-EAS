<?php
include './components/db-connect.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isbn = $_POST['isbn'] ?? '';

    if ($isbn) {
        try {
            $query = "DELETE FROM book WHERE ISBN = :isbn";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':isbn', $isbn, PDO::PARAM_STR);

            if ($stmt->execute()) {
                header("Location: book_list.php?message=Book deleted successfully.");
                exit();
            } else {
                throw new Exception("Failed to delete the book.");
            }
        } catch (Exception $e) {
            header("Location: book_list.php?error=" . urlencode($e->getMessage()));
            exit();
        }
    } else {
        header("Location: book_list.php?error=Invalid ISBN provided.");
        exit();
    }
}
?>
