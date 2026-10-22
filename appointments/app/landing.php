<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Varicose Veins Treatment Appointment in <?= e($branch['name']) ?> | Havisha</title>
    <meta name="description"
        content="Book a varicose veins treatment appointment in <?= e($branch['name']) ?> at Havisha Hospital with a free Doppler screening scan. Request a visit.">
    <link rel="canonical" href="<?= e(rtrim(env('APP_URL', 'http://127.0.0.1:8088'), '/') . $path) ?>">
    <link rel="stylesheet" href="<?= e(asset('/assets/site.css')) ?>">
    <script src="<?= e(asset('/assets/site.js')) ?>" defer></script>
    <link rel="icon" type="image/png" href="<?= e(url('/assets/favicon.png')) ?>">
    <?php require ROOT . '/app/fonts.php'; ?>
</head>

<body><a class="skip" href="#main">Skip to content</a>

    <header class="header wrap"><a class="brand" href="<?= e(url($path)) ?>"
            aria-label="Havisha Hospital home"><?php require ROOT . '/app/logo.php'; ?></a><span
            class="header-branch"><span class="location-dot"></span><?= e($branch['name']) ?>, Tamil Nadu</span><a
            class="button small" href="#appointment">Request an appointment <span aria-hidden="true">↗</span></a>
    </header>
    <main id="main">
        <section class="hero">
            <div class="wrap hero-grid">
                <div class="hero-copy">
                    <p class="eyebrow">VARICOSE VEIN CARE · <?= e(strtoupper($branch['name'])) ?></p>
                    <h1>Varicose veins treatment appointment <em>in <?= e($branch['name']) ?>.</em></h1>
                    <p class="intro">Discuss visible veins, leg heaviness or swelling with <br>Dr. Hari Hara Jothi V. at
                        Havisha Hospital, <?= e($branch['name']) ?>. Start with an assessment and guidance on suitable
                        treatment options.</p>
                    <div class="offer">
                        <div><strong>Free Doppler Screening Scan</strong><span>Included as part of your appointment
                                assessment</span></div>
                    </div>
                </div>
                <div class="form-panel" id="appointment">
                    <div class="form-heading"><span class="eyebrow">LET’S PLAN YOUR VISIT</span>
                        <h2>Request an appointment</h2>
                        <p>Choose a preferred date. We’ll take care of the next step.</p>
                    </div>
                    <?php if ($errors): ?>
                        <div class="error-summary" role="alert" tabindex="-1"><strong>Please check your request</strong>
                            <ul><?php foreach ($errors as $field => $error): ?>
                                    <li><a href="#<?= e($field === 'form' ? 'appointment' : $field) ?>"><?= e($error) ?></a></li>
                                <?php endforeach ?>
                            </ul>
                        </div><?php endif ?>
                    <form action="<?= e(url($path)) ?>#appointment" method="post" id="booking-form"><input type="hidden"
                            name="csrf" value="<?= e($_SESSION['csrf']) ?>"><input type="hidden" name="idempotency"
                            value="<?= e($idempotency) ?>">
                        <div class="trap" aria-hidden="true"><label>Leave this empty<input name="website" tabindex="-1"
                                    autocomplete="off"></label></div>
                        <div class="form-grid">
                            <?php foreach (['name' => ['Full name / முழுப் பெயர்', 'Your full name', 'text', 'name', 100], 'phone' => ['Phone number / தொலைபேசி எண்', 'Mobile number', 'tel', 'tel', 24]] as $field => $spec): ?>
                                <div class="field"><label for="<?= $field ?>"><?= $spec[0] ?> <span>*</span></label><input
                                        id="<?= $field ?>" name="<?= $field ?>" type="<?= $spec[2] ?>"
                                        autocomplete="<?= $spec[3] ?>" placeholder="<?= $spec[1] ?>"
                                        maxlength="<?= $spec[4] ?>" value="<?= e($values[$field]) ?>" required
                                        <?= isset($errors[$field]) ? 'aria-invalid="true" aria-describedby="' . $field . '-error"' : '' ?>><?php if (isset($errors[$field])): ?><span
                                            class="field-error"
                                            id="<?= $field ?>-error"><?= e($errors[$field]) ?></span><?php endif ?></div>
                            <?php endforeach ?>
                            <div class="field full"><label for="branch">Branch / கிளை</label><input type="text"
                                    name="branch" id="branch" value="<?= e($slug) ?>" readonly
                                    aria-label="Branch: <?= e($branch['name']) ?>"
                                    data-branch-name="<?= e($branch['name']) ?>"
                                    <?= isset($errors['branch']) ? 'aria-invalid="true" aria-describedby="branch-error"' : '' ?>><?php if (isset($errors['branch'])): ?><span class="field-error"
                                        id="branch-error"><?= e($errors['branch']) ?></span><?php endif ?></div>
                            <fieldset class="full symptoms" id="symptoms"
                                <?= isset($errors['symptoms']) ? 'aria-describedby="symptoms-error"' : '' ?>>
                                <legend>What symptoms are you experiencing? / உங்களுக்கு என்ன அறிகுறிகள் உள்ளன? <span>*</span></legend>
                                <p>Select all that apply. / பொருந்தும் அனைத்தையும் தேர்ந்தெடுக்கவும்.</p>
                                <details class="symptom-dropdown">
                                    <summary><span class="symptom-selection">Select symptoms</span><span
                                            class="dropdown-arrow" aria-hidden="true">⌄</span></summary>
                                    <div class="symptom-options"><?php foreach ($symptoms as $key => $label): ?><label><input
                                                    type="checkbox" name="symptoms[]" value="<?= $key ?>"
                                                    <?= in_array($key, $values['symptoms'], true) ? 'checked' : '' ?>><span><?= e($label) ?></span></label><?php endforeach ?><div
                                            class="symptom-actions"><button type="button" class="symptom-cancel">Cancel</button><button
                                                type="button" class="symptom-continue">Continue</button></div></div>
                                </details>
                                <?php if (isset($errors['symptoms'])): ?><span class="field-error"
                                        id="symptoms-error"><?= e($errors['symptoms']) ?></span><?php endif ?>
                            </fieldset>
                            <div class="field full" id="other-field"><label for="other">Other symptoms / மற்ற அறிகுறிகள்</label><textarea name="other" id="other"
                                    <?= isset($errors['other']) ? 'aria-invalid="true" aria-describedby="other-error"' : '' ?> maxlength="500" rows="2"
                                    placeholder="Describe other symptoms"><?= e($values['other']) ?></textarea><?php if (isset($errors['other'])): ?><span
                                        class="field-error" id="other-error"><?= e($errors['other']) ?></span><?php endif ?>
                            </div>
                            <div class="field city-field"><label for="city">City / நகரம் <span>*</span></label><input name="city" id="city"
                                    <?= isset($errors['city']) ? 'aria-invalid="true" aria-describedby="city-error"' : '' ?>
                                    autocomplete="address-level2" maxlength="100" placeholder="Your city" required
                                    value="<?= e($values['city']) ?>"><?php if (isset($errors['city'])): ?><span
                                        class="field-error" id="city-error"><?= e($errors['city']) ?></span><?php endif ?>
                            </div>
                            <div class="field date-field"><label for="date">Preferred Appointment Date / சந்திப்பு தேதி <span>*</span></label><input
                                    type="date" name="date" id="date" min="<?= $now->format('Y-m-d') ?>"
                                    value="<?= e($values['date']) ?>" required
                                    aria-describedby="date-help date-error"><span class="field-error"
                                    id="date-error"><?= e($errors['date'] ?? '') ?></span></div>
                            <label class="consent full" id="consent"><input type="checkbox" name="consent" value="1"
                                    required <?= ($_POST['consent'] ?? '') === '1' ? 'checked' : '' ?>><span>I agree to be
                                    contacted by Havisha Hospital about this enquiry. / இந்த விசாரணை தொடர்பாக ஹவிஷா
                                    மருத்துவமனை என்னைத் தொடர்புகொள்ள நான் சம்மதிக்கிறேன். <a
                                        href="<?= e($privacy) ?>">Privacy policy</a>.</span></label><?php if (isset($errors['consent'])): ?><span
                                    class="field-error full"><?= e($errors['consent']) ?></span><?php endif ?>
                            <button class="button full submit" type="submit"><span class="submit-copy">Request an Appointment /
                                    <span lang="ta">சந்திப்பிற்கு முன்பதிவு செய்யவும்</span></span><span aria-hidden="true">↗</span></button>
                            <p class="form-foot full">No online payment needed / ஆன்லைன் கட்டணம் தேவையில்லை</p>
                        </div>
                    </form>
                </div>
            </div>
        </section>
        <div class="care-strip">
            <div class="wrap"><span>Consultation & Doppler screening</span><span>Individual treatment
                    guidance</span><span>30-day consultation & scan follow-up</span></div>
        </div>
        <section class="section wrap doctor-profile" id="specialist">
            <div class="doctor-tabs" role="tablist" aria-label="Doctor profile sections">
                <button type="button" role="tab" id="doctor-tab-profile" aria-selected="true"
                    aria-controls="doctor-panel-profile">Meet the doctor</button>
                <button type="button" role="tab" id="doctor-tab-expertise" aria-selected="false" tabindex="-1"
                    aria-controls="doctor-panel-expertise">Medical expertise</button>
            </div>
            <div class="doctor-profile-grid">
                <div class="doctor-portrait"><img src="<?= e(url('/assets/doctor.webp')) ?>"
                        alt="Dr. Hari Hara Jothi V. at Havisha Hospital" width="673" height="423" loading="lazy">
                    <span>PHLEBOLOGIST · DIABETIC FOOT CARE SPECIALIST</span>
                </div>
                <div class="doctor-panel" id="doctor-panel-profile" role="tabpanel"
                    aria-labelledby="doctor-tab-profile">
                    <p class="eyebrow">MEET YOUR SPECIALIST</p>
                    <h2>Hello, I am<br>Dr. Hari Hara Jothi V.</h2>
                    <p class="doctor-role">Phlebologist and Diabetic Foot Care Specialist</p>
                    <dl class="doctor-facts">
                        <div><dt>Name</dt><dd>Dr. Hari Hara Jothi V.</dd></div>
                        <div><dt>Experience</dt><dd>15+ years</dd></div>
                        <div><dt>Phone</dt><dd><a href="tel:+91<?= $slug === 'coimbatore' ? '8098519191' : '8610519191' ?>"><?= $slug === 'coimbatore' ? '8098519191' : '8610519191' ?></a></dd></div>
                        <div><dt>Email</dt><dd><a href="mailto:havishahospital@gmail.com">havishahospital@gmail.com</a></dd></div>
                        <div><dt>Language</dt><dd>Tamil, English and Malayalam</dd></div>
                    </dl>
                    <div class="doctor-social" aria-label="Havisha Hospital social media">
                        <span>Social media</span>
                        <a href="https://www.facebook.com/profile.php?id=61563188004938" target="_blank"
                            rel="noopener noreferrer" aria-label="Havisha Hospital on Facebook" title="Facebook"><svg
                                viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3.3 0-5 2-5 5v3H6v4h3v5h4v-5h3l1-4h-4V9c0-.7.3-1 1-1Z" /></svg></a>
                        <a href="https://www.instagram.com/havishahospital/" target="_blank" rel="noopener noreferrer"
                            aria-label="Havisha Hospital on Instagram" title="Instagram"><svg viewBox="0 0 24 24"
                                aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" class="solid"/></svg></a>
                        <a href="https://x.com/HavishaHospital" target="_blank" rel="noopener noreferrer"
                            aria-label="Havisha Hospital on X" title="X"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 4 14 16M19 4 5 20"/></svg></a>
                        <a href="https://www.linkedin.com/company/havisha-hospital/" target="_blank"
                            rel="noopener noreferrer" aria-label="Havisha Hospital on LinkedIn" title="LinkedIn"><svg
                                viewBox="0 0 24 24" aria-hidden="true"><path d="M5 9v10M5 5.5v.1M10 19V9m0 4.5c0-2.5 2-4.5 4.5-4.5s4.5 2 4.5 4.5V19"/></svg></a>
                        <a href="https://www.youtube.com/@havishahospital" target="_blank" rel="noopener noreferrer"
                            aria-label="Havisha Hospital on YouTube" title="YouTube"><svg viewBox="0 0 24 24"
                                aria-hidden="true"><rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3Z" class="solid"/></svg></a>
                    </div>
                    <a class="text-link" href="<?= e($specialistUrl) ?>">View full doctor profile <span aria-hidden="true">↗</span></a>
                </div>
                <div class="doctor-panel" id="doctor-panel-expertise" role="tabpanel"
                    aria-labelledby="doctor-tab-expertise" hidden>
                    <h2>About Me</h2>
                    <p><strong>Dr. Hari Hara Jothi V. (MBBS, MCPhleb–London, ECoP, CCDFM)</strong> is a highly trained
                        <strong>varicose veins specialist in <?= e($branch['name']) ?></strong> with expertise in
                        <strong>ultrasound-guided foam sclerotherapy (UGFS)</strong> and regenerative treatment for
                        chronic ulcers. His London-based phlebology training supports internationally recognised,
                        evidence-based vein care with minimal discomfort and faster recovery.</p>
                    <p>He is an active member of the <strong>Diabetic Foot Society of India</strong> and the
                        <strong>Indian Podiatry Association</strong>.</p>
                    <div class="doctor-working">
                        <h3>Working information</h3>
                        <p><strong>Role:</strong> Phlebologist and Diabetic Foot Care Specialist</p>
                        <p><strong>Position:</strong> Senior Doctor at Havisha Hospital</p>
                    </div>
                    <aside class="doctor-highlight">
                        <span>HIGHLIGHT</span>
                        <strong>15+ years of focused clinical experience</strong>
                        <p>Advanced varicose vein care, diabetic foot care and chronic-ulcer management guided by
                            evidence-based assessment.</p>
                    </aside>
                </div>
            </div>
        </section>
        <section class="testimonials testimonial-scroll">
            <div class="wrap testimonial-stage">
                <div class="section-top">
                    <div>
                        <p class="eyebrow">PATIENT TESTIMONIALS</p>
                        <h2>Testimonials</h2>
                    </div>
                    <p>Experiences shared by patients.<br>Each person’s care and recovery are different.</p>
                </div>
                <div class="video-grid"><?php foreach ($content['videos'] as $i => $video): ?>
                        <article class="video-card">
                            <div class="video-frame"><button class="video-play" data-video="<?= e($video) ?>"
                                    aria-label="Play patient testimonial <?= $i + 1 ?>"><img
                                        src="<?= e(url('/assets/' . $video . '.jpg')) ?>"
                                        alt="Patient testimonial video <?= $i + 1 ?> preview" width="480" height="360"
                                        loading="lazy" referrerpolicy="no-referrer"><span class="play-mark"
                                        aria-hidden="true">▶</span><span class="video-label">WATCH THEIR STORY
                                        <span>↗</span></span></button></div><a
                                href="https://www.youtube.com/shorts/<?= e($video) ?>" target="_blank"
                                rel="noopener noreferrer">Patient story <?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?>
                                <span aria-hidden="true">↗</span></a>
                        </article><?php endforeach ?>
                </div>
            </div>
        </section>
        <section class="section wrap symptoms-section">
            <div>
                <p class="eyebrow">LISTEN TO YOUR LEGS</p>
                <h2>Small changes<br>deserve attention.</h2>
                <p>These are some of the symptoms we assess. A consultation can help you understand what may be causing
                    them.</p>
                <p class="caption">Symptoms alone do not establish a diagnosis.</p>
            </div>
            <div class="symptom-list">
                <?php foreach (['Visible veins' => 'Veins that look enlarged or more noticeable.', 'Heaviness & swelling' => 'Legs that feel heavy, tired or swollen.', 'Itching & skin changes' => 'Persistent itching or changes in skin colour or texture.', 'Non-healing wounds' => 'A leg wound that is slow to heal.'] as $title => $desc): ?>
                    <div><span class="line-cross" aria-hidden="true">+</span>
                        <div>
                            <h3><?= e($title) ?></h3>
                            <p><?= e($desc) ?></p>
                        </div>
                    </div><?php endforeach ?>
            </div>
        </section>
        <section class="section visit">
            <div class="wrap">
                <div class="visit-intro">
                    <div>
                        <p class="eyebrow">WHAT HAPPENS AT YOUR VISIT</p>
                        <h2>Know what to expect.</h2>
                        <p>Your consultation and assessment help guide the next steps in your care.</p>
                    </div>
                    <figure class="care-image"><img src="<?= e(url('/assets/evaluation.webp')) ?>"
                            srcset="<?= e(url('/assets/evaluation-small.webp')) ?> 400w, <?= e(url('/assets/evaluation.webp')) ?> 736w"
                            sizes="(max-width:760px) calc(100vw - 36px), 480px" width="736" height="736" loading="lazy"
                            decoding="async" alt="Illustrative image of leg care with a vein diagram overlay"></figure>
                </div>
                <ol class="steps">
                    <?php foreach (['Let’s talk' => 'Discuss your symptoms and medical history with the doctor.', 'Assess & screen' => 'Clinical assessment and Doppler screening help guide the discussion.', 'Understand the findings' => 'Your doctor explains the assessment and answers your questions.', 'Plan your next step' => 'Receive individual guidance on suitable care and follow-up.'] as $stepIndex => $desc): $title = $stepIndex; $iconIndex = array_search($title, ['Let’s talk', 'Assess & screen', 'Understand the findings', 'Plan your next step'], true); ?>
                        <li>
                            <span class="step-icon" aria-hidden="true">
                                <?php if ($iconIndex === 0): ?>
                                    <svg viewBox="0 0 24 24"><path d="M5 5.5h14v10H9l-4 3v-13Z"/><path d="M8.5 9h7M8.5 12h5"/></svg>
                                <?php elseif ($iconIndex === 1): ?>
                                    <svg viewBox="0 0 24 24"><path d="M8 4v5a4 4 0 0 0 8 0V4M6 4h4M14 4h4"/><path d="M12 13v2a4 4 0 0 0 8 0v-1"/><circle cx="20" cy="11.5" r="1.5"/></svg>
                                <?php elseif ($iconIndex === 2): ?>
                                    <svg viewBox="0 0 24 24"><path d="M7 3.5h8l3 3V20H7z"/><path d="M15 3.5V7h3M9.5 11h6M9.5 14h6M9.5 17h3"/></svg>
                                <?php else: ?>
                                    <svg viewBox="0 0 24 24"><path d="M4 18.5h4l3-4 3 2 6-8"/><path d="M15.5 8.5H20V13"/><circle cx="6" cy="6" r="2.5"/></svg>
                                <?php endif ?>
                            </span>
                            <h3><?= e($title) ?></h3>
                            <p><?= e($desc) ?></p>
                        </li><?php endforeach ?>
                </ol>
            </div>
        </section>
        <section class="section wrap treatment">
            <div>
                <p class="eyebrow">TREATMENT & RECOVERY</p>
                <h2>Varicose vein treatment options in <?= e($branch['name']) ?></h2>
                <figure class="care-image"><img src="<?= e(url('/assets/rfa-treatment.webp')) ?>"
                        srcset="<?= e(url('/assets/rfa-treatment-small.webp')) ?> 400w, <?= e(url('/assets/rfa-treatment.webp')) ?> 736w"
                        sizes="(max-width:760px) calc(100vw - 36px), 480px" width="736" height="736" loading="lazy"
                        decoding="async" alt="Illustrative image of an ultrasound-guided vein procedure"></figure>
            </div>
            <div>
                <h3 class="large-copy">Your treatment should fit your needs.</h3>
                <p>Your doctor will explain the options suitable for your findings, medical history and preferences,
                    including whether a procedure is appropriate.</p>
                <ul class="treatment-options">
                    <li><strong>Endovenous laser treatment (EVLT):</strong> laser energy is used to close an affected
                        vein.</li>
                    <li><strong>Radiofrequency ablation (RFA):</strong> heat from radiofrequency energy is used to close
                        an affected vein.</li>
                    <li><strong>Ultrasound-guided foam sclerotherapy:</strong> foam is injected into a selected vein
                        under ultrasound guidance.</li>
                </ul>
                <p>Your doctor will explain whether a procedure or non-procedural care is suitable after reviewing your
                    assessment.</p>
                <p><a class="text-link" href="<?= e($treatmentUrl) ?>">Explore varicose veins treatment in
                        <?= e($branch['name']) ?> <span aria-hidden="true">↗</span></a></p>
                <?php foreach ($content['services'] as $service): ?>
                    <h3><?= e($service['name']) ?></h3>
                    <p><?= e($service['description']) ?></p><?php endforeach ?>
                <blockquote>Same-day discharge for suitable patients following varicose vein procedures, subject to the
                    doctor’s assessment.</blockquote>
                <p class="caption">Treatment and recovery guidance are discussed individually after your assessment.</p>
            </div>
        </section>
        <section class="section wrap faq">
            <div>
                <p class="eyebrow">A LITTLE MORE CLARITY</p>
                <h2>Your questions,<br>answered.</h2>
            </div>
            <div><?php foreach ([
                'Is Doppler screening available?' => 'A free Doppler screening scan is included as part of the appointment assessment.',
                'Will I need a follow-up?' => 'Your doctor will advise whether a follow-up assessment or scan is appropriate for you.',
                'Is my appointment confirmed after submitting?' => 'No. Your request is saved first, then you can continue on WhatsApp. Please tap Send there. Our team will confirm your appointment date and time. No online payment is required.',
                'What should I bring to the consultation?' => 'Bring previous medical reports, any earlier scan results and a list of the medicines you currently take.',
                'How is a treatment selected?' => 'Your doctor will discuss suitable options following your consultation and assessment. Treatment recommendations are individual; submitting a request does not commit you to a procedure.',
                'Can I go home on the same day?' => 'Same-day discharge for suitable patients following varicose vein procedures, subject to the doctor’s assessment.'
            ] as $question => $answer): ?>
                    <details>
                        <summary><?= e($question) ?><span aria-hidden="true">+</span></summary>
                        <p><?= e($answer) ?></p>
                    </details><?php endforeach ?>
            </div>
        </section>
        <section class="location section">
            <div class="wrap location-grid">
                <div class="hospital-photo"><?php if ($slug === 'madurai'): ?><img
                            src="<?= e(url('/assets/easvara-hospital.webp')) ?>"
                            srcset="<?= e(url('/assets/easvara-hospital-small.webp')) ?> 768w, <?= e(url('/assets/easvara-hospital.webp')) ?> 1536w"
                            sizes="(max-width: 760px) 100vw, 50vw" alt="Easvara Hospital building in Madurai" width="1536"
                            height="1025" loading="lazy"><?php else: ?><img src="<?= e(url('/assets/hospital.webp')) ?>"
                            srcset="<?= e(url('/assets/hospital-small.webp')) ?> 768w, <?= e(url('/assets/hospital.webp')) ?> 1536w"
                            sizes="(max-width: 760px) 100vw, 50vw" alt="Havisha Hospital building" width="1536"
                            height="1024"
                            loading="lazy"><?php if ($content['hospital_photo_branch'] !== $slug): ?><span></span><?php endif ?><?php endif ?></div>
                <div>
                    <p class="eyebrow">YOUR VISIT STARTS HERE</p><?php if ($slug === 'madurai'): ?>
                        <h2>Varicose Veins Treatment in Madurai</h2>
                        <p class="hospital-association">Havisha Hospital, in association with Easvara Hospital</p>
                    <?php else: ?>
                        <h2>Visit us in <?= e($branch['name']) ?>.</h2><?php endif ?>
                    <address><?= e($branch['address']) ?></address>
                    <p><a class="text-link" href="<?= e($hospitalUrl) ?>">About Havisha Hospital in
                            <?= e($branch['name']) ?> <span aria-hidden="true">↗</span></a></p>
                    <dl class="hours"><?php foreach ($branch['hours'] as $day => $hours): ?>
                            <div>
                                <dt><?= e($day) ?></dt>
                                <dd><?= e($hours) ?></dd>
                            </div><?php endforeach ?>
                    </dl>
                    <p class="caption">Consultation hours · India time · By confirmed appointment</p>
                    <div class="location-actions"><a class="button" href="#appointment">Request an appointment ↗</a><a
                            class="text-link" href="<?= e($branch['maps']) ?>" target="_blank"
                            rel="noopener noreferrer">Get directions ↗</a></div>
                    <p class="phone-link">Branch contact: <a
                            href="tel:+<?= e($branch['whatsapp']) ?>"><?= e($branch['phone']) ?></a></p>
                </div>
            </div>
        </section>
    </main>
    <footer class="wrap footer"><a class="brand" href="<?= e(url($path)) ?>"><?php require ROOT . '/app/logo.php'; ?></a>
        <p>Thoughtful care. A clearer next step.</p>
        <nav aria-label="Footer"><a
                href="<?= e(url('/varicose-veins-treatment-appointment-in-coimbatore/')) ?>">Coimbatore</a><a
                href="<?= e(url('/varicose-veins-treatment-appointment-in-madurai/')) ?>">Madurai</a><a
                href="<?= e($privacy) ?>">Privacy</a></nav>
    </footer><a href="#appointment" class="button mobile-cta">Request an Appointment ↗</a>
    <div id="calendar-data" hidden data-calendar="<?= e(json_encode($calendar)) ?>"></div>
</body>

</html>
