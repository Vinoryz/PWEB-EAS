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
    $password = '';

    try {
        if ($role === 'librarian') {
            if (!isset($_SESSION['librarian_username'])) {
                throw new Exception("Librarian username not found in session");
            }
            $username = $_SESSION['librarian_username'];
            $query = "SELECT l_password FROM librarian WHERE l_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        } else {
            if (!isset($_SESSION['user_username'])) {
                throw new Exception("User username not found in session");
            }
            $username = $_SESSION['user_username'];
            $query = "SELECT u_password FROM user WHERE u_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $password = $role === 'librarian' ? $user['l_password'] : $user['u_password'];
        } else {
            throw new Exception("User not found in database. Please check your login status.");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPassword = trim($_POST['new_password']);

            if (empty($newPassword) || strlen($newPassword) < 8) {
                throw new Exception("Invalid password! password cannot be empty or less than 8 characters.");
            }

            if ($newPassword === $password) {
                throw new Exception("New password must be different from current password!");
            }

            if ($role === 'librarian') {
                $updateQuery = "UPDATE librarian SET l_password = :password WHERE l_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            } else {
                $updateQuery = "UPDATE user SET u_password = :password WHERE u_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':password', $newPassword, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            }

            if ($updateStmt->execute()) {
                $success = "Password updated successfully!";
                $password = $newPassword;
            } else {
                throw new Exception("Failed to update password. Please try again.");
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
?>

<div class="u-p container">
    <form class="form" method="POST" id="passwordUpdateForm" onsubmit="event.preventDefault(); showModal('passwordUpdateForm');">
        <p class="form-title">Update Password</p>

        <div class="input-container">
            <label for="old_password">Current Password:</label>
            <div class="password-field">
                <input type="password" id="old_password" value="<?= htmlspecialchars($password) ?>" readonly>
                <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('old_password', this)"></i>
            </div>
        </div>

        <div class="input-container">
            <label for="new_password">New Password:</label>
            <div class="password-field">
                <input type="password" name="new_password" id="new_password" placeholder="Enter your new password" required>
                <i class="fas fa-eye-slash password-toggle" onclick="togglePassword('new_password', this)"></i>
            </div>
        </div>

        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <button type="submit" class="submit">Update Password</button>
    </form>
</div>

<?php
    $modalTitle = 'Confirm Changes';
    $modalMessage = 'Are you sure you want to make your changes?';
    $formId = 'passwordUpdateForm';
    
    include './components/confirmation-modal.php';
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<style>
.password-field {
    position: relative;
    width: 100%;
}

.password-field input {
    width: 100%;
    padding-right: 40px;
}

.password-toggle {
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    color: #666;
    padding: 5px;
}

.password-toggle:hover {
    color: #333;
}
</style>

<script>
function togglePassword(inputId, icon) {
    const input = document.getElementById(inputId);
    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
    } else {
        input.type = "password";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
    }
}
</script>

<?php include './components/footer.php'; ?>