<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>X-Library Register</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="./css-js/style.css">
</head>
<body>

<?php
    include './components/db-connect.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
        $u_username = htmlspecialchars(trim($_POST['username']));
        // $u_password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Secure password hash
        $u_password =  $_POST['password'];
        $u_fullname = htmlspecialchars(trim($_POST['fullname']));
        $u_email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $u_phone = htmlspecialchars(trim($_POST['phone']));
        $u_address = htmlspecialchars(trim($_POST['address']));

        // Check if username or email already exists
        $check_user = $conn->prepare("SELECT * FROM `user` WHERE u_username = ? OR u_email = ?");
        $check_user->execute([$u_username, $u_email]);

        if ($check_user->rowCount() > 0) {
            $message = 'Username or Email is already registered.';
        } else {
            // Insert new user into the database
            $insert_user = $conn->prepare(
                "INSERT INTO `user` (u_username, u_password, u_fullname, u_email, u_phone, u_address) VALUES (?, ?, ?, ?, ?, ?)"
            );
            if ($insert_user->execute([$u_username, $u_password, $u_fullname, $u_email, $u_phone, $u_address])) {
                header('Location: login.php');
                exit();
            } else {
                $message = 'Registration failed. Please try again.';
            }
        }
    }
?>

<div class="register-container">
    <form class="form" action="register.php" method="POST">
        <p class="form-title">REGISTER</p>
        <?php if (isset($message)) { echo '<p class="error-message">' . htmlspecialchars($message) . '</p>'; } ?>
        <div class="input-container">
            <input type="text" name="username" placeholder="Enter Username" required />
        </div>
        <div class="input-container">
            <input type="password" name="password" placeholder="Enter Password" required />
        </div>
        <div class="input-container">
            <input type="text" name="fullname" placeholder="Enter Full Name" required />
        </div>
        <div class="input-container">
            <input type="email" name="email" placeholder="Enter Email" required />
        </div>
        <div class="input-container">
            <input type="text" name="phone" placeholder="Enter Phone Number" required pattern="[0-9]+" title="Phone number should only contain digits." />
        </div>
        <div class="input-container">
            <input type="text" name="address" placeholder="Enter Address" required />
        </div>
        <button type="submit" name="register" class="submit">
            Register
        </button>
        <p class="signup-link">Already have an account? <a href="login.php">Login here</a>.</p>
        <p class="signup-link">A Librarian? <a href="login-lib.php">Librarian login here</a></p>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="./css-js/main.js"></script>
</body>
</html>
