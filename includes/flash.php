<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function render_flash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return '<div class="alert alert-' . htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') . ' border-0 shadow-sm my-3">' . htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8') . '</div>';
}
