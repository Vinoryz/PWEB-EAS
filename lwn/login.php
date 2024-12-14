<?php
include './components/db-connect.php';

session_start();
if (isset($_POST['login'])) {
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['pass'];

    $select_user = $conn->prepare("SELECT * FROM `user` WHERE u_username = ?");
    $select_user->execute([$username]);

    if ($select_user->rowCount() == 1) {
        $fetch_user_info = $select_user->fetch(PDO::FETCH_ASSOC);
        // In production, use password_verify() with hashed passwords
        if ($password === $fetch_user_info['u_password']) {  // Changed to match your column name
            $_SESSION['role'] = 'user';
            $_SESSION['user_username'] = $fetch_user_info['u_username'];
            header('location:index.php');
            exit();

        } else {
            $message = 'Incorrect password.';
        }
    } else {
        $message = 'Username not found.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Library Login</title>
    <!-- Box-icon -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./css-js/style.css">
</head>
<body>


<div class="login-container">
    <form class="form" action="login.php" method="POST">
        <p class="form-title">LOGIN</p>
        <div class="input-container">
            <input type="text" name="username" placeholder="Enter username" required oninput="this.value = this.value.replace(/\s/g, '')" />
        </div>
        <div class="input-container">
            <input type="password" name="pass" placeholder="Enter password" required oninput="this.value = this.value.replace(/\s/g, '')" />
        </div>
        <button type="submit" name="login" class="submit">Login</button>
        <p class="signup-link">Don't have an account? <a href="./register.php">Register here</a></p>
        <p class="signup-link">A Librarian? <a href="./login-lib.php">Librarian login here</a></p>
    </form>
</div>


<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="./css-js/main.js"></script>
</body>
</html>
