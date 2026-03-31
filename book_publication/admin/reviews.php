<?php
$pageTitle = 'Manage Reviews';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireAdmin();

// delete inappropriate review
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM reviews WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: reviews.php');
    exit;
}

$sql = "SELECT r.id, r.rating, r.comment, r.created_at,
               b.title AS book_title,
               u.name AS customer_name
        FROM reviews r
        JOIN books b ON r.book_id = b.id
        JOIN users u ON r.customer_id = u.id
        ORDER BY r.created_at DESC";
$reviews = mysqli_query($conn, $sql);

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">All Reviews</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Book</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
            <th style="width: 100px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($reviews) === 0): ?>
        <tr><td colspan="7">No reviews yet.</td></tr>
    <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['book_title']); ?></td>
                <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
                <td><span class="badge bg-primary"><?php echo (int)$r['rating']; ?>/5</span></td>
                <td><?php echo nl2br(htmlspecialchars($r['comment'])); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
                <td>
                    <a href="reviews.php?delete=<?php echo $r['id']; ?>" 
                       class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this review?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<a href="index.php" class="btn btn-link">← Back to dashboard</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
