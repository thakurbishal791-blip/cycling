<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'Interest Sign-ups';
$activePage = 'interest';
$errors = [];
$interests = [];

try {
    $conn = get_db_connection();
    $stmt = $conn->query('SELECT id, firstname, surname, email, terms FROM interest ORDER BY id DESC');
    $interests = $stmt->fetchAll();
} catch (PDOException $e) {
    $errors[] = 'Could not load registered interest accounts: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
            <div>
                <p class="section-kicker mb-2">Admin dashboard</p>
                <h1 class="mb-1">Registered interest accounts</h1>
                <p class="text-muted mb-0">Review all people who have signed up to hear about the next Cit-E Cycling event.</p>
            </div>
            <div class="text-md-end">
                <div class="stat-card" style="padding: 1rem 1.25rem; min-width: 190px;">
                    <div class="stat-value"><?php echo count($interests); ?></div>
                    <div class="stat-label">Total sign-ups</div>
                </div>
            </div>
        </div>

        <?php if (!empty($errors)): ?>
            <?php foreach ($errors as $error): ?>
                <div class="cte-alert cte-alert-danger mb-4"><?php echo h($error); ?></div>
            <?php endforeach; ?>
        <?php endif; ?>

        <?php if (empty($interests) && empty($errors)): ?>
            <div class="cte-card cte-card-pad text-center">
                <h2 class="h4 mb-2">No interest sign-ups yet</h2>
                <p class="text-muted mb-0">Once people register, their accounts will appear here for admin review.</p>
            </div>
        <?php else: ?>
            <div class="cte-table-wrap">
                <table class="table table-borderless table-hover table-cte align-middle mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Terms</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($interests as $interest): ?>
                            <tr>
                                <td><?php echo h($interest['id']); ?></td>
                                <td><?php echo h($interest['firstname'] . ' ' . $interest['surname']); ?></td>
                                <td><a href="mailto:<?php echo h($interest['email']); ?>"><?php echo h($interest['email']); ?></a></td>
                                <td><?php echo $interest['terms'] ? 'Accepted' : 'Not accepted'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
