<?php
$pageTitle = 'Publisher Dashboard';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requirePublisher();
require __DIR__ . '/../includes/header.php';

$publisherId = $_SESSION['user_id'];

$stats = [
    'total_books'   => 0,
    'pending_books' => 0,
    'approved'      => 0,
    'rejected'      => 0,
    'total_views'   => 0,
    'total_reviews' => 0,
];

// book counts
$sql = "SELECT 
    COUNT(*) AS total_books,
    SUM(status='pending')  AS pending_books,
    SUM(status='approved') AS approved,
    SUM(status='rejected') AS rejected,
    COALESCE(SUM(views),0) AS total_views
  FROM books
  WHERE publisher_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $stats = array_merge($stats, $row);
}
mysqli_stmt_close($stmt);

// total reviews for their books
$sql = "SELECT COUNT(r.id) AS total_reviews
        FROM reviews r 
        JOIN books b ON r.book_id = b.id
        WHERE b.publisher_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
if ($row = mysqli_fetch_assoc($result)) {
    $stats['total_reviews'] = $row['total_reviews'];
}
mysqli_stmt_close($stmt);

// latest books
$sql = "SELECT b.id, b.title, b.status, s.name AS subject_name, b.created_at
        FROM books b
        LEFT JOIN subjects s ON b.subject_id = s.id
        WHERE b.publisher_id = ?
        ORDER BY b.created_at DESC
        LIMIT 5";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$latestBooks = mysqli_stmt_get_result($stmt);
?>

<h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Your Books</h6>
                <h3><?php echo $stats['total_books']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Pending</h6>
                <h3><?php echo $stats['pending_books']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Approved</h6>
                <h3><?php echo $stats['approved']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Rejected</h6>
                <h3><?php echo $stats['rejected']; ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Total Views</h6>
                <h3><?php echo $stats['total_views']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Total Reviews</h6>
                <h3><?php echo $stats['total_reviews']; ?></h3>
            </div>
        </div>
    </div>
</div>
<a href="reviews.php" class="btn btn-outline-secondary btn-sm mt-2">View Reviews</a>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Your Latest Books</h4>
    <a href="books.php" class="btn btn-primary btn-sm">Manage All Books</a>
</div>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th>Title</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Created</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($latestBooks) === 0): ?>
        <tr><td colspan="4">No books yet. <a href="book_add.php">Add your first book</a>.</td></tr>
    <?php else: ?>
        <?php while ($b = mysqli_fetch_assoc($latestBooks)): ?>
            <tr>
                <td><?php echo htmlspecialchars($b['title']); ?></td>
                <td><?php echo htmlspecialchars($b['subject_name'] ?? 'N/A'); ?></td>
                <td><span class="badge bg-<?php 
                    echo $b['status'] === 'approved' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'danger'); 
                ?>">
                    <?php echo htmlspecialchars(ucfirst($b['status'])); ?>
                </span></td>
                <td><?php echo htmlspecialchars($b['created_at']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php
mysqli_stmt_close($stmt);
require __DIR__ . '/../includes/footer.php';
?>
