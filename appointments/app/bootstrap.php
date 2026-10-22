<?php
declare(strict_types=1);
ini_set('display_errors', '0');
date_default_timezone_set('Asia/Kolkata');
define('ROOT', dirname(__DIR__));
if (!defined('PUBLIC_ROOT')) define('PUBLIC_ROOT', ROOT . '/public');
if ((int) ($_SERVER['CONTENT_LENGTH'] ?? 0) > 16384) {
    http_response_code(413);
    exit('Request too large.');
}
if (is_file(ROOT . '/.env')) {
    foreach (file(ROOT . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        if (str_starts_with(trim($line), '#') || !str_contains($line, '='))
            continue;
        [$key, $value] = explode('=', $line, 2);
        if (getenv(trim($key)) === false)
            putenv(trim($key) . '=' . trim($value, " \t\n\r\0\x0B\"'"));
    }
}
function env(string $key, string $default = ''): string
{
    $v = getenv($key);
    return $v === false ? $default : $v;
}
function basePath(): string
{
    // The development server mounts public at /, while XAMPP uses APP_URL's folder.
    if (PHP_SAPI === 'cli-server' && env('APP_ENV', 'local') !== 'production')
        return '';
    return rtrim((string) parse_url(env('APP_URL'), PHP_URL_PATH), '/');
}
function url(string $path): string
{
    return basePath() . '/' . ltrim($path, '/');
}
function asset(string $path): string
{
    return url($path) . '?v=' . filemtime(PUBLIC_ROOT . $path);
}
function e($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function failSafe(): void
{
    http_response_code(503);
    exit('The appointment service is temporarily unavailable. Please try again shortly.');
}
$production = env('APP_ENV', 'local') === 'production';
$expectedDocumentRoot = defined('FIXED_DOCUMENT_ROOT') && realpath(FIXED_DOCUMENT_ROOT) === realpath(ROOT) ? ROOT : PUBLIC_ROOT;
if ($production && (PHP_VERSION_ID < 80300 || strlen(env('APP_KEY')) < 64 || env('CONTENT_APPROVED') !== 'true' || !str_starts_with(env('APP_URL'), 'https://') || realpath($_SERVER['DOCUMENT_ROOT'] ?? '') !== realpath($expectedDocumentRoot)))
    failSafe();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: no-referrer');
header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
header("Content-Security-Policy: default-src 'self'; style-src 'self' https://fonts.googleapis.com; font-src 'self' https://fonts.gstatic.com; script-src 'self'; img-src 'self' https://i.ytimg.com; frame-src https://www.youtube-nocookie.com; base-uri 'none'; form-action 'self'; frame-ancestors 'none'; object-src 'none'");
header('Cache-Control: no-store');
if ($production)
    header('Strict-Transport-Security: max-age=31536000');
else
    header('X-Robots-Tag: noindex, nofollow');
session_name('havisha_session');
ini_set('session.use_strict_mode', '1');
if (!is_dir(ROOT . '/storage/sessions') && !mkdir(ROOT . '/storage/sessions', 0700, true))
    failSafe();
session_save_path(ROOT . '/storage/sessions');
session_set_cookie_params(['httponly' => true, 'secure' => $production, 'samesite' => 'Lax', 'path' => url('/')]);
if (!session_start())
    failSafe();
$_SESSION['csrf'] ??= bin2hex(random_bytes(32));
require ROOT . '/app/Validation.php';
require ROOT . '/app/Repository.php';
require ROOT . '/app/CrmClient.php';
require ROOT . '/app/AppointmentService.php';
$branches = require ROOT . '/config/branches.php';
$content = require ROOT . '/config/content.php';
$symptoms = require ROOT . '/config/symptoms.php';
