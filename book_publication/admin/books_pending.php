<?php
$pageTitle = 'Pending Books';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireAdmin();

// handle approve/reject
if (isset($_GET['action'], $_GET['id'])) {
    $id = (int)$_GET['id'];
    if ($_GET['action'] === 'approve') {
        $stmt = mysqli_prepare($conn, "UPDATE books SET status='approved' WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } elseif ($_GET['action'] === 'reject') {
        $stmt = mysqli_prepare($conn, "UPDATE books SET status='rejected' WHERE id=?");
        mysqli_stmt_bind_param($stmt, 'i', $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header('Location: books_pending.php');
    exit;
}

// list pending books with publisher and subject
$sql = "SELECT b.id, b.title, b.author, b.created_at,
               u.name AS publisher_name,
               s.name AS subject_name
        FROM books b
        JOIN users u ON b.publisher_id = u.id
        LEFT JOIN subjects s ON b.subject_id = s.id
        WHERE b.status = 'pending'
        ORDER BY b.created_at DESC";
$pending = mysqli_query($conn, $sql);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Pending Books</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Author</th>
            <th>Subject</th>
            <th>Publisher</th>
            <th>Submitted</th>
            <th style="width: 180px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($pending) === 0): ?>
        <tr><td colspan="7">No pending books.</td></tr>
    <?php else: ?>
        <?php while ($b = mysqli_fetch_assoc($pending)): ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><?php echo htmlspecialchars($b['title']); ?></td>
                <td><?php echo htmlspecialchars($b['author']); ?></td>
                <td><?php echo htmlspecialchars($b['subject_name'] ?? 'N/A'); ?></td>
                <td><?php echo htmlspecialchars($b['publisher_name']); ?></td>
                <td><?php echo htmlspecialchars($b['created_at']); ?></td>
                <td>
                    <a href="../book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-secondary" target="_blank">
                        View
                    </a>
                    <a href="books_pending.php?action=approve&id=<?php echo $b['id']; ?>" 
                       class="btn btn-sm btn-success">Approve</a>
                    <a href="books_pending.php?action=reject&id=<?php echo $b['id']; ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Reject this book?');">Reject</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<a href="index.php" class="btn btn-link">← Back to dashboard</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
