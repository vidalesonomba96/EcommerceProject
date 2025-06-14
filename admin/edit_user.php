<?php
// admin/edit_user.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

$user_id_to_edit = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id_to_edit === 0) {
    header("Location: manage_users.php");
    exit();
}

$stmt = $conn->prepare("SELECT id, fullname, username, email, is_admin FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id_to_edit);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header("Location: manage_users.php?message=" . urlencode("User not found."));
    exit();
}

$page_title = "Edit User: " . htmlspecialchars($user['username']);
include 'header.php';
?>

<div class="admin-container form-container">
    <h1 class="admin-title">Edit User: <?php echo htmlspecialchars($user['username']); ?></h1>

    <?php if(isset($_GET['error'])): ?>
        <p class="auth-message error" style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 1rem;"><?php echo htmlspecialchars(urldecode($_GET['error'])); ?></p>
    <?php endif; ?>
    
    <!-- This form action is now corrected to point to update_user_handler.php -->
    <form action="update_user_handler.php" method="POST">
        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
        
        <div class="form-group">
            <label for="fullname">Full Name</label>
            <input type="text" id="fullname" name="fullname" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="is_admin">Role</label>
            <select name="is_admin" id="is_admin">
                <option value="0" <?php echo !$user['is_admin'] ? 'selected' : ''; ?>>User</option>
                <option value="1" <?php echo $user['is_admin'] ? 'selected' : ''; ?>>Admin</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Update User</button>
        <a href="manage_users.php" class="btn">Cancel</a>
    </form>
</div>

<?php include 'footer.php'; ?>
