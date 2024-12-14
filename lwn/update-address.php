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
    $address = '';

    try {
        if ($role === 'librarian') {
            if (!isset($_SESSION['librarian_username'])) {
                throw new Exception("Librarian username not found in session");
            }
            $username = $_SESSION['librarian_username'];
            $query = "SELECT l_address FROM librarian WHERE l_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        } else {
            if (!isset($_SESSION['user_username'])) {
                throw new Exception("User username not found in session");
            }
            $username = $_SESSION['user_username'];
            $query = "SELECT u_address FROM user WHERE u_username = :username";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        }

        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            $address = $role === 'librarian' ? $user['l_address'] : $user['u_address'];
        } else {
            throw new Exception("User not found in database. Please check your login status.");
        }

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newAddress = trim($_POST['new_address']);

            if (empty($newAddress) || strlen($newAddress) > 255) {
                throw new Exception("Invalid address! Address cannot be empty or exceed 255 characters.");
            }

            if ($newAddress === $address) {
                throw new Exception("New address must be different from current address!");
            }

            if ($role === 'librarian') {
                $updateQuery = "UPDATE librarian SET l_address = :address WHERE l_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':address', $newAddress, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            } else {
                $updateQuery = "UPDATE user SET u_address = :address WHERE u_username = :username";
                $updateStmt = $conn->prepare($updateQuery);
                $updateStmt->bindParam(':address', $newAddress, PDO::PARAM_STR);
                $updateStmt->bindParam(':username', $username, PDO::PARAM_STR);
            }

            if ($updateStmt->execute()) {
                $success = "Address updated successfully!";
                $address = $newAddress;
            } else {
                throw new Exception("Failed to update address. Please try again.");
            }
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
?>

<div class="u-p container">
    <form class="form" method="POST" id="addressUpdateForm" onsubmit="event.preventDefault(); showModal('addressUpdateForm');">
        <p class="form-title">Update Adress</p>

        <div class="input-container">
            <label for="old_address">Current Address:</label>
            <input type="tel" id="old_address" value="<?= htmlspecialchars($address) ?>" readonly>
        </div>

        <div class="input-container">
            <label for="new_address">New Address:</label>
            <input type="tel" name="new_address" id="new_address" placeholder="Enter your new address" required>
        </div>

        <!-- Display error or success messages -->
        <?php if (isset($error)): ?>
            <p class="error"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        <?php if (isset($success)): ?>
            <p class="success"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>

        <button type="submit" class="submit">Update Address</button>
    </form>
</div>

<?php
    $modalTitle = 'Confirm Changes';
    $modalMessage = 'Are you sure you want to make your changes?';
    $formId = 'addressUpdateForm';
    
    include './components/confirmation-modal.php';
?>

<?php include './components/footer.php'; ?>