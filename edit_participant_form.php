<?php
/**
 * Expects $participant (array) and $errors (array) to be set by
 * edit_participant.php before this file is included.
 */
$participant = $participant ?? [];
$errors = $errors ?? [];
?>
<section class="page-section">
    <div class="container" style="max-width: 640px;">
        <p class="section-kicker mb-2">Restricted area</p>
        <h1 class="mb-1">Update rider scores</h1>
        <p class="text-muted mb-4">Only power output and distance travelled can be changed here. Name and email are fixed once a participant has registered.</p>

        <?php if (!empty($errors['general'])): ?>
            <div class="cte-alert cte-alert-danger mb-3"><?php echo h($errors['general']); ?></div>
        <?php endif; ?>

        <?php if (!empty($participant)): ?>
        <div class="cte-card cte-card-pad">
            <form action="edit_participant.php" method="POST" class="cte-validate" novalidate>
                <div class="mb-3">
                    <label for="firstname">Participant first name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" disabled value="<?php echo h($participant['firstname'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="surname">Participant surname</label>
                    <input type="text" class="form-control" id="surname" name="surname" disabled value="<?php echo h($participant['surname'] ?? ''); ?>">
                </div>
                <div class="mb-3">
                    <label for="club_name">Club</label>
                    <input type="text" class="form-control" id="club_name" disabled value="<?php echo h($participant['club_name'] ?? 'No club'); ?>">
                </div>

                <div class="row">
                    <div class="col-sm-6 mb-3">
                        <label for="power_output">Power output (watts) <span class="required-mark">*</span></label>
                        <input type="number" step="0.1" min="0" class="form-control <?php echo isset($errors['power_output']) ? 'is-invalid' : ''; ?>" id="power_output" name="power_output" required data-validate="number" value="<?php echo h($participant['power_output'] ?? '0'); ?>">
                        <div class="invalid-feedback"><?php echo h($errors['power_output'] ?? 'Please enter a positive number.'); ?></div>
                    </div>
                    <div class="col-sm-6 mb-3">
                        <label for="distance_travelled">Distance travelled (km) <span class="required-mark">*</span></label>
                        <input type="number" step="0.1" min="0" class="form-control <?php echo isset($errors['distance_travelled']) ? 'is-invalid' : ''; ?>" id="distance_travelled" name="distance_travelled" required data-validate="number" value="<?php echo h($participant['distance'] ?? '0'); ?>">
                        <div class="invalid-feedback"><?php echo h($errors['distance_travelled'] ?? 'Please enter a positive number.'); ?></div>
                    </div>
                </div>

                <input type="hidden" name="id" value="<?php echo h($participant['id'] ?? ''); ?>">

                <div class="d-flex gap-2">
                    <button type="submit" class="btn-cte-primary">Update this rider</button>
                    <a href="view_participants_edit_delete.php" class="btn-cte-ghost">Cancel</a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>
</section>
