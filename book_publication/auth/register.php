<?php
// auth/register.php
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';

$errors = [];
$name = $email = $role = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm_password'] ?? '';
    $role     = $_POST['role'] ?? 'customer';

    // 1. Basic validation
    if ($name === '') {
        $errors[] = 'Name is required.';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required.';
    }
    if ($password === '' || strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }
    if (!in_array($role, ['customer', 'publisher'], true)) {
        $errors[] = 'Invalid role selected.';
    }

    // 2. Check if email already exists
    if (empty($errors)) {
        $stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = 'Email is already registered.';
        }
        mysqli_stmt_close($stmt);
    }

    // 3. Insert user
    if (empty($errors)) {
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($conn, "INSERT INTO users (name, email, password, role, status) VALUES (?, ?, ?, ?, 'active')");
        mysqli_stmt_bind_param($stmt, 'ssss', $name, $email, $hashed, $role);

        if (mysqli_stmt_execute($stmt)) {
            $success = 'Registration successful. You can now log in.';
            $name = $email = '';
            $role = 'customer';
        } else {
            $errors[] = 'Error creating account. Please try again.';
        }
        mysqli_stmt_close($stmt);
    }
}

$pageTitle = 'Register';
require __DIR__ . '/../includes/header.php';
?>

<h2 class="mb-4">Create an Account</h2>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
                <li><?php echo htmlspecialchars($e); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<form method="post" class="card p-4 shadow-sm" style="max-width: 500px;">
    <div class="mb-3">
        <label class="form-label">Full Name</label>
        <input type="text" name="name" class="form-control"
               value="<?php echo htmlspecialchars($name); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Email address</label>
        <input type="email" name="email" class="form-control"
               value="<?php echo htmlspecialchars($email); ?>" required>
    </div>

    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required minlength="6">
    </div>

    <div class="mb-3">
        <label class="form-label">Confirm Password</label>
        <input type="password" name="confirm_password" class="form-control" required minlength="6">
    </div>

    <div class="mb-3">
        <label class="form-label">Register as</label>
        <select name="role" class="form-select">
            <option value="customer" <?php echo $role === 'customer' ? 'selected' : ''; ?>>Customer</option>
            <option value="publisher" <?php echo $role === 'publisher' ? 'selected' : ''; ?>>Publisher</option>
        </select>
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
    <a href="login.php" class="btn btn-link">Already have an account? Login</a>
</form>

<?php require __DIR__ . '/../includes/footer.php'; ?>
