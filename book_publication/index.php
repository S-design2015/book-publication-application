<?php
$pageTitle = 'BookPub - Home';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/header.php';

$sql = "SELECT b.id, b.title, b.author, b.cover_image, s.name AS subject_name, u.name AS publisher_name
        FROM books b
        LEFT JOIN subjects s ON b.subject_id = s.id
        JOIN users u ON b.publisher_id = u.id
        WHERE b.status = 'approved'
        ORDER BY b.created_at DESC
        LIMIT 8";
$books = mysqli_query($conn, $sql);
?>

<!-- Hero section -->
<section class="p-4 p-md-5 mb-4 bg-light rounded-3 shadow-sm">
    <div class="container-fluid py-3">
        <h1 class="display-5 fw-bold">Book Publication Management System</h1>
        <!-- <p class="col-md-8 fs-5">
            Discover, publish, and read books online with dedicated dashboards for admins, publishers, and customers.
        </p> -->
        <div class="mt-3">
            <a href="auth/register.php" class="btn btn-primary btn-lg me-2">Get Started</a>
            <a href="subjects.php" class="btn btn-outline-secondary btn-lg">Browse Subjects</a>
        </div>
    </div>
</section>

<h2 class="mb-4">Latest Approved Books</h2>

<div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-4">
    <?php if (mysqli_num_rows($books) === 0): ?>
        <p>No books available yet.</p>
    <?php else: ?>
        <?php while ($b = mysqli_fetch_assoc($books)): ?>
            <div class="col">
                <div class="card h-100 shadow-sm border-0">
                    <?php if ($b['cover_image']): ?>
                        <img src="uploads/covers/<?php echo htmlspecialchars($b['cover_image']); ?>"
                             class="card-img-top" alt="Cover">
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?php echo htmlspecialchars($b['title']); ?></h5>
                        <p class="card-text mb-1">
                            <small class="text-muted">
                                <?php echo htmlspecialchars($b['author']); ?>
                            </small>
                        </p>
                        <p class="card-text mb-1">
                            <small><?php echo htmlspecialchars($b['subject_name'] ?? 'General'); ?></small>
                        </p>
                        <p class="card-text mb-3">
                            <small>By <?php echo htmlspecialchars($b['publisher_name']); ?></small>
                        </p>
                        <div class="mt-auto">
                            <a href="book.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-primary w-100">
                                Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>