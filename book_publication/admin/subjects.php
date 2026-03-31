<?php
$pageTitle = 'Manage Subjects';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireAdmin();

$errors = [];
$name = $description = '';
$editId = null;

// handle add / update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $editId = isset($_POST['id']) && $_POST['id'] !== '' ? (int)$_POST['id'] : null;

    if ($name === '') {
        $errors[] = 'Subject name is required.';
    }

    if (empty($errors)) {
        if ($editId) {
            $stmt = mysqli_prepare($conn, "UPDATE subjects SET name=?, description=? WHERE id=?");
            mysqli_stmt_bind_param($stmt, 'ssi', $name, $description, $editId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        } else {
            $stmt = mysqli_prepare($conn, "INSERT INTO subjects (name, description) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, 'ss', $name, $description);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
        header('Location: subjects.php');
        exit;
    }
}

// handle delete
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $stmt = mysqli_prepare($conn, "DELETE FROM subjects WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $delId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header('Location: subjects.php');
    exit;
}

// handle edit load
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT id, name, description FROM subjects WHERE id=?");
    mysqli_stmt_bind_param($stmt, 'i', $editId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($res)) {
        $name = $row['name'];
        $description = $row['description'];
    }
    mysqli_stmt_close($stmt);
}

// list all subjects
$subjects = mysqli_query($conn, "SELECT * FROM subjects ORDER BY name ASC");

require __DIR__ . '/../includes/header.php';
?>

<h1 class="mb-4">Manage Subjects</h1>

<div class="row">
    <div class="col-md-5">
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <h5 class="card-title">
                    <?php echo $editId ? 'Edit Subject' : 'Add Subject'; ?>
                </h5>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach ($errors as $e): ?>
                                <li><?php echo htmlspecialchars($e); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form method="post">
                    <input type="hidden" name="id" value="<?php echo $editId ?: ''; ?>">
                    <div class="mb-3">
                        <label class="form-label">Subject Name</label>
                        <input type="text" name="name" class="form-control"
                               value="<?php echo htmlspecialchars($name); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?php
                            echo htmlspecialchars($description);
                        ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $editId ? 'Update' : 'Add'; ?>
                    </button>
                    <?php if ($editId): ?>
                        <a href="subjects.php" class="btn btn-secondary">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <h5>All Subjects</h5>
        <table class="table table-striped table-hover align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created</th>
                    <th style="width: 130px;">Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($s = mysqli_fetch_assoc($subjects)): ?>
                <tr>
                    <td><?php echo $s['id']; ?></td>
                    <td><?php echo htmlspecialchars($s['name']); ?></td>
                    <td><?php echo nl2br(htmlspecialchars($s['description'])); ?></td>
                    <td><?php echo htmlspecialchars($s['created_at']); ?></td>
                    <td>
                        <a href="subjects.php?edit=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                        <a href="subjects.php?delete=<?php echo $s['id']; ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Delete this subject? Books using it will show subject as NULL.');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="index.php" class="btn btn-link mt-3">← Back to dashboard</a>

<?php require __DIR__ . '/../includes/footer.php'; ?>
