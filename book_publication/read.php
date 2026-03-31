<?php
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';

$bookId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = mysqli_prepare($conn, "SELECT title, pdf_file FROM books WHERE id=? AND status='approved'");
mysqli_stmt_bind_param($stmt, 'i', $bookId);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$book = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$book || !$book['pdf_file']) {
    $pageTitle = 'Book not found';
    require __DIR__ . '/includes/header.php';
    echo '<div class="alert alert-danger">Book or PDF not found.</div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$pageTitle = 'Read: ' . $book['title'];
require __DIR__ . '/includes/header.php';
?>

<h1 class="mb-3">Reading: <?php echo htmlspecialchars($book['title']); ?></h1>

<div class="ratio ratio-4x3">
    <iframe src="uploads/pdfs/<?php echo htmlspecialchars($book['pdf_file']); ?>" 
            frameborder="0"></iframe>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
