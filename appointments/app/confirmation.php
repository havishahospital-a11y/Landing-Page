<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <title>Request saved | Havisha Hospital</title>
    <link rel="stylesheet" href="<?= e(asset('/assets/site.css')) ?>">
    <script src="<?= e(url('/assets/confirmation.js')) ?>" defer></script>
    <link rel="icon" type="image/png" href="<?= e(url('/assets/favicon.png')) ?>">
    <?php require ROOT . '/app/fonts.php'; ?>
</head>

<body>
    <main class="confirmation wrap"><a class="brand"
            href="<?= e(url('/')) ?>"><?php require ROOT . '/app/logo.php'; ?></a>
        <p class="eyebrow">APPOINTMENT REQUEST RECEIVED</p>
        <h1>Your request<br>has been saved.</h1>
        <p>Our team will confirm your appointment date and time. This is a request, not a confirmed appointment.</p>
        <dl>
            <dt>Branch</dt>
            <dd><?= e($branches[$booking['branch']]['name']) ?></dd>
            <dt>Preferred date</dt>
            <dd><?= e((new DateTimeImmutable($booking['preferred_date']))->format('j F Y')) ?></dd>
            <dt>Booking reference</dt>
            <dd class="reference"><?= e($booking['public_reference']) ?></dd>
        </dl><a class="button" id="whatsapp-link" href="<?= e($wa) ?>">Continue on WhatsApp ↗</a>
        <p><strong>Please tap Send in WhatsApp.</strong> Your request remains saved even if you don’t open WhatsApp or
            send the message.</p>
        <p class="caption">If WhatsApp does not open automatically, use the button above.</p><a class="text-link"
            href="<?= e(url('/varicose-veins-treatment-appointment-in-' . $booking['branch'] . '/')) ?>">Back to branch
            page</a>
    </main>
</body>

</html>