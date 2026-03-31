<?php
$pageTitle = 'Admin Dashboard';
require __DIR__ . '/../config/db.php';
require __DIR__ . '/../includes/functions.php';
requireAdmin();
require __DIR__ . '/../includes/header.php';

// total users by role
$stats = [
    'total_users'      => 0,
    'total_publishers' => 0,
    'total_customers'  => 0,
    'total_books'      => 0,
    'pending_books'    => 0,
    'approved_books'   => 0,
    'rejected_books'   => 0,
];

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users");
$stats['total_users'] = mysqli_fetch_assoc($res)['c'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='publisher'");
$stats['total_publishers'] = mysqli_fetch_assoc($res)['c'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM users WHERE role='customer'");
$stats['total_customers'] = mysqli_fetch_assoc($res)['c'];

$res = mysqli_query($conn, "SELECT COUNT(*) AS c FROM books");
$stats['total_books'] = mysqli_fetch_assoc($res)['c'];

$res = mysqli_query($conn, "SELECT 
    SUM(status='pending')  AS pending_cnt,
    SUM(status='approved') AS approved_cnt,
    SUM(status='rejected') AS rejected_cnt
  FROM books");
$row = mysqli_fetch_assoc($res);
$stats['pending_books']  = (int)$row['pending_cnt'];
$stats['approved_books'] = (int)$row['approved_cnt'];
$stats['rejected_books'] = (int)$row['rejected_cnt'];
?>

<h1 class="mb-4">Admin Dashboard</h1>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Total Users</h6>
                <h3><?php echo $stats['total_users']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Publishers</h6>
                <h3><?php echo $stats['total_publishers']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Customers</h6>
                <h3><?php echo $stats['total_customers']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Total Books</h6>
                <h3><?php echo $stats['total_books']; ?></h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-warning border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Pending Books</h6>
                <h3><?php echo $stats['pending_books']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-success border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Approved Books</h6>
                <h3><?php echo $stats['approved_books']; ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 border-start border-danger border-4">
            <div class="card-body">
                <h6 class="text-muted text-uppercase">Rejected Books</h6>
                <h3><?php echo $stats['rejected_books']; ?></h3>
            </div>
        </div>
    </div>
</div>

<a href="users.php" class="btn btn-outline-primary me-2">Manage Users</a>
<a href="subjects.php" class="btn btn-outline-secondary me-2">Manage Subjects</a>
<a href="books_pending.php" class="btn btn-outline-warning">Pending Books</a>
<a href="reviews.php" class="btn btn-outline-danger ms-2">Manage Reviews</a>


<?php require __DIR__ . '/../includes/footer.php'; ?>
