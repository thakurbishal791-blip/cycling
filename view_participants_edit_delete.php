<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'View Participants';
$activePage = 'participants';

$participants = [];
$dbError = null;
$filter = trim($_GET['filter'] ?? '');

try {
    $conn = get_db_connection();

    $sql = 'SELECT p.id, p.firstname, p.surname, p.email, p.power_output, p.distance, c.name AS club_name
            FROM participant p
            LEFT JOIN club c ON c.id = p.club_id';
    $params = [];

    if ($filter !== '') {
        $sql .= ' WHERE p.firstname LIKE :filter OR p.surname LIKE :filter OR c.name LIKE :filter';
        $params['filter'] = '%' . $filter . '%';
    }

    $sql .= ' ORDER BY p.surname ASC, p.firstname ASC';

    $stmt = $conn->prepare($sql);
    $stmt->execute($params);
    $participants = $stmt->fetchAll();

} catch (PDOException $e) {
    $dbError = 'Could not load participants: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container">
        <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
            <div>
                <p class="section-kicker mb-2">Restricted area</p>
                <h1 class="mb-1">All participants</h1>
                <p class="text-muted mb-0">Edit a rider's power output and distance, or delete a participant from the system.</p>
            </div>
            <form action="view_participants_edit_delete.php" method="GET" class="d-flex gap-2">
                <input type="text" name="filter" class="form-control" placeholder="Filter by name or club" value="<?php echo h($filter); ?>" style="min-width: 220px;">
                <button type="submit" class="btn-cte-ghost">Filter</button>
                <?php if ($filter !== ''): ?>
                    <a href="view_participants_edit_delete.php" class="btn-cte-ghost">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <?php if ($dbError): ?>
            <div class="cte-alert cte-alert-danger mb-4"><?php echo h($dbError); ?></div>
        <?php elseif (empty($participants)): ?>
            <div class="cte-table-wrap">
                <div class="empty-state">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                    <p class="mb-0">No participants found<?php echo $filter !== '' ? ' for "' . h($filter) . '"' : ''; ?>.</p>
                </div>
            </div>
        <?php else: ?>
            <div class="cte-table-wrap">
                <div class="table-responsive">
                    <table class="table table-cte mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Club</th>
                                <th>Power (W)</th>
                                <th>Distance (km)</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($participants as $p): ?>
                            <tr>
                                <td><strong><?php echo h($p['firstname'] . ' ' . $p['surname']); ?></strong></td>
                                <td><?php echo h($p['email']); ?></td>
                                <td><?php echo $p['club_name'] ? '<span class="badge-club">' . h($p['club_name']) . '</span>' : '<span class="text-muted">&mdash;</span>'; ?></td>
                                <td><?php echo h($p['power_output'] ?? '0'); ?></td>
                                <td><?php echo h($p['distance'] ?? '0'); ?></td>
                                <td class="text-end">
                                    <a href="edit_participant.php?id=<?php echo (int) $p['id']; ?>" class="btn-cte-ghost btn-sm">Edit</a>
                                    <a href="delete.php?id=<?php echo (int) $p['id']; ?>" class="btn-cte-danger btn-sm">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <p class="text-muted small mt-2"><?php echo count($participants); ?> participant(s) shown.</p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
