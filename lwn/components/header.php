<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Library</title>
    <!-- Box-icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./css-js/style.css">
</head>
<body>

<header>
    <div class="nav container">
        <a href="./index.php" class="logo">X-<span>Library</span></a>
        <a href="./profile.php" class="login"><i class='bx bxs-user-circle'></i></a>
        <?php 
        $currentFile = basename($_SERVER['PHP_SELF']);
        $files = glob('update-*.php');
        if ($currentFile !== 'index.php' && in_array($currentFile, $files)): ?>
            <a href="u-profile.php" class="btn">Back</a>
        <?php endif; ?>
        
        <?php 
        if ($currentFile !== 'index.php' && $currentFile === 'profile.php'): ?>
            <a href="index.php" class="btn">Back</a>
        <?php endif; ?>
            
            <?php 
        if ($currentFile !== 'index.php' && $currentFile === 'u-profile.php'): ?>
            <a href="profile.php" class="btn">Back</a>
        <?php endif; ?>
            
        <?php if ($currentFile === 'index.php'): ?>
            <a href="./book_list.php" class="btn">List of books</a>
        <?php endif; ?>
            
        <?php if ($currentFile === 'book_list.php'): ?>
            <a href="./index.php" class="btn">Back</a>
        <?php endif; ?>

        <?php if ($currentFile === 'add_book.php'): ?>
            <a href="./book_list.php" class="btn">Back</a>
        <?php endif; ?>


    </div>
</header>
