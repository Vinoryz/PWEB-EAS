<?php 
    include './components/db-connect.php';
    session_start();
    include './components/header.php'; 
    if (!isset($_SESSION['role'])) {
        header('location:./login.php');
        exit();
    }


    if ($_SESSION['role'] === 'user') {
        $username = $_SESSION['user_username'];
        // User-specific content
    } elseif ($_SESSION['role'] === 'librarian') {
        $username = $_SESSION['librarian_username'];
        // Librarian-specific content
    } else {
        header('location:./login.php');
        exit();
    }
?>

<section class="home" id="home">
    <div class="home-text container">
        <h2 class="home-title">X-Library</h2>
        <span class="home-subtitle">Your gateway to knowledge.</span>
    </div>
</section>

<div class="post-filter container">
    <span class="filter-item active-filter" data-filter="all">All</span>

    <?php
        // First, let's get unique genres from the book table
        $select_shelf = $conn->prepare("SELECT ShelfName FROM `shelf`");
        $select_shelf->execute();
        if($select_shelf->rowCount() > 0){
            while($fetch_shelf = $select_shelf->fetch(PDO::FETCH_ASSOC)){
    ?>
    <span class="filter-item" data-filter="<?= $fetch_shelf['ShelfName']; ?>">
        <?= $fetch_shelf['ShelfName']; ?>
    </span>
    <?php
            }
        } else {
            echo '<p class="empty">No genres added yet!</p>';
        }
    ?>
</div>

<div class="post container">
    <?php
        // Updated query to use Genre instead of shelfName
        $select_books = $conn->prepare("SELECT * FROM `book` WHERE b_status = 'Available'");
        $select_books->execute();
        if($select_books->rowCount() > 0){
            while($fetch_books = $select_books->fetch(PDO::FETCH_ASSOC)){
    ?>
        <div class="post-box <?= $fetch_books['Genre']; ?>">
            <img src="data:image/jpeg;base64,<?= base64_encode($fetch_books['Picture']); ?>" class="post-img" />
            <h2 class="category"><?= $fetch_books['Genre']; ?></h2>
            <a href="#" class="post-title"><?= $fetch_books['Title']; ?></a>
            <span class="post-date">ISBN: <?= $fetch_books['ISBN']; ?></span>
            <p class="post-description"><?= $fetch_books['Synopsis']; ?></p>
            <div class="profile">
                <span class="profile-name"><?= $fetch_books['Author']; ?></span>
            </div>
        </div>
    <?php
            }
        } else {
            echo '<p class="empty">No books available yet!</p>';
        }
    ?>
</div>

<?php 
    include './components/footer.php'; 
?>