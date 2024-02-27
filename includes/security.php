<?php

function sanitizeInput($input)
{
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function generateCSRFToken()
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token)
{
    if (!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] !== $token) {
        die('Invalid CSRF token.');
    }
}