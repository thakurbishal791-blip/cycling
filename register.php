<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

$pageTitle = 'Register Your Interest';
$activePage = 'register';

$errors = [];
$old = ['firstname' => '', 'surname' => '', 'email' => ''];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstname = trim($_POST['firstname'] ?? '');
    $surname   = trim($_POST['surname'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $terms     = isset($_POST['terms']) && $_POST['terms'] === 'yes';

    $old = ['firstname' => $firstname, 'surname' => $surname, 'email' => $email, 'terms' => $terms];

    // Server-side validation - the form cannot be accepted unless
    // every field is completed, even if JavaScript is disabled or
    // the request is sent directly (e.g. with a bypassed form).
    if ($firstname === '') {
        $errors['firstname'] = 'Please enter your first name.';
    } elseif (strlen($firstname) > 50) {
        $errors['firstname'] = 'First name must be 50 characters or fewer.';
    }

    if ($surname === '') {
        $errors['surname'] = 'Please enter your surname.';
    } elseif (strlen($surname) > 50) {
        $errors['surname'] = 'Surname must be 50 characters or fewer.';
    }

    if ($email === '') {
        $errors['email'] = 'Please enter your email address.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Please enter a valid email address.';
    } elseif (strlen($email) > 100) {
        $errors['email'] = 'Email must be 100 characters or fewer.';
    }

    if (!$terms) {
        $errors['terms'] = 'You must accept the terms and conditions.';
    }

    if (empty($errors)) {
        try {
            $conn = get_db_connection();

            // Prevent the same email registering interest twice.
            $check = $conn->prepare('SELECT id FROM interest WHERE email = :email LIMIT 1');
            $check->execute(['email' => $email]);

            if ($check->fetch()) {
                $errors['email'] = 'This email address has already registered interest.';
            } else {
                $stmt = $conn->prepare(
                    'INSERT INTO interest (firstname, surname, email, terms) VALUES (:firstname, :surname, :email, :terms)'
                );
                $stmt->execute([
                    'firstname' => $firstname,
                    'surname'   => $surname,
                    'email'     => $email,
                    'terms'     => 1,
                ]);
                $success = true;
                $old = ['firstname' => '', 'surname' => '', 'email' => ''];
            }
        } catch (PDOException $e) {
            $errors['general'] = 'Sorry, we could not save your registration. Please try again shortly.';
        }
    }
}

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container" style="max-width: 640px;">

        <?php if ($success): ?>
            <div class="cte-card cte-card-pad text-center">
                <div class="tile-icon mx-auto mb-3" style="background: rgba(0,191,168,0.12); color:#00bfa8;">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                </div>
                <h1 class="h3 mb-2">You're on the list!</h1>
                <p class="text-muted">Thanks for registering your interest in Cit-E Cycling. We'll email you as soon as booking opens for an event near you.</p>
                <div class="d-flex justify-content-center gap-3 mt-3">
                    <a href="index.html" class="btn-cte-primary">Back to home</a>
                    <a href="register.php" class="btn-cte-ghost">Register another person</a>
                </div>
            </div>
        <?php else: ?>

            <p class="section-kicker mb-2">Future events</p>
            <h1 class="mb-2">Register your interest</h1>
            <p class="text-muted mb-4">Fill in every field below and we'll let you know as soon as booking opens for a pop-up event near you. All fields are required.</p>

            <div class="cte-card cte-card-pad">

                <?php if (!empty($errors['general'])): ?>
                    <div class="cte-alert cte-alert-danger mb-3"><?php echo h($errors['general']); ?></div>
                <?php elseif (!empty($errors)): ?>
                    <div class="cte-alert cte-alert-danger mb-3">Please fix the highlighted fields below &mdash; every field is required to register.</div>
                <?php endif; ?>

                <form action="register.php" method="POST" class="cte-validate" novalidate>
                    <div class="mb-3">
                        <label for="firstname">First name <span class="required-mark">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['firstname']) ? 'is-invalid' : ''; ?>" id="firstname" name="firstname" required maxlength="50" value="<?php echo h($old['firstname']); ?>" data-error-message="Please enter your first name.">
                        <div class="invalid-feedback"><?php echo h($errors['firstname'] ?? 'Please enter your first name.'); ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="surname">Surname <span class="required-mark">*</span></label>
                        <input type="text" class="form-control <?php echo isset($errors['surname']) ? 'is-invalid' : ''; ?>" id="surname" name="surname" required maxlength="50" value="<?php echo h($old['surname']); ?>" data-error-message="Please enter your surname.">
                        <div class="invalid-feedback"><?php echo h($errors['surname'] ?? 'Please enter your surname.'); ?></div>
                    </div>
                    <div class="mb-3">
                        <label for="email">Email address <span class="required-mark">*</span></label>
                        <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" required maxlength="100" data-validate="email" value="<?php echo h($old['email']); ?>">
                        <div class="invalid-feedback"><?php echo h($errors['email'] ?? 'Please enter a valid email address.'); ?></div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>" type="checkbox" id="terms" name="terms" value="yes" required>
                        <label class="form-check-label" for="terms">
                            I accept the terms and conditions <span class="required-mark">*</span>
                        </label>
                        <div class="invalid-feedback"><?php echo h($errors['terms'] ?? 'You must accept the terms and conditions.'); ?></div>
                    </div>
                    <button type="submit" class="btn-cte-primary w-100">Register Interest</button>
                </form>
            </div>
            <p class="text-center mt-3"><a href="index.html">&larr; Back to home</a></p>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
