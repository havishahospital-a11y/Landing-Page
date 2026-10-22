<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="robots" content="noindex">
    <title>Appointment enquiry privacy | Havisha Hospital</title>
    <link rel="stylesheet" href="<?= e(asset('/assets/site.css')) ?>">
    <link rel="icon" type="image/png" href="<?= e(url('/assets/favicon.png')) ?>">
    <?php require ROOT . '/app/fonts.php'; ?>
</head>

<body>
    <main class="wrap confirmation"><a class="brand"
            href="<?= e(url('/')) ?>"><?php require ROOT . '/app/logo.php'; ?></a>
        <h1>Appointment enquiry privacy</h1>
        <p>This development notice requires hospital approval before launch.</p>
        <p>The form collects your name, phone number, branch, symptoms, city and preferred date so Havisha Hospital can
            respond to your appointment enquiry. It also records your consent, request reference and any supplied
            campaign attribution.</p>
        <p>The request is stored in the hospital’s appointment application. This form does not ask for marketing
            consent. A necessary session cookie protects the form and shows your confirmation.</p>
        <p>If you continue to WhatsApp, the link contains only the branch, booking reference and preferred date. You
            choose whether to send the message. Playing a testimonial connects to YouTube; video thumbnails are hosted
            on this website.</p>
        <p>Before launch, the hospital must supply its approved retention period, privacy contact, access and deletion
            process, and final privacy policy.</p><a class="text-link" href="<?= e(url('/')) ?>">Return to
            appointments</a>
    </main>
</body>

</html>