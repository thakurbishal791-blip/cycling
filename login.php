<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

// If someone is already logged in, send them straight to the menu.
if (is_admin_logged_in()) {
    header('Location: admin_menu.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_login.html');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');

if ($username === '' || $password === '') {
    header('Location: admin_login.html?error=1');
    exit;
}

try {
    $conn = get_db_connection();

    $stmt = $conn->prepare('SELECT id, username, password FROM user WHERE username = :username LIMIT 1');
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    // Credentials in this database are stored as plain text (see
    // cycling.sql), so we compare directly. A real production system
    // would store a salted hash and use password_verify() instead.
    if ($user && hash_equals((string) $user['password'], $password)) {
        session_regenerate_id(true);
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_login_time'] = time();
        header('Location: admin_menu.php');
        exit;
    }

    header('Location: admin_login.html?error=1');
    exit;

} catch (PDOException $e) {
    header('Location: admin_login.html?error=1');
    exit;
}
