<?php
$pageTitle = 'Manage Users';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireAdmin();

// handle actions: change status / role
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int) $_GET['id'];
    $action = $_GET['action'];

    if ($id !== (int)$_SESSION['user_id']) { // prevent self-block
        if ($action === 'block') {
            $stmt = mysqli_prepare($conn, "UPDATE users SET status='blocked' WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } elseif ($action === 'unblock') {
            $stmt = mysqli_prepare($conn, "UPDATE users SET status='active' WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } elseif (in_array($action, ['make_admin','make_publisher','make_customer'], true)) {
            $newRole = $action === 'make_admin' ? 'admin' : ($action === 'make_publisher' ? 'publisher' : 'customer');
            $stmt = mysqli_prepare($conn, "UPDATE users SET role=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'si', $newRole, $id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }

    header('Location: users.php');
    exit;
}

// fetch all users
$sql = "SELECT id, name, email, role, status, created_at FROM users ORDER BY created_at DESC";
$users = mysqli_query($conn, $sql);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Manage Users</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Status</th>
            <th>Created</th>
            <th style="width: 250px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php while ($u = mysqli_fetch_assoc($users)): ?>
        <tr>
            <td><?php echo $u['id']; ?></td>
            <td><?php echo htmlspecialchars($u['name']); ?></td>
            <td><?php echo htmlspecialchars($u['email']); ?></td>
            <td><?php echo htmlspecialchars($u['role']); ?></td>
            <td>
                <span class="badge bg-<?php echo $u['status'] === 'active' ? 'success' : 'secondary'; ?>">
                    <?php echo htmlspecialchars($u['status']); ?>
                </span>
            </td>
            <td><?php echo htmlspecialchars($u['created_at']); ?></td>
            <td>
                <?php if ($u['id'] !== (int)$_SESSION['user_id']): ?>
                    <?php if ($u['status'] === 'active'): ?>
                        <a href="users.php?action=block&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Block this user?');">Block</a>
                    <?php else: ?>
                        <a href="users.php?action=unblock&id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-success">Unblock</a>
                    <?php endif; ?>

                    <div class="btn-group btn-group-sm mt-1" role="group">
                        <a href="users.php?action=make_admin&id=<?php echo $u['id']; ?>" class="btn btn-outline-dark">Admin</a>
                        <a href="users.php?action=make_publisher&id=<?php echo $u['id']; ?>" class="btn btn-outline-primary">Publisher</a>
                        <a href="users.php?action=make_customer&id=<?php echo $u['id']; ?>" class="btn btn-outline-secondary">Customer</a>
                    </div>
                <?php else: ?>
                    <em>Your account</em>
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<a href="index.php" class="btn btn-link">← Back to dashboard</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
