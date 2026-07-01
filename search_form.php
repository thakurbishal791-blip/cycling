<?php
require_once 'includes/auth.php';
require_admin_login();

$pageTitle = 'Search Participants & Clubs';
$activePage = 'search';

include 'includes/header.php';
?>

<section class="page-section">
    <div class="container">
        <p class="section-kicker mb-2">Restricted area</p>
        <h1 class="mb-1">Search participants or clubs</h1>
        <p class="text-muted mb-4">Find a rider by first name or surname, or look up a club to see its full roster and combined stats.</p>

        <div class="row g-4">
            <div class="col-md-6">
                <div class="cte-card cte-card-pad h-100">
                    <h2 class="h5 mb-3">Search for a participant</h2>
                    <form action="search_result.php" method="POST" class="cte-validate" novalidate>
                        <div class="mb-3">
                            <label for="participant_query">First name or surname <span class="required-mark">*</span></label>
                            <input type="text" class="form-control" id="participant_query" name="participant_query" required maxlength="50" placeholder="e.g. Lorette or Lamacraft" data-error-message="Please enter a name to search for.">
                            <div class="invalid-feedback">Please enter a name to search for.</div>
                        </div>
                        <input type="hidden" name="search_type" value="participant">
                        <button type="submit" class="btn-cte-primary w-100">Search Participants</button>
                    </form>
                </div>
            </div>

            <div class="col-md-6">
                <div class="cte-card cte-card-pad h-100">
                    <h2 class="h5 mb-3">Search for a club / team</h2>
                    <form action="search_result.php" method="POST" class="cte-validate" novalidate>
                        <div class="mb-3">
                            <label for="club_query">Club name <span class="required-mark">*</span></label>
                            <input type="text" class="form-control" id="club_query" name="club_query" required maxlength="100" placeholder="e.g. Byker Bikers" data-error-message="Please enter a club name to search for.">
                            <div class="invalid-feedback">Please enter a club name to search for.</div>
                        </div>
                        <input type="hidden" name="search_type" value="club">
                        <button type="submit" class="btn-cte-orange w-100">Search Clubs</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
