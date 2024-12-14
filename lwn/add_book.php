<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Book</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./css-js/style.css">
</head>
<body>

<?php
    include './components/db-connect.php';
    include './components/header.php'; 

    $message = '';

    if (isset($_POST['add_book'])) {
        $isbn = htmlspecialchars($_POST['isbn']);
    
        // Validate ISBN format
        if (!preg_match('/^(?:\d{9}[\dXx]|\d{13})$/', $isbn)) {
            $message = 'Invalid ISBN format. It must be either 10 or 13 characters long.';
        } else {
            // Sanitize inputs
            $title = $_POST['title'];
            $author = htmlspecialchars($_POST['author'], ENT_QUOTES, 'UTF-8');
            $publisher = htmlspecialchars($_POST['publisher'], ENT_QUOTES, 'UTF-8');
            $publication_year = intval($_POST['publication_year']);
            $genre = htmlspecialchars($_POST['genre'], ENT_QUOTES, 'UTF-8');
            $synopsis = $_POST['synopsis'];
    
            // Handle file upload
            $picture = null;
            if (!empty($_FILES['picture']['tmp_name'])) {
                $picture = file_get_contents($_FILES['picture']['tmp_name']);
            }
    
            try {
                // Fetch or insert the shelf ID
                $query_shelf = $conn->prepare("SELECT shelfID FROM shelf WHERE shelfName = ?");
                $query_shelf->execute([$genre]);
                $shelf_id = $query_shelf->fetchColumn();
    
                if (!$shelf_id) {
                    $insert_shelf = $conn->prepare("INSERT INTO shelf (shelfName) VALUES (?)");
                    $insert_shelf->execute([$genre]);
                    $shelf_id = $conn->lastInsertId();
                }
    
                // Insert the book into the database
                $insert_book = $conn->prepare(
                    "INSERT INTO book (ISBN, Picture, Title, Author, Publisher, PublicationYear, Genre, Synopsis, shelfID) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );
                $insert_book->execute([
                    $isbn, $picture, $title, $author, $publisher, $publication_year, $genre, $synopsis, $shelf_id
                ]);
    
                $message = 'Book added successfully.';
            } catch (Exception $e) {
                $message = 'Failed to add book: ' . $e->getMessage();
            }
        }
    }
?>

<div class="register-container">
    <form class="form" action="add_book.php" method="POST" enctype="multipart/form-data">
        <p class="form-title">ADD BOOK</p>
        <?php if (!empty($message)) { echo '<p class="error-message">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>'; } ?>
        
        <div class="input-container">
            <input type="text" name="isbn" placeholder="Enter ISBN (10 or 13 digits)" maxlength="13" required />
        </div>
        <div class="input-container">
            <input type="file" name="picture" accept="image/*" />
        </div>
        <div class="input-container">
            <input type="text" name="title" placeholder="Enter Title" required />
        </div>
        <div class="input-container">
            <input type="text" name="author" placeholder="Enter Author" required />
        </div>
        <div class="input-container">
            <input type="text" name="publisher" placeholder="Enter Publisher" required />
        </div>
        <div class="input-container">
            <input type="number" name="publication_year" placeholder="Enter Publication Year" min="1000" max="9999" required />
        </div>
        <div class="input-container">
            <input type="text" name="genre" placeholder="Enter Genre" required />
        </div>
        <div class="synopsis-container">
            <textarea name="synopsis" placeholder="Enter Synopsis" maxlength="4096" required></textarea>
        </div>

        <button type="submit" name="add_book" class="submit">Add Book</button>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
    function validateISBN() {
        const isbnInput = document.querySelector('input[name="isbn"]');
        const isbn = isbnInput.value.trim();
        const isbnPattern = /^(?:\d{9}[\dXx]|\d{13})$/;

        if (!isbnPattern.test(isbn)) {
            alert('Please enter a valid ISBN (10 or 13 characters, without hyphens).');
            isbnInput.focus();
            return false;
        }
        return true;
    }

    document.querySelector('form').addEventListener('submit', function (event) {
        if (!validateISBN()) {
            event.preventDefault();
        }
    });
</script>

</body>
</html>
