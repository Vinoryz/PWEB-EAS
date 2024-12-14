<?php
    include './components/db-connect.php';
    session_start();

    // Redirect if not logged in
    if (!isset($_SESSION['role'])) {
        header('location:./login.php');
        exit();
    }

    include './components/header.php'; 

    $role = $_SESSION['role'];

    if ($role === 'user') {
        $username = $_SESSION['user_username'];

        $select_profile = $conn->prepare("SELECT * FROM `user` WHERE u_username = ?");
        $select_profile->execute([$username]);

        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="u-p container">
                <div class="profile-box tech">
                    <div class="p-i">
                        <img src="./images/pro.jpg" alt="User Profile" class="p-img">
                    </div>
                    <h2 class="p-category"><?= htmlspecialchars($fetch_profile['u_username']); ?></h2>
                    <span class="p-date">Role: <?= $_SESSION['role']?></span>
                    <br>
                    <div class="bt">
                        <a href="u-profile.php" class="p-btn">Edit Profile</a>
                        <a href="logout.php" class="p-btn">Logout</a>
                    </div>
                </div>
            </div>
            <?php
        } else {
            echo '<p class="error">User profile not found.</p>';
        }
    } elseif ($role === 'librarian') {
        $username = $_SESSION['librarian_username'];

        $select_profile = $conn->prepare("SELECT * FROM `librarian` WHERE l_username = ?");
        $select_profile->execute([$username]);

        if ($select_profile->rowCount() > 0) {
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="u-p container">
                <div class="profile-box tech">
                    <div class="p-i">
                        <img src="./images/pro.jpg" alt="Librarian Profile" class="p-img">
                    </div>
                    <h2 class="p-category"><?= htmlspecialchars($fetch_profile['l_username']); ?></h2>
                    <span class="p-date">Role: <?= $_SESSION['role']?></span>
                    <br>
                    <div class="bt">
                        <a href="u-profile.php" class="p-btn">Edit Profile</a>
                        <a href="logout.php" class="p-btn">Logout</a>
                    </div>
                </div>
            </div>
            <?php
        } else {
            echo '<p class="error">Librarian profile not found.</p>';
        }
    } else {
        echo '<p class="error">Invalid role. Please login again.</p>';
        session_destroy();
    }

    include './components/footer.php'; 
?>
