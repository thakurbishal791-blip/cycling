<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'Delete Participant';
$activePage = 'participants';

$errors = [];
$participant = null;

try {
    $conn = get_db_connection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $confirmText = trim($_POST['confirm_text'] ?? '');

        if (!$id) {
            set_flash('error', 'No participant was specified.');
            header('Location: view_participants_edit_delete.php');
            exit;
        }

        // Look the participant up first so we know who we are
        // deleting and can show their name in the success message.
        $stmt = $conn->prepare('SELECT firstname, surname FROM participant WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $found = $stmt->fetch();

        if (!$found) {
            set_flash('error', 'That participant could not be found, or has already been deleted.');
            header('Location: view_participants_edit_delete.php');
            exit;
        }

        // Extra protection against accidental deletion: the admin
        // must type DELETE into the confirmation box.
        if (strtoupper($confirmText) !== 'DELETE') {
            set_flash('error', 'Deletion was not confirmed, so ' . $found['firstname'] . ' ' . $found['surname'] . ' was not removed.');
            header('Location: delete.php?id=' . $id);
            exit;
        }

        $delete = $conn->prepare('DELETE FROM participant WHERE id = :id');
        $delete->execute(['id' => $id]);

        set_flash('success', $found['firstname'] . ' ' . $found['surname'] . ' was deleted successfully.');
        header('Location: view_participants_edit_delete.php');
        exit;

    } else {
        $id = filter_var($_GET['id'] ?? '', FILTER_VALIDATE_INT);

        if (!$id) {
            set_flash('error', 'No participant was specified.');
            header('Location: view_participants_edit_delete.php');
            exit;
        }

        $stmt = $conn->prepare(
            'SELECT p.id, p.firstname, p.surname, p.email, p.power_output, p.distance, c.name AS club_name
             FROM participant p LEFT JOIN club c ON c.id = p.club_id
             WHERE p.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $participant = $stmt->fetch();

        if (!$participant) {
            set_flash('error', 'That participant could not be found.');
            header('Location: view_participants_edit_delete.php');
            exit;
        }
    }

} catch (PDOException $e) {
    $errors['general'] = 'A database error occurred: ' . $e->getMessage();
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container" style="max-width: 600px;">
        <p class="section-kicker mb-2">Restricted area</p>
        <h1 class="mb-1">Delete participant</h1>
        <p class="text-muted mb-4">This cannot be undone. Please confirm carefully before removing a participant from the system.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="cte-alert cte-alert-danger mb-3"><?php echo h($errors['general']); ?></div>
        <?php elseif ($participant): ?>
            <div class="cte-card cte-card-pad">
                <div class="cte-alert cte-alert-danger mb-4">
                    You are about to permanently delete this participant and all of their recorded scores.
                </div>

                <dl class="row mb-4">
                    <dt class="col-sm-4 text-muted">Name</dt>
                    <dd class="col-sm-8"><?php echo h($participant['firstname'] . ' ' . $participant['surname']); ?></dd>
                    <dt class="col-sm-4 text-muted">Email</dt>
                    <dd class="col-sm-8"><?php echo h($participant['email']); ?></dd>
                    <dt class="col-sm-4 text-muted">Club</dt>
                    <dd class="col-sm-8"><?php echo h($participant['club_name'] ?? 'No club'); ?></dd>
                    <dt class="col-sm-4 text-muted">Power output</dt>
                    <dd class="col-sm-8"><?php echo h($participant['power_output'] ?? '0'); ?> W</dd>
                    <dt class="col-sm-4 text-muted">Distance</dt>
                    <dd class="col-sm-8"><?php echo h($participant['distance'] ?? '0'); ?> km</dd>
                </dl>

                <form action="delete.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo (int) $participant['id']; ?>">
                    <label for="deleteConfirmInput" class="mb-1">Type <strong>DELETE</strong> to confirm <span class="required-mark">*</span></label>
                    <input type="text" class="form-control mb-3" id="deleteConfirmInput" name="confirm_text" data-expected="DELETE" autocomplete="off" required>
                    <div class="d-flex gap-2">
                        <button type="submit" id="deleteConfirmBtn" class="btn-cte-danger">Yes, delete this participant</button>
                        <a href="view_participants_edit_delete.php" class="btn-cte-ghost">Cancel</a>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
