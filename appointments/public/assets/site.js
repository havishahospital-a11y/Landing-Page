'use strict';
const form = document.querySelector('#booking-form');
if (form) {
  const branch = form.querySelector('#branch');
  const originalDate = form.querySelector('#date');
  const calendar = JSON.parse(document.querySelector('#calendar-data').dataset.calendar);
  const date = originalDate;
  date.type = 'hidden';
  date.required = false;
  date.id = 'preferred-date-value';
  const picker = document.createElement('div');
  picker.className = 'date-picker';
  picker.innerHTML = `<button type="button" class="date-trigger" id="date" aria-expanded="false" aria-controls="appointment-calendar" aria-describedby="date-help date-error"><span>Select a date</span><span aria-hidden="true">&#9638;</span></button>
    <div class="date-calendar" id="appointment-calendar" hidden>
      <div class="calendar-heading"><button type="button" class="month-prev" aria-label="Previous month">&lsaquo;</button><strong class="calendar-month" aria-live="polite"></strong><button type="button" class="month-next" aria-label="Next month">&rsaquo;</button></div>
      <div class="calendar-week" aria-hidden="true"><span>Mo</span><span>Tu</span><span>We</span><span>Th</span><span>Fr</span><span>Sa</span><span>Su</span></div>
      <div class="calendar-days" role="group" aria-label="Choose a consultation date"></div>
      <p class="calendar-note">Select an available day. Our team will confirm your time.</p>
    </div>`;
  date.after(picker);
  const trigger = picker.querySelector('.date-trigger');
  const panel = picker.querySelector('.date-calendar');
  const days = picker.querySelector('.calendar-days');
  const previousMonth = picker.querySelector('.month-prev');
  const nextMonth = picker.querySelector('.month-next');
  let allowed = new Map();
  let months = [];
  let monthIndex = 0;
  const closeCalendar = (restoreFocus = false) => {
    panel.hidden = true; trigger.setAttribute('aria-expanded', 'false');
    if (restoreFocus) trigger.focus();
  };
  const renderMonth = () => {
    days.replaceChildren();
    previousMonth.disabled = monthIndex <= 0;
    nextMonth.disabled = monthIndex >= months.length - 1;
    if (!months.length) {
      picker.querySelector('.calendar-month').textContent = 'No dates available';
      return;
    }
    const [year, month] = months[monthIndex].split('-').map(Number);
    picker.querySelector('.calendar-month').textContent = new Date(year, month - 1, 1).toLocaleDateString('en-IN', { month: 'long', year: 'numeric' });
    const offset = (new Date(year, month - 1, 1).getDay() + 6) % 7;
    for (let i = 0; i < offset; i++) days.append(document.createElement('span'));
    for (let day = 1; day <= new Date(year, month, 0).getDate(); day++) {
      const value = `${year}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
      const button = document.createElement('button');
      button.type = 'button'; button.textContent = day; button.dataset.date = value;
      button.disabled = !allowed.has(value);
      button.setAttribute('aria-label', new Date(year, month - 1, day).toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' }) + (button.disabled ? ', unavailable' : ''));
      button.setAttribute('aria-pressed', String(date.value === value));
      if (date.value === value) button.className = 'selected';
      button.addEventListener('click', () => {
        date.value = value;
        trigger.firstElementChild.textContent = allowed.get(value);
        trigger.classList.add('has-date'); trigger.removeAttribute('aria-invalid');
        document.querySelector('#date-error').textContent = '';
        renderMonth(); closeCalendar(true);
        date.dispatchEvent(new Event('change', { bubbles: true }));
      });
      days.append(button);
    }
  };
  const updateDates = (preferred = '') => {
    allowed = new Map((calendar[branch.value] || []).map(day => [day.value, day.label]));
    months = [...new Set([...allowed.keys()].map(value => value.slice(0, 7)))];
    date.value = allowed.has(preferred) ? preferred : '';
    monthIndex = Math.max(0, months.indexOf(date.value.slice(0, 7)));
    trigger.firstElementChild.textContent = allowed.get(date.value) || 'Select a date';
    trigger.classList.toggle('has-date', !!date.value);
    renderMonth();
  };
  trigger.addEventListener('click', () => {
    panel.hidden = !panel.hidden;
    trigger.setAttribute('aria-expanded', String(!panel.hidden));
    if (!panel.hidden) days.querySelector('.selected, button:not(:disabled)')?.focus();
  });
  previousMonth.addEventListener('click', () => { monthIndex--; renderMonth(); if (previousMonth.disabled) nextMonth.focus(); });
  nextMonth.addEventListener('click', () => { monthIndex++; renderMonth(); if (nextMonth.disabled) previousMonth.focus(); });
  picker.addEventListener('keydown', event => {
    if (event.key === 'Escape') { event.preventDefault(); closeCalendar(true); }
    if (event.target.matches('.calendar-days button') && ['ArrowRight', 'ArrowLeft', 'ArrowUp', 'ArrowDown', 'Home', 'End'].includes(event.key)) {
      event.preventDefault();
      const choices = [...days.querySelectorAll('button:not(:disabled)')];
      const index = choices.indexOf(event.target);
      const delta = ['ArrowLeft', 'ArrowUp'].includes(event.key) ? -1 : 1;
      const target = event.key === 'Home' ? 0 : event.key === 'End' ? choices.length - 1 : Math.max(0, Math.min(choices.length - 1, index + delta));
      choices[target]?.focus();
    }
  });
  document.addEventListener('click', event => { if (!picker.contains(event.target)) closeCalendar(); });
  updateDates(date.value);
  branch.addEventListener('change', () => {
    const previous = date.value;
    updateDates(previous); closeCalendar();
    document.querySelector('#date-error').textContent = previous && !date.value ? 'Please choose a consultation date for the selected branch.' : '';
  });
  const symptomDropdown = form.querySelector('.symptom-dropdown');
  const symptomSummary = symptomDropdown?.querySelector('summary');
  const symptomSelection = symptomDropdown?.querySelector('.symptom-selection');
  const symptomInputs = [...form.querySelectorAll('[name="symptoms[]"]')];
  const symptomCancel = symptomDropdown?.querySelector('.symptom-cancel');
  const symptomContinue = symptomDropdown?.querySelector('.symptom-continue');
  let symptomSnapshot = [];
  const updateSymptomSelection = () => {
    const selected = symptomInputs.filter(input => input.checked);
    if (!symptomSelection) return;
    symptomSelection.textContent = selected.length === 0
      ? 'Select symptoms'
      : selected.length === 1
        ? selected[0].nextElementSibling.textContent
        : `${selected.length} symptoms selected / அறிகுறிகள் தேர்ந்தெடுக்கப்பட்டன`;
  };
  symptomInputs.forEach(input => input.addEventListener('change', updateSymptomSelection));
  updateSymptomSelection();
  symptomDropdown?.addEventListener('toggle', () => {
    if (symptomDropdown.open) symptomSnapshot = symptomInputs.filter(input => input.checked).map(input => input.value);
  });
  symptomCancel?.addEventListener('click', () => {
    symptomInputs.forEach(input => { input.checked = symptomSnapshot.includes(input.value); });
    updateSymptomSelection();
    symptomDropdown.open = false;
    symptomSummary?.focus();
  });
  symptomContinue?.addEventListener('click', () => {
    symptomDropdown.open = false;
    const nextField = form.querySelector('[value="other"]')?.checked
      ? form.querySelector('#other')
      : form.querySelector('#city');
    nextField?.focus();
  });
  document.addEventListener('click', event => {
    if (symptomDropdown?.open && !symptomDropdown.contains(event.target)) symptomDropdown.open = false;
  });
  symptomDropdown?.addEventListener('keydown', event => {
    if (event.key === 'Escape') { symptomDropdown.open = false; symptomSummary?.focus(); }
  });
  form.addEventListener('submit', event => {
    if (!allowed.has(date.value)) {
      event.preventDefault(); event.stopImmediatePropagation();
      document.querySelector('#date-error').textContent = 'Please select an available appointment date.';
      trigger.setAttribute('aria-invalid', 'true');
      panel.hidden = false; trigger.setAttribute('aria-expanded', 'true'); trigger.focus();
    }
  });
  const other = form.querySelector('[value="other"]');
  const otherField = form.querySelector('#other-field');
  const toggleOther = () => { otherField.hidden = !other.checked; form.querySelector('#other').required = other.checked; };
  other.addEventListener('change', toggleOther); toggleOther();
  let submitting = false;
  let retryPayload = null;
  const submitButton = form.querySelector('[type="submit"]');
  const defaultSubmitButton = submitButton.innerHTML;
  const status = document.createElement('div');
  status.className = 'submission-status full'; status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
  submitButton.before(status);
  const unlock = () => {
    submitting = false; form.removeAttribute('aria-busy');
    submitButton.disabled = false;
    if (retryPayload) submitButton.textContent = 'Retry this request / மீண்டும் முயற்சிக்கவும்';
    else submitButton.innerHTML = defaultSubmitButton;
  };
  form.addEventListener('submit', async event => {
    if (submitting) { event.preventDefault(); return; }
    if (!form.querySelector('[name="symptoms[]"]:checked')) {
      event.preventDefault();
      const group = document.querySelector('#symptoms');
      let error = document.querySelector('#symptoms-error');
      if (!error) { error = document.createElement('span'); error.id = 'symptoms-error'; error.className = 'field-error'; error.setAttribute('role', 'alert'); group.append(error); }
      error.textContent = 'Please select at least one symptom. / குறைந்தது ஓர் அறிகுறியைத் தேர்ந்தெடுக்கவும்.';
      if (symptomDropdown) symptomDropdown.open = true;
      symptomSummary?.focus(); return;
    }
    if (!window.fetch || !window.AbortController) {
      submitting = true; submitButton.disabled = true; submitButton.textContent = 'Saving your request... / உங்கள் கோரிக்கை சேமிக்கப்படுகிறது...'; return;
    }
    event.preventDefault();
    submitting = true; form.setAttribute('aria-busy', 'true');
    submitButton.disabled = true; submitButton.textContent = 'Saving your request... / உங்கள் கோரிக்கை சேமிக்கப்படுகிறது...';
    status.textContent = 'Please wait. Your request is being sent. Do not submit another form.';
    const payload = retryPayload || new FormData(form);
    retryPayload = payload;
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);
    let navigating = false;
    try {
      const response = await fetch(form.action.split('#')[0], {
        method: 'POST', body: payload, credentials: 'same-origin',
        headers: { Accept: 'application/json' }, signal: controller.signal
      });
      const result = await response.json();
      if (response.ok && typeof result.redirect === 'string') {
        const destination = new URL(result.redirect, location.href);
        if (destination.origin !== location.origin) throw new Error('Unexpected redirect');
        status.textContent = 'Your request is saved. Opening confirmation...';
        navigating = true; window.location.assign(destination.href); return;
      }
      if (!result.errors || typeof result.errors !== 'object') throw new Error('Unconfirmed response');
      status.replaceChildren();
      for (const message of Object.values(result.errors)) {
        const item = document.createElement('p'); item.textContent = message; status.append(item);
      }
      // Validation/rate-limit responses are definitive. Outages can be ambiguous: keep the exact original payload/key.
      if (response.status < 500) {
        retryPayload = null;
        if (typeof result.idempotency === 'string' && /^[a-f0-9]{64}$/.test(result.idempotency)) form.querySelector('[name="idempotency"]').value = result.idempotency;
      } else {
        const note = document.createElement('p');
        note.textContent = 'Retry sends the original details, even if you edit the fields, so an already-saved request is not duplicated.';
        status.append(note);
      }
    } catch (_) {
      status.textContent = 'The connection was interrupted or the server is taking too long. We cannot yet confirm whether your request was saved. Use Retry this request to safely resend the original details without creating a duplicate.';
    } finally {
      clearTimeout(timeout);
      if (!navigating) unlock();
    }
  });
  window.addEventListener('pageshow', event => { if (event.persisted) unlock(); });
  document.querySelector('.error-summary')?.focus();
  const sticky = document.querySelector('.mobile-cta');
  new IntersectionObserver(entries => { sticky.classList.toggle('is-hidden', entries[0].isIntersecting); }, { threshold: 0 }).observe(document.querySelector('#appointment'));
  form.addEventListener('focusin', () => sticky.classList.add('input-focused'));
  form.addEventListener('focusout', () => sticky.classList.remove('input-focused'));
}

const doctorTabs = [...document.querySelectorAll('.doctor-tabs [role="tab"]')];
if (doctorTabs.length) {
  const activateDoctorTab = tab => {
    doctorTabs.forEach(item => {
      const active = item === tab;
      item.setAttribute('aria-selected', String(active));
      item.tabIndex = active ? 0 : -1;
      document.querySelector('#' + item.getAttribute('aria-controls')).hidden = !active;
    });
  };
  doctorTabs.forEach((tab, index) => {
    tab.addEventListener('click', () => activateDoctorTab(tab));
    tab.addEventListener('keydown', event => {
      if (!['ArrowLeft', 'ArrowRight', 'Home', 'End'].includes(event.key)) return;
      event.preventDefault();
      const targetIndex = event.key === 'Home' ? 0 : event.key === 'End' ? doctorTabs.length - 1
        : (index + (event.key === 'ArrowRight' ? 1 : -1) + doctorTabs.length) % doctorTabs.length;
      activateDoctorTab(doctorTabs[targetIndex]);
      doctorTabs[targetIndex].focus();
    });
  });
}
// One accessible modal, loaded only after a patient chooses a video.
const videoButtons = document.querySelectorAll('.video-play');
if (videoButtons.length) {
  const modal = document.createElement('dialog');
  modal.className = 'video-modal';
  modal.setAttribute('aria-labelledby', 'video-modal-title');
  modal.innerHTML = `<div class="video-modal-heading"><h2 id="video-modal-title">Patient testimonial</h2><button type="button" class="video-modal-close" aria-label="Close video">&times;</button></div><div class="video-modal-player"></div><p class="video-modal-help">If playback does not start, press play in the video. <a target="_blank" rel="noopener noreferrer">Watch on YouTube</a></p>`;
  document.body.append(modal);
  const player = modal.querySelector('.video-modal-player');
  const close = modal.querySelector('.video-modal-close');
  let opener;
  const stop = () => {
    player.replaceChildren();
    document.documentElement.classList.remove('video-modal-open');
    opener?.focus({ preventScroll: true });
  };
  close.addEventListener('click', () => modal.close());
  modal.addEventListener('close', stop);
  modal.addEventListener('cancel', event => { event.preventDefault(); modal.close(); });
  let backdropPress = false;
  const outside = event => {
    const rect = modal.getBoundingClientRect();
    return event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom;
  };
  modal.addEventListener('pointerdown', event => { backdropPress = outside(event); });
  modal.addEventListener('click', event => { if (backdropPress && outside(event)) modal.close(); backdropPress = false; });
  videoButtons.forEach((button, index) => {
    button.setAttribute('aria-haspopup', 'dialog');
    button.addEventListener('click', () => {
      opener = button;
      const id = button.dataset.video;
      if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;
      const title = 'Patient testimonial ' + (index + 1);
      modal.querySelector('h2').textContent = title;
      modal.querySelector('a').href = 'https://www.youtube.com/shorts/' + id;
      modal.querySelector('a').textContent = 'Watch on YouTube';
      const frame = document.createElement('iframe');
      frame.src = 'https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&playsinline=1&rel=0';
      frame.title = title;
      frame.allow = 'autoplay; fullscreen; encrypted-media; picture-in-picture';
      frame.allowFullscreen = true;
      frame.referrerPolicy = 'strict-origin-when-cross-origin';
      player.replaceChildren(frame);
      document.documentElement.classList.add('video-modal-open');
      modal.showModal();
      close.focus({ preventScroll: true });
    });
  });
}

// On wide screens, center the left card above and move the right card between the lower pair.
const testimonialScroll = document.querySelector('.testimonial-scroll');
if (testimonialScroll) {
  const stage = testimonialScroll.querySelector('.testimonial-stage');
  const cards = [...testimonialScroll.querySelectorAll('.video-card')];
  const animatedLayout = window.matchMedia('(min-width: 901px) and (min-height: 720px)');
  let testimonialFrame = 0;
  const updateTestimonials = () => {
    testimonialFrame = 0;
    if (!animatedLayout.matches || cards.length < 4) {
      cards[0]?.style.removeProperty('transform');
      cards[1]?.style.removeProperty('transform');
      return;
    }
    const rect = testimonialScroll.getBoundingClientRect();
    const range = Math.max(1, testimonialScroll.offsetHeight - window.innerHeight);
    const progress = Math.max(0, Math.min(1, -rect.top / range));
    const upperLeft = cards[0];
    const upperRight = cards[1];
    const centerX = (stage.clientWidth - upperLeft.offsetWidth) / 2;
    const lowerCenterY = stage.clientHeight - upperRight.offsetHeight - Math.max(28, stage.clientHeight * .04);
    upperLeft.style.transform = `translate3d(${(centerX - upperLeft.offsetLeft) * progress}px, 0, 0)`;
    upperRight.style.transform = `translate3d(${(centerX - upperRight.offsetLeft) * progress}px, ${(lowerCenterY - upperRight.offsetTop) * progress}px, 0)`;
  };
  const requestTestimonialUpdate = () => {
    if (!testimonialFrame) testimonialFrame = requestAnimationFrame(updateTestimonials);
  };
  addEventListener('scroll', requestTestimonialUpdate, { passive: true });
  addEventListener('resize', requestTestimonialUpdate);
  animatedLayout.addEventListener('change', requestTestimonialUpdate);
  updateTestimonials();
}

// Prepare only offscreen content, so visible text never flashes or jumps.
const motionPreference = window.matchMedia('(prefers-reduced-motion: reduce)');
if ('IntersectionObserver' in window && !motionPreference.matches) {
  const pending = new Set();
  const reveal = element => {
    element.classList.remove('reveal-pending');
    pending.delete(element);
    observer.unobserve(element);
  };
  const observer = new IntersectionObserver(entries => {
    entries.forEach(entry => { if (entry.isIntersecting) reveal(entry.target); });
  }, { threshold: 0, rootMargin: '0px 0px -24px 0px' });
  document.querySelectorAll('.section.wrap > *, .section > .wrap > *').forEach(element => {
    if (element.getBoundingClientRect().top < window.innerHeight + 24) return;
    element.classList.add('reveal-content', 'reveal-pending');
    pending.add(element);
    observer.observe(element);
  });
  document.addEventListener('focusin', event => {
    pending.forEach(element => { if (element.contains(event.target)) reveal(element); });
  });
  motionPreference.addEventListener('change', event => {
    if (event.matches) pending.forEach(reveal);
  });
  window.addEventListener('beforeprint', () => pending.forEach(reveal));
  window.addEventListener('pageshow', event => {
    if (event.persisted) pending.forEach(reveal);
  });
}
