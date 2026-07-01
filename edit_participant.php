<?php
require_once 'includes/auth.php';
require_admin_login();
require_once 'includes/db.php';

$pageTitle = 'Update Participant';
$activePage = 'participants';

$errors = [];
$participant = null;
$updated = false;

try {
    $conn = get_db_connection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = filter_var($_POST['id'] ?? '', FILTER_VALIDATE_INT);
        $powerRaw = trim($_POST['power_output'] ?? '');
        $distanceRaw = trim($_POST['distance_travelled'] ?? '');

        if (!$id) {
            $errors['general'] = 'Invalid participant reference.';
        }

        if ($powerRaw === '' || !is_numeric($powerRaw) || (float) $powerRaw < 0) {
            $errors['power_output'] = 'Please enter a power output of 0 or more.';
        }

        if ($distanceRaw === '' || !is_numeric($distanceRaw) || (float) $distanceRaw < 0) {
            $errors['distance_travelled'] = 'Please enter a distance of 0 or more.';
        }

        // Re-fetch the participant so we can re-show their (read-only)
        // name fields and the club they belong to, whether the update
        // succeeds or we need to show validation errors.
        $stmt = $conn->prepare(
            'SELECT p.id, p.firstname, p.surname, p.email, p.power_output, p.distance, c.name AS club_name
             FROM participant p LEFT JOIN club c ON c.id = p.club_id
             WHERE p.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $participant = $stmt->fetch();

        if (!$participant) {
            $errors['general'] = 'That participant could not be found.';
        }

        if (empty($errors)) {
            $update = $conn->prepare(
                'UPDATE participant SET power_output = :power_output, distance = :distance WHERE id = :id'
            );
            $update->execute([
                'power_output' => (float) $powerRaw,
                'distance'     => (float) $distanceRaw,
                'id'           => $id,
            ]);

            set_flash('success', $participant['firstname'] . ' ' . $participant['surname'] . "'s scores were updated successfully.");
            header('Location: view_participants_edit_delete.php');
            exit;
        } else {
            // keep the values the admin typed so they aren't lost
            $participant['power_output'] = $powerRaw;
            $participant['distance'] = $distanceRaw;
        }

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
include 'edit_participant_form.php';
include 'includes/footer.php';
