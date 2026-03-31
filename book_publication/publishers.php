<?php
$pageTitle = 'Publishers';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/header.php';

$sql = "SELECT id, name, email, created_at FROM users 
        WHERE role='publisher' AND status='active'
        ORDER BY name ASC";
$pubs = mysqli_query($conn, $sql);
?>

<h1 class="mb-4">Publishers</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Since</th>
            <th>Books</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($pubs) === 0): ?>
        <tr><td colspan="4">No publishers yet.</td></tr>
    <?php else: ?>
        <?php while ($p = mysqli_fetch_assoc($pubs)): ?>
            <tr>
                <td><?php echo htmlspecialchars($p['name']); ?></td>
                <td><?php echo htmlspecialchars($p['email']); ?></td>
                <td><?php echo htmlspecialchars($p['created_at']); ?></td>
                <td>
                    <a href="publisher_books.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-primary">
                        View books
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/includes/footer.php'; ?>
