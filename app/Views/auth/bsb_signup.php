<?php $validation = session('validation'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Sign Up') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style><?= file_get_contents(APPPATH . 'Views/css/main.css'); ?></style>
</head>
<body class="auth-page">
<main class="auth-wrap">
    <section class="auth-card auth-card-signup">
        <h1 class="auth-brand">BYTE-SIZED BAKES</h1>
        <p class="auth-subtitle">JOIN US!</p>

        <form class="auth-grid-2" method="post" action="<?= site_url('/signup') ?>" novalidate>
            <?= csrf_field() ?>

            <div>
                <label>FULL NAME</label>
                <div class="auth-inline">
                    <input type="text" name="fname" placeholder="First name" required maxlength="50" value="<?= old('fname') ?>">
                    <input type="text" name="lname" placeholder="Last name" required maxlength="50" value="<?= old('lname') ?>">
                </div>

                <label>USERNAME</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-person-fill"></i>
                    <input
                        type="text"
                        name="uname"
                        placeholder="Create username"
                        required
                        minlength="3"
                        maxlength="50"
                        value="<?= old('uname') ?>"
                    >
                </div>
                <?php if ($validation && $validation->hasError('uname')): ?>
                    <div class="text-danger small mt-1 mb-2"><?= esc($validation->getError('uname')) ?></div>
                <?php endif; ?>

                <label>EMAIL</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-envelope"></i>
                    <input type="email" name="email" placeholder="Enter email address" required maxlength="255" value="<?= old('email') ?>">
                </div>
                <?php if ($validation && $validation->hasError('email')): ?>
                    <div class="text-danger small mt-1 mb-2"><?= esc($validation->getError('email')) ?></div>
                <?php endif; ?>
            </div>

            <div>
                <label>CREATE PASSWORD</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="pword" placeholder="Create a password" required minlength="8" maxlength="255">
                    <button type="button" class="auth-eye-btn" aria-label="Toggle password"><i class="bi bi-eye-slash"></i></button>
                </div>
                <small class="auth-help">Must be at least 8 characters</small>

                <label>RE-ENTER PASSWORD</label>
                <div class="auth-input-wrap">
                    <i class="bi bi-lock-fill"></i>
                    <input type="password" name="pword_confirm" placeholder="Re-enter password" required minlength="8" maxlength="255">
                    <button type="button" class="auth-eye-btn" aria-label="Toggle password"><i class="bi bi-eye-slash"></i></button>
                </div>
            </div>

            <div class="auth-grid-full">
                <button class="btn auth-main-btn" type="submit">CREATE ACCOUNT</button>
                <p class="auth-foot-note">Already have an account? <a href="<?= site_url('/signin') ?>">Login</a></p>
            </div>

            <div class="auth-alt-login">
                <div class="auth-divider"><span>or</span></div>
                <div class="auth-socials">
                    <a href="#" class="auth-social-btn auth-social-google" aria-label="Continue with Google">
                        <i class="bi bi-google"></i>
                    </a>
                    <a href="#" class="auth-social-btn auth-social-facebook" aria-label="Continue with Facebook">
                        <i class="bi bi-facebook"></i>
                    </a>
                </div>
            </div>
        </form>
    </section>
    <div class="auth-bottom-actions">
        <a href="<?= site_url('/') ?>" class="btn btn-ghost auth-back-btn">Back to Landing Page</a>
    </div>
</main>
</body>
</html>

<?php $validation = session('validation'); ?>

<?php if (session('signup_error')): ?>
    <div class="alert alert-danger py-2 mb-3" role="alert">
        <?= esc(session('signup_error')) ?>
    </div>
<?php endif; ?>