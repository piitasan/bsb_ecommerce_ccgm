<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($pageTitle ?? 'BSB Management System') ?></title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    >
    <style>
        <?= file_get_contents(APPPATH . 'Views/css/main.css'); ?>
    </style>
</head>
<body class="admin-login-page">
<main class="admin-login-wrap">
    <section class="admin-login-card">
        <p class="admin-login-eyebrow">BSB Management System</p>
        <h1>Admin Login</h1>
        <p class="admin-login-subtext">Sign in to manage products, users, and store content.</p>

        <?php if (!empty($login_error)): ?>
            <div class="alert alert-danger py-2 mb-3" role="alert">
                <?= esc($login_error) ?>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('/admin/login') ?>" method="post" novalidate>
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" for="uname">Username</label>
                <input
                    id="uname"
                    name="uname"
                    type="text"
                    class="form-control <?= isset($validation) && $validation->hasError('uname') ? 'is-invalid' : '' ?>"
                    value="<?= old('uname') ?>"
                    required
                    minlength="3"
                    maxlength="50"
                    autocomplete="username"
                >
                <?php if (isset($validation) && $validation->hasError('uname')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('uname')) ?></div>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label class="form-label" for="pword">Password</label>
                <input
                    id="pword"
                    name="pword"
                    type="password"
                    class="form-control <?= isset($validation) && $validation->hasError('pword') ? 'is-invalid' : '' ?>"
                    required
                    minlength="6"
                    maxlength="255"
                    autocomplete="current-password"
                >
                <?php if (isset($validation) && $validation->hasError('pword')): ?>
                    <div class="invalid-feedback"><?= esc($validation->getError('pword')) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn btn-cta w-100 mt-2">Sign In</button>
        </form>
    </section>
</main>

<footer class="admin-footer-min">
    <p>Copyright &copy; <?= date('Y') ?> BSB Management System | Developed by Piitasan Developments | Version V2.0.0</p>
</footer>
</body>
</html>