<?php
$pageTitle = 'My Books';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requirePublisher();
require __DIR__ . '/../includes/header.php';

$publisherId = $_SESSION['user_id'];

// handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    // ensure book belongs to this publisher
    $stmt = mysqli_prepare($conn, "SELECT cover_image, pdf_file FROM books WHERE id=? AND publisher_id=?");
    mysqli_stmt_bind_param($stmt, 'ii', $id, $publisherId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($book = mysqli_fetch_assoc($res)) {
        // delete files
        if ($book['cover_image'] && file_exists(__DIR__ . '/../uploads/covers/' . $book['cover_image'])) {
            unlink(__DIR__ . '/../uploads/covers/' . $book['cover_image']);
        }
        if ($book['pdf_file'] && file_exists(__DIR__ . '/../uploads/pdfs/' . $book['pdf_file'])) {
            unlink(__DIR__ . '/../uploads/pdfs/' . $book['pdf_file']);
        }
        // delete DB row
        $stmtDel = mysqli_prepare($conn, "DELETE FROM books WHERE id=? AND publisher_id=?");
        mysqli_stmt_bind_param($stmtDel, 'ii', $id, $publisherId);
        mysqli_stmt_execute($stmtDel);
        mysqli_stmt_close($stmtDel);
    }
    mysqli_stmt_close($stmt);

    header('Location: books.php');
    exit;
}

// fetch books for this publisher
$sql = "SELECT b.id, b.title, b.author, b.status, b.created_at, s.name AS subject_name
        FROM books b
        LEFT JOIN subjects s ON b.subject_id = s.id
        WHERE b.publisher_id = ?
        ORDER BY b.created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'i', $publisherId);
mysqli_stmt_execute($stmt);
$books = mysqli_stmt_get_result($stmt);
?>

<h1 class="mb-4">My Books</h1>

<a href="book_add.php" class="btn btn-primary mb-3">Add New Book</a>

<table class="table table-striped table-hover align-middle">
    <thead>
        <tr>
            <th>#</th>
            <th>Title</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Created</th>
            <th style="width: 180px;">Actions</th>
        </tr>
    </thead>
    <tbody>
    <?php if (mysqli_num_rows($books) === 0): ?>
        <tr><td colspan="6">No books yet. Click "Add New Book" to start.</td></tr>
    <?php else: ?>
        <?php while ($b = mysqli_fetch_assoc($books)): ?>
            <tr>
                <td><?php echo $b['id']; ?></td>
                <td><?php echo htmlspecialchars($b['title']); ?></td>
                <td><?php echo htmlspecialchars($b['subject_name'] ?? 'N/A'); ?></td>
                <td>
                    <span class="badge bg-<?php 
                        echo $b['status'] === 'approved' ? 'success' : ($b['status'] === 'pending' ? 'warning' : 'danger');
                    ?>">
                        <?php echo htmlspecialchars(ucfirst($b['status'])); ?>
                    </span>
                </td>
                <td><?php echo htmlspecialchars($b['created_at']); ?></td>
                <td>
                    <a href="book_edit.php?id=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                    <a href="books.php?delete=<?php echo $b['id']; ?>" class="btn btn-sm btn-outline-danger"
                       onclick="return confirm('Delete this book?');">Delete</a>
                </td>
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
