<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$publisherId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT id, name FROM users WHERE id=? AND role='publisher'");
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$publisher = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$publisher) {
    $pageTitle = 'Publisher not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Publisher not found.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Publisher: ' . $publisher['name'];
require __DIR__ . '/includes/header.php';

$sql = "SELECT id, title, author, cover_image 
        FROM books 
        WHERE publisher_id=? AND status='approved'
        ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$books = mysqli_stmt_get_result($stmt);
?>

<h1 class="mb-4">Books by <?php echo htmlspecialchars($publisher['name']); ?></h1>

<div class="row g-3">
<?php if (mysqli_num_rows($books) === 0): ?>
    <p>No approved books for this publisher yet.</p>
<?php else: ?>
    <?php while ($b = mysqli_fetch_assoc($books)): ?>
        <div class="col-md-3 col-sm-6">
            <div class="card h-100 shadow-sm border-0">
                <?php if ($b['cover_image']): ?>
                    <img src="uploads/covers/<?php echo htmlspecialchars($b['cover_image']); ?>" 
                         class="card-img-top" alt="Cover">
                <?php endif; ?>
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($b['title']); ?></h5>
                    <p class="card-text">
                        <small class="text-muted"><?php echo htmlspecialchars($b['author']); ?></small>
                    </p>
                    <a href="book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary">Details</a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . '/includes/footer.php';
?>
