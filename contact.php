<?php
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/auth.php';
require __DIR__ . '/includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('danger', 'Security validation failed.');
    } else {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($name !== '' && $email !== '' && $subject !== '' && $message !== '') {
            $stmt = db()->prepare('INSERT INTO contacts (name, email, phone, subject, message, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())');
            $stmt->execute([$name, $email, $phone, $subject, $message]);
            setFlash('success', 'Thanks for contacting us. Our support team will get back shortly.');
            header('Location: ' . asset('contact.php'));
            exit;
        }
        setFlash('danger', 'Please complete all required fields.');
    }
}

$pageTitle = 'Contact | Meta Galaxy';
include __DIR__ . '/includes/header.php';
?>
<section class="shop-shell">
    <div class="container">
        <div class="row g-4 align-items-start">
            <div class="col-lg-6">
                <span class="kicker">Contact</span>
                <h2>Let’s talk style.</h2>
                <p class="text-muted">We respond within 24 hours to order help, styling questions, and support requests.</p>
                <div class="summary-box mt-4">
                    <p><strong>Email:</strong> support@metagalaxy.com</p>
                    <p><strong>Phone:</strong> +91 90000 00000</p>
                    <p><strong>Location:</strong> Orbital Avenue, Mumbai, India</p>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="summary-box">
                    <form method="POST" action="<?= asset('contact.php') ?>">
                        <?= csrf_field() ?>
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Name</label><input class="form-control" type="text" name="name" required></div>
                            <div class="col-md-6"><label class="form-label">Email</label><input class="form-control" type="email" name="email" required></div>
                            <div class="col-12"><label class="form-label">Phone</label><input class="form-control" type="text" name="phone"></div>
                            <div class="col-12"><label class="form-label">Subject</label><input class="form-control" type="text" name="subject" required></div>
                            <div class="col-12"><label class="form-label">Message</label><textarea class="form-control" name="message" rows="5" required></textarea></div>
                            <div class="col-12"><button type="submit" class="btn btn-primary">Send message</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
