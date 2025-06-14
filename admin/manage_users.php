<?php
// admin/manage_users.php
require_once 'admin_auth.php';
require_once '../db_connect.php';

// Handle user deletion
if (isset($_GET['delete_user'])) {
    $user_id_to_delete = intval($_GET['delete_user']);
    // You should not be able to delete your own account
    if ($user_id_to_delete !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id_to_delete);
        $stmt->execute();
    }
    header("Location: manage_users.php?message=" . urlencode("User deleted successfully."));
    exit();
}

$page_title = "Manage Users";
include 'header.php';

$sql = "SELECT id, fullname, username, email, created_at, is_admin FROM users ORDER BY created_at DESC";
$result = $conn->query($sql);
$users = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="table-container">
    <h1 class="admin-title" style="text-align: center; color: #374151; margin-bottom: 2rem;">Manage Users</h1>

    <?php if(isset($_GET['message'])): ?>
        <p class="auth-message success" style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 1rem;"><?php echo htmlspecialchars(urldecode($_GET['message'])); ?></p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Full Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Registered At</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
            <tr>
                <td><?php echo $user['id']; ?></td>
                <td><?php echo htmlspecialchars($user['fullname']); ?></td>
                <td><?php echo htmlspecialchars($user['username']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td><?php echo $user['created_at']; ?></td>
                <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
                <td class="actions">
                    <!-- This link is now corrected to point to edit_user.php -->
                    <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-primary">Edit</a>
                    <?php if ($user['id'] !== $_SESSION['user_id']): // Prevent deleting own account ?>
                        <a href="?delete_user=<?php echo $user['id']; ?>" onclick="return confirm('Are you sure you want to delete this user?');" class="btn-danger">Delete</a>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'footer.php'; ?>
