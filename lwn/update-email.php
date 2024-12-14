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
    $email = '';

    try {
        if ($role === 'librarian') {
            if (!isset($_SESSION['librarian_username'])) {
                throw new Exception("Librarian username not found in session");
            }
            $username = $_SESSION['librarian_username'];
            $query = "SELECT l_email FROM librarian WHERE l_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        } else {
            if (!isset($_SESSION['user_username'])) {
                throw new Exception("User username not found in session");
            }
            $username = $_SESSION['user_username'];
            $query = "SELECT u_email FROM user WHERE u_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $email = $role === 'librarian' ? $user['l_email'] : $user['u_email'];
        } else {
            throw new Exception("User not found in database. Please check your login status.");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newEmail = trim($_POST['new_email']);

            // Validate email format
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception("Invalid email format!");
            }

            // Check if email is different from current
            if ($newEmail === $email) {
                throw new Exception("New email must be different from current email!");
            }

            if ($role === 'librarian') {
                $updateQuery = "UPDATE librarian SET l_email = :email WHERE l_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':email', $newEmail, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            } else {
                $updateQuery = "UPDATE user SET u_email = :email WHERE u_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':email', $newEmail, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            }

            if ($updateStmt->execute()) {
                $success = "Email updated successfully!";
                $email = $newEmail; // Reflect the updated email
            } else {
                throw new Exception("Failed to update email. Please try again.");
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
?>

<div class="u-p container">
    <form class="form" method="POST" id="emailUpdateForm" onsubmit="event.preventDefault(); showModal('emailUpdateForm');">
        <p class="form-title">Update Email</p>
        <br>

        <!-- Display old email -->
        <div class="input-container">
            <label for="old_email">Current Email:</label>
            <input type="email" id="old_email" value="<?= htmlspecialchars($email) ?>" readonly>
        </div>

        <!-- Input new email -->
        <div class="input-container">
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" id="new_email" placeholder="Enter your new email" required>
        </div>

        <!-- Display error or success messages -->
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <button type="submit" class="submit">Update Email</button>
    </form>
</div>

<?php
    $modalTitle = 'Confirm Changes';
    $modalMessage = 'Are you sure you want to make your changes?';
    $formId = 'emailUpdateForm';
    
    include './components/confirmation-modal.php';
?>

<?php include './components/footer.php'; ?>