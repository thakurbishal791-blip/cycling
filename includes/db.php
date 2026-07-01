<?php
/**
 * Returns a ready-to-use PDO connection using the credentials in
 * dbconnect.php. Throws PDOException on failure so callers can
 * catch it and show a friendly error message.
 */
function get_db_connection()
{
    require __DIR__ . '/../dbconnect.php';

    $dsn = "mysql:host={$servername};port={$port};dbname={$database};charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    return $conn;
}
