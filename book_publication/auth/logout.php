<?php
// auth/logout.php
require __DIR__ . '/../config/db.php'; // ensures session_start already called

$_SESSION = [];
session_destroy();

header('Location: /book_publication/index.php');
exit;
