<?php
/**
 * Cit-E Cycling — authentication helper
 * Starts (or resumes) the session and provides helpers used by
 * every page that lives inside the restricted admin area:
 *   - search_form.php / search_result.php
 *   - view_participants_edit_delete.php
 *   - edit_participant_form.php / edit_participant.php
 *   - delete.php
 *   - admin_menu.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirects back to the login page if there is no active admin
 * session. Call this at the very top of every restricted page,
 * before any HTML has been echoed.
 */
function require_admin_login()
{
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: admin_login.html?session=1');
        exit;
    }
}

function is_admin_logged_in()
{
    return !empty($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Stores a one-time flash message in the session so it can be
 * displayed on the next page load (e.g. after a redirect).
 */
function set_flash($type, $message)
{
    $_SESSION['flash_' . $type] = $message;
}

/**
 * Pulls a flash message out of the session (and removes it so it
 * is only ever shown once).
 */
function get_flash($type)
{
    if (!empty($_SESSION['flash_' . $type])) {
        $message = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $message;
    }
    return null;
}

/** Small helper to safely echo user-supplied data into HTML. */
function h($value)
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
