<?php
// includes/functions.php

function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getUserRole()
{
    return isset($_SESSION['role']) ? $_SESSION['role'] : null;
}

// function requireLogin()
// {
//     if (!isLoggedIn()) {
//         header('Location: /book_publication/auth/login.php');
//         exit;
//     }
// }
// function requireLogin()
// {
//     if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
//         header('Location: /book_publication/auth/login.php');
//         exit;
//     }
// }
function requireLogin()
{
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
        header('Location: /book_publication/auth/login.php');
        exit;
    }
}



function requireAdmin()
{
    requireLogin();
    if (getUserRole() !== 'admin') {
        header('Location: /book_publication/index.php');
        exit;
    }
}

function requirePublisher()
{
    requireLogin();
    if (getUserRole() !== 'publisher') {
        header('Location: /book_publication/index.php');
        exit;
    }
}

function requireCustomer()
{
    requireLogin();
    if (getUserRole() !== 'customer') {
        header('Location: /book_publication/index.php');
        exit;
    }
}
