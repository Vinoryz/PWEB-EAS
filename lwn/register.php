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

    if (isset($_POST['register'])) {
        $u_Username = htmlspecialchars($_POST['username']);
        // $u_Password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $u_password = $_POST['password'];
        $u_Fullname = htmlspecialchars($_POST['fullname']);
        $u_Email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $u_Phone = htmlspecialchars($_POST['phone']);
        $u_Address = htmlspecialchars($_POST['address']);

        // Check if username or email already exists
        $check_user = $conn->prepare("SELECT * FROM `user` WHERE u_username = ? OR u_Email = ?");
        $check_user->execute([$u_Username, $u_Email]);

        if ($check_user->rowCount() > 0) {
            $message = 'Username or Email is already registered.';
        } else {
            $insert_user = $conn->prepare("INSERT INTO `User` (u_Username, u_Password, u_Fullname, u_Email, u_Phone, u_Address) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_user->execute([$u_Username, $u_Password, $u_Fullname, $u_Email, $u_Phone, $u_Address]);

            if ($insert_user) {
                $message = 'Registration successful. You can now login.';
                header('location:login.php');
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
        <?php if (isset($message)) { echo '<p class="error-message">' . $message . '</p>'; } ?>
        <div class="input-container">
            <input type="text" name="username" placeholder="Enter Username" required oninput="this.value = this.value.trim();" />
        </div>
        <div class="input-container">
            <input type="password" name="password" placeholder="Enter Password" required oninput="this.value = this.value.trim();" />
        </div>
        <div class="input-container">
            <input type="text" name="fullname" placeholder="Enter Full Name" required oninput="this.value = this.value.trim();" />
        </div>
        <div class="input-container">
            <input type="email" name="email" placeholder="Enter Email" required oninput="this.value = this.value.trim();" />
        </div>
        <div class="input-container">
            <input type="text" name="phone" placeholder="Enter Phone Number" required oninput="this.value = this.value.replace(/\D/g, '');" />
        </div>
        <div class="input-container">
            <input type="text" name="address" placeholder="Enter Address" required />
        </div>
        <button type="submit" name="register" class="submit">
            Register
        </button>
        <p class="signup-link">Already have an account? <a href="./login.php">Login here</a>.</p>
        <p class="signup-link">A Librarian? <a href="./login-lib.php">Librarian login here</a></p>
    </form>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.1/jquery.min.js" integrity="sha512-aVKKRRi/Q/YV+4mjoKBsE4x3H+BkegoM/em46NNlCqNTmUYADjBbeNefNxYV7giUp0VxICtqdrbqU7iVaeZNXA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="./css-js/main.js"></script>
</body>
</html>
