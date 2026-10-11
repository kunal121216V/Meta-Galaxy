<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';

$pageTitle = 'Login | Meta Galaxy';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($email === '' || $password === '') {
            setFlash('danger', 'Email and password are required.');
        } else {
            $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && verifyPassword($password, $user['password'])) {
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_name'] = $user['name'];
                if ((int) $user['is_admin'] === 1) {
                    header('Location: ' . asset('admin/index.php'));
                    exit;
                }
                header('Location: ' . asset('account.php'));
                exit;
            }

            setFlash('danger', 'Invalid login credentials.');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <h2 class="text-center mb-4">Welcome back</h2>
            <form method="POST" action="<?= asset('login.php') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input class="form-control" type="email" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input class="form-control" type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
            <p class="text-center mt-3 mb-0">Need an account? <a href="<?= asset('register.php') ?>">Register</a></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
