<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'Admin Menu';
$activePage = 'admin';

$stats = [
    'participants' => 0,
    'clubs' => 0,
    'interest' => 0,
    'avg_power' => 0,
];

try {
    $conn = get_db_connection();

    $stats['participants'] = (int) $conn->query('SELECT COUNT(*) FROM participant')->fetchColumn();
    $stats['clubs']        = (int) $conn->query('SELECT COUNT(*) FROM club')->fetchColumn();
    $stats['interest']     = (int) $conn->query('SELECT COUNT(*) FROM interest')->fetchColumn();
    $avg = $conn->query('SELECT AVG(power_output) FROM participant WHERE power_output IS NOT NULL')->fetchColumn();
    $stats['avg_power'] = $avg ? round($avg, 1) : 0;

} catch (PDOException $e) {
    $dbError = 'Could not load dashboard statistics: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container">
        <p class="section-kicker mb-2">Restricted area</p>
        <h1 class="mb-1">Welcome back, <?php echo h($_SESSION['admin_username'] ?? 'admin'); ?></h1>
        <p class="text-muted mb-4">Manage participants, clubs and search the national leaderboard from here.</p>

        <?php if (!empty($dbError)): ?>
            <div class="cte-alert cte-alert-danger mb-4"><?php echo h($dbError); ?></div>
        <?php endif; ?>

        <div class="row g-3 mb-5">
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['participants']; ?></div>
                    <div class="stat-label">Participants</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['clubs']; ?></div>
                    <div class="stat-label">Registered Clubs</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['avg_power']; ?>W</div>
                    <div class="stat-label">Avg. Power Output</div>
                </div>
            </div>
            <div class="col-6 col-lg-3">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $stats['interest']; ?></div>
                    <div class="stat-label">Interest Sign-ups</div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-md-4">
                <a href="search_form.php" class="menu-tile">
                    <div class="tile-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
                    </div>
                    <h3>Search participants or clubs</h3>
                    <p>Look up a rider by name, or a club to see its full roster and stats.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="view_participants_edit_delete.php" class="menu-tile tile-orange">
                    <div class="tile-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h7"/><path d="M16.5 3.5l4 4L8 20l-4 1 1-4L16.5 3.5z"/></svg>
                    </div>
                    <h3>View, edit &amp; delete participants</h3>
                    <p>Update power output and distance, or remove a participant from the system.</p>
                </a>
            </div>
            <div class="col-md-4">
                <a href="interest_list.php" class="menu-tile tile-yellow">
                    <div class="tile-icon">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8"/><path d="M12 8v8M8 12h8"/></svg>
                    </div>
                    <h3>Interest sign-ups</h3>
                    <p><?php echo $stats['interest']; ?> people have registered interest in the event.</p>
                </a>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
