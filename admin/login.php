<?php
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/functions.php';

$pageTitle = 'Admin Login | Meta Galaxy';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $stmt = db()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && (int) $user['is_admin'] === 1 && verifyPassword($password, $user['password'])) {
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header('Location: ' . asset('admin/index.php'));
            exit;
        }
        setFlash('danger', 'Invalid admin credentials.');
    }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="auth-page">
    <div class="container">
        <div class="auth-card">
            <h2 class="text-center mb-4">Admin login</h2>
            <form method="POST" action="<?= asset('admin/login.php') ?>">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
                <div class="mb-3"><label class="form-label">Password</label><input class="form-control" type="password" name="password" required></div>
                <button type="submit" class="btn btn-primary">Login</button>
            </form>
        </div>
    </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
