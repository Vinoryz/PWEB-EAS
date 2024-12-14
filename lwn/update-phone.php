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
    $phone = '';

    try {
        if ($role === 'librarian') {
            if (!isset($_SESSION['librarian_username'])) {
                throw new Exception("Librarian username not found in session");
            }
            $username = $_SESSION['librarian_username'];
            $query = "SELECT l_phone FROM librarian WHERE l_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        } else {
            if (!isset($_SESSION['user_username'])) {
                throw new Exception("User username not found in session");
            }
            $username = $_SESSION['user_username'];
            $query = "SELECT u_phone FROM user WHERE u_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $phone = $role === 'librarian' ? $user['l_phone'] : $user['u_phone'];
        } else {
            throw new Exception("User not found in database. Please check your login status.");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newPhone = trim($_POST['new_phone']);

            // Basic phone number validation (you might want to adjust this based on your requirements)
            if (empty($newPhone) || strlen($newPhone) < 10 || strlen($newPhone) > 15) {
                throw new Exception("Invalid phone number format! Please enter a valid phone number.");
            }

            // Check if phone is different from current
            if ($newPhone === $phone) {
                throw new Exception("New phone number must be different from current number!");
            }

            if ($role === 'librarian') {
                $updateQuery = "UPDATE librarian SET l_phone = :phone WHERE l_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':phone', $newPhone, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            } else {
                $updateQuery = "UPDATE user SET u_phone = :phone WHERE u_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':phone', $newPhone, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            }

            if ($updateStmt->execute()) {
                $success = "Phone number updated successfully!";
                $phone = $newPhone; // Reflect the updated phone
            } else {
                throw new Exception("Failed to update phone number. Please try again.");
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
?>

<div class="u-p container">
    <form class="form" method="POST" id="phoneUpdateForm" onsubmit="event.preventDefault(); showModal('phoneUpdateForm');">
        <p class="form-title">Update Phone Number</p>

        <!-- Display old phone -->
        <div class="input-container">
            <label for="old_phone">Current Phone Number:</label>
            <input type="tel" id="old_phone" value="<?= htmlspecialchars($phone) ?>" readonly>
        </div>

        <!-- Input new phone -->
        <div class="input-container">
            <label for="new_phone">New Phone Number:</label>
            <input type="tel" name="new_phone" id="new_phone" placeholder="Enter your new phone number" required>
        </div>

        <!-- Display error or success messages -->
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <button type="submit" class="submit">Update Phone Number</button>
    </form>
</div>

<?php
    $modalTitle = 'Confirm Changes';
    $modalMessage = 'Are you sure you want to make your changes?';
    $formId = 'phoneUpdateForm';
    
    include './components/confirmation-modal.php';
?>


<?php include './components/footer.php'; ?>