<?php
/**
 * Shared header for all PHP pages.
 * Expects (optionally) the following variables to be set before
 * including this file:
 *   $pageTitle   - string shown in <title> and used in og tags
 *   $activePage  - string key matching one of the nav items below
 */
if (!isset($pageTitle)) {
    $pageTitle = 'Cit-E Cycling';
}
if (!isset($activePage)) {
    $activePage = '';
}
$loggedIn = function_exists('is_admin_logged_in') ? is_admin_logged_in() : false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title><?php echo h($pageTitle); ?> | Cit-E Cycling</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Rajdhani:wght@500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<a class="skip-link" href="#main-content">Skip to main content</a>

<nav class="navbar navbar-expand-lg cte-navbar">
    <div class="container">
        <a class="navbar-brand" href="index.html">
            <svg width="28" height="28" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <circle cx="18" cy="45" r="8" stroke="#0d9b86" stroke-width="3"/>
                <circle cx="46" cy="45" r="8" stroke="#ff7c3c" stroke-width="3"/>
                <path d="M18 45L30 23L38 23L46 45" stroke="#0d9b86" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M28 23L32 14" stroke="#ff7c3c" stroke-width="3" stroke-linecap="round"/>
                <path d="M14 42L23 39" stroke="#0d9b86" stroke-width="2" opacity="0.6"/>
                <path d="M50 42L41 39" stroke="#ff7c3c" stroke-width="2" opacity="0.6"/>
            </svg>
            Cit<span class="brand-accent">-E</span> Cycling
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#cteNav" aria-controls="cteNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="cteNav">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'home' ? 'active-page' : ''; ?>" href="index.html">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo $activePage === 'register' ? 'active-page' : ''; ?>" href="register.php">Register Interest</a>
                </li>
                <?php if ($loggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'admin' ? 'active-page' : ''; ?>" href="admin_menu.php">Admin Menu</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'search' ? 'active-page' : ''; ?>" href="search_form.php">Search</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo $activePage === 'participants' ? 'active-page' : ''; ?>" href="view_participants_edit_delete.php">Participants</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link nav-cta" href="logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item ms-lg-2">
                        <a class="nav-link nav-cta" href="admin_login.html">Admin Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main id="main-content">
<?php
$flashSuccess = function_exists('get_flash') ? get_flash('success') : null;
$flashError = function_exists('get_flash') ? get_flash('error') : null;
if ($flashSuccess || $flashError):
?>
<div class="container pt-4">
    <?php if ($flashSuccess): ?>
        <div class="cte-alert cte-alert-success" data-autodismiss role="status"><?php echo h($flashSuccess); ?></div>
    <?php endif; ?>
    <?php if ($flashError): ?>
        <div class="cte-alert cte-alert-danger" role="alert"><?php echo h($flashError); ?></div>
    <?php endif; ?>
</div>
<?php endif; ?>
