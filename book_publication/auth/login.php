<?php
// auth/login.php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        $stmt = mysqli_prepare($conn, "SELECT id, name, password, role, status FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $user = mysqli_fetch_assoc($result);
        mysqli_stmt_close($stmt);

        if (!$user) {
            $errors[] = 'Invalid email or password.';
        } else {
            if ($user['status'] !== 'active') {
                $errors[] = 'Your account is blocked or inactive. Contact admin.';
            } elseif (!password_verify($password, $user['password'])) {
                $errors[] = 'Invalid email or password.';
            } else {
                // success: set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = $user['role'];

                // redirect by role
                if ($user['role'] === 'admin') {
                    header('Location: /book_publication/admin/index.php');
                } elseif ($user['role'] === 'publisher') {
                    header('Location: /book_publication/publisher/index.php');
                } else { // customer
                    header('Location: /book_publication/customer/index.php');
                }
                exit;
            }
        }
    }
}

$pageTitle = 'Login';
require __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Login</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm" style="max-width: 400px;">
    <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control"
               value="<?php echo htmlspecialchars($email); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Login</button>
    <a href="register.php" class="btn btn-link">Create new account</a>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
