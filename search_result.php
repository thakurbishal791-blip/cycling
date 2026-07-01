<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'Search Results';
$activePage = 'search';

$searchType = $_POST['search_type'] ?? '';
$participantQuery = trim($_POST['participant_query'] ?? '');
$clubQuery = trim($_POST['club_query'] ?? '');

$participants = [];
$clubResults = [];
$dbError = null;
$missingQuery = false;

/** Wraps every occurrence of $needle in $haystack with a highlight span (HTML-escaped). */
function highlight_match($haystack, $needle)
{
    $escapedHaystack = h($haystack);
    $needle = trim($needle);
    if ($needle === '') {
        return $escapedHaystack;
    }
    $pattern = '/' . preg_quote(h($needle), '/') . '/i';
    return preg_replace($pattern, '<span class="search-highlight">$0</span>', $escapedHaystack);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || $searchType === '') {
    header('Location: search_form.php');
    exit;
}

try {
    $conn = get_db_connection();

    if ($searchType === 'participant') {

        if ($participantQuery === '') {
            $missingQuery = true;
        } else {
            $stmt = $conn->prepare(
                'SELECT p.id, p.firstname, p.surname, p.email, p.power_output, p.distance, c.name AS club_name
                 FROM participant p LEFT JOIN club c ON c.id = p.club_id
                 WHERE p.firstname LIKE :query OR p.surname LIKE :query
                 ORDER BY p.surname, p.firstname'
            );
            $stmt->execute(['query' => '%' . $participantQuery . '%']);
            $participants = $stmt->fetchAll();
        }

    } else {

        if ($clubQuery === '') {
            $missingQuery = true;
        } else {
            $clubStmt = $conn->prepare('SELECT id, name, location FROM club WHERE name LIKE :query ORDER BY name');
            $clubStmt->execute(['query' => '%' . $clubQuery . '%']);
            $clubs = $clubStmt->fetchAll();

            foreach ($clubs as $club) {
                $memberStmt = $conn->prepare(
                    'SELECT firstname, surname, email, power_output, distance
                     FROM participant WHERE club_id = :club_id ORDER BY surname, firstname'
                );
                $memberStmt->execute(['club_id' => $club['id']]);
                $members = $memberStmt->fetchAll();

                $count = count($members);
                $totalPower = 0;
                $totalDistance = 0;
                foreach ($members as $m) {
                    $totalPower += (float) ($m['power_output'] ?? 0);
                    $totalDistance += (float) ($m['distance'] ?? 0);
                }

                $clubResults[] = [
                    'club' => $club,
                    'members' => $members,
                    'count' => $count,
                    'total_power' => $totalPower,
                    'total_distance' => $totalDistance,
                    'avg_power' => $count ? round($totalPower / $count, 1) : 0,
                    'avg_distance' => $count ? round($totalDistance / $count, 1) : 0,
                ];
            }
        }
    }

} catch (PDOException $e) {
    $dbError = 'A database error occurred: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container">
        <p class="section-kicker mb-2">Restricted area</p>
        <h1 class="mb-1"><?php echo $searchType === 'club' ? 'Club search results' : 'Participant search results'; ?></h1>
        <p class="text-muted mb-4">
            <?php if ($searchType === 'club'): ?>
                Showing clubs matching "<strong><?php echo h($clubQuery); ?></strong>"
            <?php else: ?>
                Showing participants matching "<strong><?php echo h($participantQuery); ?></strong>"
            <?php endif; ?>
        </p>

        <p class="mb-4"><a href="search_form.php" class="btn-cte-ghost">&larr; New search</a></p>

        <?php if ($dbError): ?>
            <div class="cte-alert cte-alert-danger"><?php echo h($dbError); ?></div>

        <?php elseif ($missingQuery): ?>
            <div class="cte-alert cte-alert-danger">Please enter a search term.</div>

        <?php elseif ($searchType === 'participant'): ?>

            <?php if (empty($participants)): ?>
                <div class="cte-table-wrap">
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p class="mb-0">No participants matched "<?php echo h($participantQuery); ?>".</p>
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
                                    <td><strong><?php echo highlight_match($p['firstname'], $participantQuery) . ' ' . highlight_match($p['surname'], $participantQuery); ?></strong></td>
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
                <p class="text-muted small mt-2"><?php echo count($participants); ?> result(s) found.</p>
            <?php endif; ?>

        <?php else: ?>

            <?php if (empty($clubResults)): ?>
                <div class="cte-table-wrap">
                    <div class="empty-state">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.35-4.35"/></svg>
                        <p class="mb-0">No clubs matched "<?php echo h($clubQuery); ?>".</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($clubResults as $result): ?>
                    <div class="cte-card cte-card-pad mb-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div>
                                <h2 class="h4 mb-1"><?php echo highlight_match($result['club']['name'], $clubQuery); ?></h2>
                                <p class="text-muted mb-0"><?php echo h($result['club']['location']); ?> &middot; <?php echo $result['count']; ?> rider(s)</p>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="stat-value"><?php echo $result['total_power']; ?>W</div>
                                    <div class="stat-label">Total power</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="stat-value"><?php echo $result['avg_power']; ?>W</div>
                                    <div class="stat-label">Average power</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="stat-value"><?php echo $result['total_distance']; ?>km</div>
                                    <div class="stat-label">Total distance</div>
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="stat-card">
                                    <div class="stat-value"><?php echo $result['avg_distance']; ?>km</div>
                                    <div class="stat-label">Average distance</div>
                                </div>
                            </div>
                        </div>

                        <?php if (empty($result['members'])): ?>
                            <p class="text-muted mb-0">This club has no participants registered yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-cte mb-0 align-middle">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Power (W)</th>
                                            <th>Distance (km)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($result['members'] as $m): ?>
                                        <tr>
                                            <td><?php echo h($m['firstname'] . ' ' . $m['surname']); ?></td>
                                            <td><?php echo h($m['email']); ?></td>
                                            <td><?php echo h($m['power_output'] ?? '0'); ?></td>
                                            <td><?php echo h($m['distance'] ?? '0'); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
