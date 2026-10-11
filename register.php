<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';

$pageTitle = 'Register | Meta Galaxy';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($name === '' || $email === '' || $password === '' || $confirm === '') {
            setFlash('danger', 'Please complete all required fields.');
        } elseif ($password !== $confirm) {
            setFlash('danger', 'Passwords do not match.');
        } else {
            $check = db()->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
            $check->execute([$email]);
            if ($check->fetch()) {
                setFlash('danger', 'An account with this email already exists.');
            } else {
                $stmt = db()->prepare('INSERT INTO users (name, email, phone, password, is_admin, created_at, updated_at) VALUES (?, ?, ?, ?, 0, NOW(), NOW())');
                $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
                $_SESSION['user_id'] = (int) db()->lastInsertId();
                $_SESSION['user_name'] = $name;
                header('Location: ' . asset('account.php'));
                exit;
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>
<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <h2 class="text-center mb-4">Create account</h2>
            <form method="POST" action="<?= asset('register.php') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">Full name</label>
                    <input class="form-control" type="text" name="name" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control" type="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input class="form-control" type="text" name="phone">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input class="form-control" type="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Confirm password</label>
                    <input class="form-control" type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            <p class="text-center mt-3 mb-0">Already have an account? <a href="<?= asset('login.php') ?>">Login</a></p>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
