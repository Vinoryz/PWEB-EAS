<?php
    include './components/db-connect.php';
    session_start();

    // Redirect if not logged in
    if (!isset($_SESSION['role'])) {
        header('location:./login.php');
        exit();
    }

    include './components/header.php'; 

?>

<div class="u-p container">
    <!-- Form to select a field to update -->
    <form class="form" method="POST" id="field-update-form">
        <p class="form-title">Choose a Field to Update</p>
        <div class="input-container">
            <select name="field" id="field-selector" required>
                <option value="" disabled selected>Select Field</option>
                    <option value="email">Email</option>
                    <option value="phone">Phone</option>
                    <option value="address">Address</option>
                    <option value="password">Password</option>
            </select>
        </div>
        <button type="button" class="submit" onclick="redirectToUpdate()">Go to Update</button>
    </form>

    <!-- Form to delete the account -->
    <form class="delete-form" method="POST" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
        <p class="delete-form-title">Delete Your Account</p>
        <button type="submit" name="delete" class="submit delete">
            Delete Account
        </button>
    </form>
</div>

<script>
    function redirectToUpdate() {
        const field = document.getElementById('field-selector').value;
        if (field) {
            // Redirect based on the selected field
            window.location.href = `update-${field}.php`;
        } else {
            alert('Please select a field to update.');
        }
    }
</script>

<?php 
    include './components/footer.php'; 
?>
