<?php
$pageTitle = 'My Reviews';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireCustomer();
require __DIR__ . '/../includes/header.php';

$customerId = $_SESSION['user_id'];

$sql = "SELECT r.id, r.rating, r.comment, r.created_at, b.title
        FROM reviews r
        JOIN books b ON r.book_id = b.id
        WHERE r.customer_id = ?
        ORDER BY r.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $customerId);
mysqli_stmt_execute($stmt);
$reviews = mysqli_stmt_get_result($stmt);
?>

<h1 class="mb-4">My Reviews</h1>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Book</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($reviews) === 0): ?>
        <tr><td colspan="5">You have not written any reviews yet.</td></tr>
    <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
            <tr>
                <td><?php echo $r['id']; ?></td>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
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
