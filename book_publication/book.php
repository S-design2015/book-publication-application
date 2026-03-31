<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// increment views
$stmt = mysqli_prepare($conn, "UPDATE books SET views = views + 1 WHERE id=? AND status='approved'");
mysqli_stmt_bind_param($stmt, 'i', $bookId);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// load book
$sql = "SELECT b.*, s.name AS subject_name, u.name AS publisher_name
        FROM books b
        LEFT JOIN subjects s ON b.subject_id = s.id
        LEFT JOIN users u ON b.publisher_id = u.id
        WHERE b.id=? AND b.status='approved'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $bookId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$book) {
    $pageTitle = 'Book not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Book not found or not approved.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = $book['title'];
require __DIR__ . '/includes/header.php';

// handle new review
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['rating'])) {
    requireCustomer(); // only customers can post
    $rating  = (int)($_POST['rating'] ?? 0);
    $comment = trim($_POST['comment'] ?? '');
    if ($rating < 1 || $rating > 5) $errors[] = 'Rating must be between 1 and 5.';

    if (empty($errors)) {
        $sql = "INSERT INTO reviews (book_id, customer_id, rating, comment)
                VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'iiis', $bookId, $_SESSION['user_id'], $rating, $comment);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: book.php?id=" . $bookId);
        exit;
    }
}

// load reviews
$sql = "SELECT r.*, u.name AS customer_name
        FROM reviews r
        JOIN users u ON r.customer_id = u.id
        WHERE r.book_id=?
        ORDER BY r.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $bookId);
mysqli_stmt_execute($stmt);
$reviews = mysqli_stmt_get_result($stmt);
?>

<div class="row mb-4">
    <div class="col-md-4">
        <?php if ($book['cover_image']): ?>
            <img src="uploads/covers/<?php echo htmlspecialchars($book['cover_image']); ?>"
                 alt="Cover" class="img-fluid rounded shadow-sm mb-3">
        <?php endif; ?>
    </div>
    <div class="col-md-8">
        <h1><?php echo htmlspecialchars($book['title']); ?></h1>
        <p class="mb-1"><strong>Author:</strong> <?php echo htmlspecialchars($book['author']); ?></p>
        <p class="mb-1"><strong>Subject:</strong> <?php echo htmlspecialchars($book['subject_name'] ?? 'General'); ?></p>
        <p class="mb-1"><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher_name']); ?></p>
        <p class="mb-1"><strong>ISBN:</strong> <?php echo htmlspecialchars($book['isbn']); ?></p>
        <p class="mb-3"><strong>Views:</strong> <?php echo (int)$book['views'] + 1; // already incremented ?></p>
        <p><?php echo nl2br(htmlspecialchars($book['description'])); ?></p>

        <a href="read.php?id=<?php echo $book['id']; ?>" class="btn btn-primary me-2" target="_blank">
            Read Online
        </a>
    </div>
</div>

<h3 class="mb-3">Reviews</h3>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if (isLoggedIn() && getUserRole() === 'customer'): ?>
    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Write a review</h5>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Rating (1–5)</label>
                    <select name="rating" class="form-select" required>
                        <option value="">Select rating</option>
                        <?php for ($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Comment</label>
                    <textarea name="comment" rows="3" class="form-control"></textarea>
                </div>
                <button type="submit" class="btn btn-success">Submit Review</button>
            </form>
        </div>
    </div>
<?php else: ?>
    <p><em>Login as customer to write a review.</em></p>
<?php endif; ?>

<?php if (mysqli_num_rows($reviews) === 0): ?>
    <p>No reviews yet.</p>
<?php else: ?>
    <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
        <div class="mb-3">
            <strong><?php echo htmlspecialchars($r['customer_name']); ?></strong>
            <span class="badge bg-primary"><?php echo (int)$r['rating']; ?>/5</span>
            <small class="text-muted"><?php echo htmlspecialchars($r['created_at']); ?></small>
            <p class="mb-0"><?php echo nl2br(htmlspecialchars($r['comment'])); ?></p>
        </div>
        <hr>
    <?php endwhile; ?>
<?php endif; ?>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . '/includes/footer.php';
?>
