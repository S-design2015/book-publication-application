<?php
$pageTitle = 'Add Book';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requirePublisher();
require __DIR__ . '/../includes/header.php';

$publisherId = $_SESSION['user_id'];
$errors = [];
$title = $isbn = $author = $description = '';
$subjectId = '';

$subjectsRes = mysqli_query($conn, "SELECT id, name FROM subjects ORDER BY name ASC");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title       = trim($_POST['title'] ?? '');
    $isbn        = trim($_POST['isbn'] ?? '');
    $author      = trim($_POST['author'] ?? '');
    $subjectId   = $_POST['subject_id'] !== '' ? (int)$_POST['subject_id'] : null;
    $description = trim($_POST['description'] ?? '');

    if ($title === '')  $errors[] = 'Title is required.';
    if ($author === '') $errors[] = 'Author is required.';

    // handle files
    $coverName = null;
    $pdfName   = null;

    if (!empty($_FILES['cover_image']['name'])) {
        $ext = strtolower(pathinfo($_FILES['cover_image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg','jpeg','png','webp'], true)) {
            $errors[] = 'Cover image must be JPG, PNG, or WEBP.';
        } else {
            $coverName = uniqid('cover_', true) . '.' . $ext;
        }
    }

    if (!empty($_FILES['pdf_file']['name'])) {
        $ext = strtolower(pathinfo($_FILES['pdf_file']['name'], PATHINFO_EXTENSION));
        if ($ext !== 'pdf') {
            $errors[] = 'Book file must be a PDF.';
        } else {
            $pdfName = uniqid('book_', true) . '.pdf';
        }
    } else {
        $errors[] = 'PDF file is required.';
    }

    if (empty($errors)) {
        // move files
        if ($coverName) {
            move_uploaded_file(
                $_FILES['cover_image']['tmp_name'],
                __DIR__ . '/../uploads/covers/' . $coverName
            );
        }
        if ($pdfName) {
            move_uploaded_file(
                $_FILES['pdf_file']['tmp_name'],
                __DIR__ . '/../uploads/pdfs/' . $pdfName
            );
        }

        $sql = "INSERT INTO books (title, isbn, author, subject_id, description, cover_image, pdf_file, publisher_id, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param(
            $stmt,
            'sssisssi',
            $title,
            $isbn,
            $author,
            $subjectId,
            $description,
            $coverName,
            $pdfName,
            $publisherId
        );
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header('Location: books.php');
        exit;
    }
}
?>

<h1 class="mb-4">Add New Book</h1>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm">
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Title</label>
            <input type="text" name="title" class="form-control"
                   value="<?php echo htmlspecialchars($title); ?>" required>
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">ISBN</label>
            <input type="text" name="isbn" class="form-control"
                   value="<?php echo htmlspecialchars($isbn); ?>">
        </div>
        <div class="col-md-3 mb-3">
            <label class="form-label">Author</label>
            <input type="text" name="author" class="form-control"
                   value="<?php echo htmlspecialchars($author); ?>" required>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Subject</label>
        <select name="subject_id" class="form-select">
            <option value="">Select subject</option>
            <?php while ($s = mysqli_fetch_assoc($subjectsRes)): ?>
                <option value="<?php echo $s['id']; ?>"
                    <?php echo $subjectId == $s['id'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($s['name']); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" rows="4" class="form-control"><?php
            echo htmlspecialchars($description);
        ?></textarea>
    </div>

    <div class="row mb-3">
        <div class="col-md-6">
            <label class="form-label">Cover Image (JPG/PNG/WEBP)</label>
            <input type="file" name="cover_image" class="form-control">
        </div>
        <div class="col-md-6">
            <label class="form-label">PDF File</label>
            <input type="file" name="pdf_file" class="form-control" required>
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Submit for Approval</button>
    <a href="books.php" class="btn btn-secondary">Cancel</a>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
