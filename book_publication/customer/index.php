<?php
$pageTitle = 'Customer Dashboard';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireCustomer();
require __DIR__ . '/../includes/header.php';

$customerId = $_SESSION['user_id'];

// count reviews by this customer
$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM reviews WHERE customer_id = " . (int)$customerId);
$myReviews = mysqli_fetch_assoc($res)['c'];

// latest approved books
$sql = "SELECT b.id, b.title, b.author, s.name AS subject_name, b.created_at
        FROM books b
        LEFT JOIN subjects s ON b.subject_id = s.id
        WHERE b.status = 'approved'
        ORDER BY b.created_at DESC
        LIMIT 6";
$books = mysqli_query($conn, $sql);

// latest reviews by this customer
$sql = "SELECT r.id, r.rating, r.comment, r.created_at, b.title 
        FROM reviews r
        JOIN books b ON r.book_id = b.id
        WHERE r.customer_id = " . (int)$customerId . "
        ORDER BY r.created_at DESC
        LIMIT 5";
$reviews = mysqli_query($conn, $sql);
?>

<h1 class="mb-4">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></h1>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Your Reviews</h6>
                <h3><?php echo $myReviews; ?></h3>
            </div>
        </div>
    </div>
</div>
<a href="my_reviews.php" class="btn btn-outline-secondary btn-sm mb-3">View All My Reviews</a>

<h4 class="mb-3">Latest Approved Books</h4>
<div class="row g-3 mb-4">
<?php if (mysqli_num_rows($books) === 0): ?>
    <p>No books approved yet.</p>
<?php else: ?>
    <?php while ($b = mysqli_fetch_assoc($books)): ?>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($b['title']); ?></h5>
                    <h6 class="card-subtitle mb-2 text-muted">
                        <?php echo htmlspecialchars($b['author']); ?>
                    </h6>
                    <p class="card-text">
                        <small><?php echo htmlspecialchars($b['subject_name'] ?? 'General'); ?></small>
                    </p>
                    <a href="/book_publication/book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary">
                        View details
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<h4 class="mb-3">Your Recent Reviews</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Book</th>
            <th>Rating</th>
            <th>Comment</th>
            <th>Date</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($reviews) === 0): ?>
        <tr><td colspan="4">You have not written any reviews yet.</td></tr>
    <?php else: ?>
        <?php while ($r = mysqli_fetch_assoc($reviews)): ?>
            <tr>
                <td><?php echo htmlspecialchars($r['title']); ?></td>
                <td><?php echo (int)$r['rating']; ?>/5</td>
                <td><?php echo nl2br(htmlspecialchars($r['comment'])); ?></td>
                <td><?php echo htmlspecialchars($r['created_at']); ?></td>
            </tr>
        <?php endwhile; ?>
    <?php endif; ?>
    </tbody>
</table>

<?php require __DIR__ . '/../includes/footer.php'; ?>
