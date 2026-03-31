<?php
$pageTitle = 'Subjects';
require __DIR__ . '/config/db.php';
require __DIR__ . '/includes/functions.php';
require __DIR__ . '/includes/header.php';

$subjects = mysqli_query($conn, "SELECT id, name, description FROM subjects ORDER BY name ASC");
?>

<h1 class="mb-4">Subjects</h1>

<div class="row g-3">
<?php while ($s = mysqli_fetch_assoc($subjects)): ?>
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0">
            <div class="card-body">
                <h5 class="card-title"><?php echo htmlspecialchars($s['name']); ?></h5>
                <p class="card-text">
                    <?php echo nl2br(htmlspecialchars($s['description'])); ?>
                </p>
                <a href="subject_books.php?id=<?php echo $s['id']; ?>" class="btn btn-sm btn-primary">
                    View books
                </a>
            </div>
        </div>
    </div>
<?php endwhile; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
