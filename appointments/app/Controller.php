<?php
declare(strict_types=1);
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = basePath();
if ($base !== '' && str_starts_with($path, $base . '/'))
    $path = substr($path, strlen($base));
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: GET, POST');
    exit('Method not allowed');
}
if (preg_match('#^/varicose-veins-appointment-(coimbatore|madurai)/?$#D', $path, $legacy)) {
    $target = url('/varicose-veins-treatment-appointment-in-' . $legacy[1] . '/');
    if (($_SERVER['QUERY_STRING'] ?? '') !== '')
        $target .= '?' . $_SERVER['QUERY_STRING'];
    header('Location: ' . $target, true, $_SERVER['REQUEST_METHOD'] === 'POST' ? 307 : 301);
    exit;
}
if (preg_match('#^/varicose-veins-treatment-appointment-in-(coimbatore|madurai)$#D', $path, $bare)) {
    header('Location: ' . url($path . '/') . (empty($_SERVER['QUERY_STRING']) ? '' : '?' . $_SERVER['QUERY_STRING']), true, $_SERVER['REQUEST_METHOD'] === 'POST' ? 307 : 301);
    exit;
}
if ($path === '/') {
    header('Location: ' . url('/varicose-veins-treatment-appointment-in-coimbatore/'), true, 302);
    exit;
}
if ($path === '/confirmation/') {
    header('X-Robots-Tag: noindex, nofollow');
    $booking = $_SESSION['confirmation'] ?? null;
    if (!$booking) {
        http_response_code(404);
        exit('No appointment request in this session.');
    }
    $wa = AppointmentService::whatsapp($booking, $branches);
    require ROOT . '/app/confirmation.php';
    exit;
}
if ($path === '/privacy/') {
    require ROOT . '/app/privacy.php';
    exit;
}
if (!preg_match('#^/varicose-veins-treatment-appointment-in-(coimbatore|madurai)/$#D', $path, $match)) {
    http_response_code(404);
    exit('Page not found');
}
$slug = $match[1];
$branch = $branches[$slug];
$errors = [];
$jsonRequest = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
function bookingSaved(bool $jsonRequest): void
{
    if ($jsonRequest) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['redirect' => url('/confirmation/')]);
    } else {
        header('Location: ' . url('/confirmation/'), true, 303);
    }
    exit;
}
$treatmentUrl = 'https://havishahospital.com/varicose-veins-treatment-in-' . $slug . '/';
$hospitalUrl = 'https://havishahospital.com/multispecialty-hospital-' . $slug . '/';
$specialistUrl = 'https://havishahospital.com/specialists/varicose-veins-doctor-in-coimbatore-dr-harihara-jothi-v/';
$values = ['name' => '', 'phone' => '', 'branch' => $slug, 'city' => '', 'date' => '', 'other' => '', 'symptoms' => []];
$_SESSION['forms'] ??= [];
foreach ($_SESSION['forms'] as $token => $form)
    if ($form['created'] < time() - 7200)
        unset($_SESSION['forms'][$token]);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!is_string($_POST['csrf'] ?? null) || !hash_equals($_SESSION['csrf'], $_POST['csrf'])) {
        http_response_code(403);
        exit('Your session could not be verified. Reload the appointment page and try again.');
    }
    $idempotency = is_string($_POST['idempotency'] ?? null) ? $_POST['idempotency'] : '';
    [$values, $errors] = Validation::check($_POST, $branches, $symptoms);
    if (!isset($_SESSION['forms'][$idempotency]))
        $errors['form'] = 'This form has expired. Please submit again using the refreshed form below.';
    if (!empty($_POST['website']))
        $errors['form'] = 'Unable to submit this request.';
    try {
        $repo = new Repository(database());
        // Replays return the first saved result even if the original preferred date is now closed.
        if (!isset($errors['form']) && ($existing = $repo->find($idempotency))) {
            $_SESSION['confirmation'] = (new AppointmentService($repo))->sync($existing);
            bookingSaved($jsonRequest);
        }
        if (!$repo->limit($_SERVER['REMOTE_ADDR'] ?? 'unknown')) {
            http_response_code(429);
            $errors['form'] = 'Too many requests. Please wait 15 minutes before trying again.';
        }
        if (!$errors) {
            $form = $_SESSION['forms'][$idempotency];
            $_SESSION['confirmation'] = (new AppointmentService($repo))->create($values, $idempotency, $form['origin'], $form['attribution']);
            bookingSaved($jsonRequest);
        }
    } catch (Throwable $exception) {
        // Never log exception messages, SQL, form content, session IDs, or connection strings.
        error_log('[havisha] appointment processing unavailable');
        http_response_code(503);
        $errors['form'] = 'We could not confirm whether your request was saved. Please retry this form; the same request will not be saved twice.';
    }
    if (http_response_code() === 200)
        http_response_code(422);
}
if (!isset($idempotency) || !isset($_SESSION['forms'][$idempotency])) {
    $idempotency = bin2hex(random_bytes(32));
    $attribution = [];
    foreach (['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'gclid', 'fbclid'] as $field) {
        $v = $_GET[$field] ?? null;
        if (is_string($v) && preg_match('/^[a-zA-Z0-9 ._~+\-]{1,120}$/D', $v))
            $attribution[$field] = $v;
    }
    if (count($_SESSION['forms']) >= 20)
        array_shift($_SESSION['forms']);
    $_SESSION['forms'][$idempotency] = ['created' => time(), 'origin' => $path, 'attribution' => $attribution];
}
$calendar = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $jsonRequest) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['errors' => $errors, 'idempotency' => $idempotency], JSON_UNESCAPED_UNICODE);
    exit;
}
$now = new DateTimeImmutable();
foreach ($branches as $key => $item) {
    $dates = [];
    for ($i = 0; $i < 91; $i++) {
        $date = $now->modify('+' . $i . ' days')->format('Y-m-d');
        if (Validation::dateAllowed($date, $item, $now))
            $dates[] = ['value' => $date, 'label' => (new DateTimeImmutable($date))->format('D, j M Y')];
    }
    $calendar[$key] = $dates;
}
$privacy = env('PRIVACY_URL', '/privacy/');
if (!preg_match('#^(?:/(?!/)|https://)#', $privacy))
    $privacy = '/privacy/';
if (str_starts_with($privacy, '/'))
    $privacy = url($privacy);
require ROOT . '/app/landing.php';
