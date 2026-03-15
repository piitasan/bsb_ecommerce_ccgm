<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'Sign In') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style><?= file_get_contents(APPPATH . 'Views/css/main.css'); ?></style>
</head>
<body class="auth-page">
<main class="auth-wrap">
    <section class="auth-card auth-card-signin">
        <h1 class="auth-brand">BYTE-SIZED BAKES</h1>
        <p class="auth-subtitle">WELCOME BACK!</p>

        <?php if (!empty($login_error)): ?>
          <div class="alert alert-danger py-2 mb-3"><?= esc($login_error) ?></div>
        <?php endif; ?>

        <form class="auth-form-grid" method="post" action="<?= site_url('/signin') ?>" novalidate>
            <?= csrf_field() ?>
            <label>USERNAME</label>
            <div class="auth-input-wrap">
                <i class="bi bi-person-fill"></i>
                <input type="text" name="uname" placeholder="Enter your username" required minlength="3" maxlength="50" value="<?= old('uname') ?>">
            </div>

            <label>PASSWORD</label>
            <div class="auth-input-wrap">
                <i class="bi bi-lock-fill"></i>
                <input type="password" name="pword" placeholder="Enter your password" required minlength="8" maxlength="255">
                <button type="button" class="auth-eye-btn" aria-label="Toggle password"><i class="bi bi-eye-slash"></i></button>
            </div>

            <div class="auth-row">
                <label class="auth-check"><input type="checkbox"> remember me?</label>
                <a href="#">forgot password?</a>
            </div>

            
            <p class="auth-foot-note">Not registered yet? <a href="<?= site_url('/signup') ?>">Create an account</a></p>
            <div class="auth-alt-login">
                <div class="auth-divider"><span>or</span></div>
                <div class="auth-socials">
                    <a href="#" class="auth-social-btn auth-social-google" aria-label="Continue with Google"><i class="bi bi-google"></i></a>
                    <a href="#" class="auth-social-btn auth-social-facebook" aria-label="Continue with Facebook"><i class="bi bi-facebook"></i></a>
                </div>
            </div>
            <button class="btn auth-main-btn" type="submit">LOGIN</button>
        </form>
    </section>
    <div class="auth-bottom-actions">
        <a href="<?= site_url('/') ?>" class="btn btn-ghost auth-back-btn">Back to Landing Page</a>
    </div>

</main>
</body>
</html>