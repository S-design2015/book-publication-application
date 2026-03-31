<?php
$pageTitle = 'Reviews on My Books';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requirePublisher();
require __DIR__ . '/../includes/header.php';

$publisherId = $_SESSION['user_id'];

$sql = "SELECT r.id, r.rating, r.comment, r.created_at,
               b.title AS book_title,
               u.name AS customer_name
        FROM reviews r
        JOIN books b ON r.book_id = b.id
        JOIN users u ON r.customer_id = u.id
        WHERE b.publisher_id = ?
        ORDER BY r.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$reviews = mysqli_stmt_get_result($stmt);
?>

<h1 class="mb-4">Reviews on Your Books</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Book</th>
            <th>Customer</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($reviews) === 0): ?>
        <tr><td colspan="6">No reviews yet.</td></tr>
    <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['book_title']); ?></td>
                <td><?php echo htmlspecialchars($r['customer_name']); ?></td>
                <td><span class="badge bg-primary"><?php echo (int)$r['rating']; ?>/5</span></td>
                <td><?php echo nl2br(htmlspecialchars($r['comment'])); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<a href="index.php" class="btn btn-link">← Back to dashboard</a>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . '/../includes/footer.php';
?>
