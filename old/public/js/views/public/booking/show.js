/*!
 |------------------------------------------------------------------
 | show.js — public booking calendar + availability fetch + submit
 |------------------------------------------------------------------
 |
 | Paired with resources/views/public/booking/show.blade.php.
 | Extracted from a previously-inline <script> block to satisfy
 | CodeCanyon's "no inline scripts unless dynamic" rule.
 |
 | Dynamic configuration is passed in via data-* attributes on the
 | `.wrap` element (the view sets them from Blade), so this file
 | contains zero Blade interpolation.
 |
 | Required `<meta>`:
 |   <meta name="csrf-token" content="...">
 |
 | Required `data-*` on .wrap:
 |   data-book-url        — POST endpoint to create the booking
 |   data-avail-url       — GET endpoint that returns available slots
 |   data-future-days-max — int, how far ahead booking is allowed
 */
(function () {
    'use strict';

    var wrap = document.querySelector('.wrap');
    if (!wrap) {
        return;
    }

    var BOOK_URL = wrap.dataset.bookUrl;
    var AVAIL_URL = wrap.dataset.availUrl;
    var FUTURE_MAX = parseInt(wrap.dataset.futureDaysMax || '60', 10);
    var CONFIRM_BOOKING_LABEL = wrap.dataset.confirmBookingLabel || 'Confirm booking';
    var ERROR_GENERIC = wrap.dataset.errorGeneric || 'Something went wrong. Please try again.';
    var ERROR_NETWORK = wrap.dataset.errorNetwork || 'Network error. Please try again.';
    var LOADING_TIMES = wrap.dataset.loadingTimes || 'Loading times…';
    var NO_AVAILABLE_TIMES = wrap.dataset.noAvailableTimes || 'No available times on this day. Try another date.';
    var COULD_NOT_LOAD_TIMES = wrap.dataset.couldNotLoadTimes || 'Could not load times. Please try again.';
    var AT_TIME_SEPARATOR = wrap.dataset.atTimeSeparator || ' at ';
    var BOOKING_IN_PROGRESS = wrap.dataset.bookingInProgress || 'Booking…';
    var TZ = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var CSRF = csrfMeta ? csrfMeta.getAttribute('content') : '';
    var MONTHS_FALLBACK = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    var MONTHS;
    try {
        var parsedMonths = JSON.parse(wrap.dataset.months || 'null');
        MONTHS = (Array.isArray(parsedMonths) && parsedMonths.length === 12)
            ? parsedMonths
            : MONTHS_FALLBACK;
    } catch (e) {
        MONTHS = MONTHS_FALLBACK;
    }

    var today = new Date();
    today.setHours(0, 0, 0, 0);
    var view = new Date(today.getFullYear(), today.getMonth(), 1);
    var selectedDate = null;
    var selectedSlot = null;

    function pad(n) {
        return n < 10 ? '0' + n : '' + n;
    }

    function ymd(d) {
        return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
    }

    function buildCalendar() {
        document.getElementById('cal-title').textContent =
            MONTHS[view.getMonth()] + ' ' + view.getFullYear();

        var firstDow = new Date(view.getFullYear(), view.getMonth(), 1).getDay();
        var offset = (firstDow === 0 ? 6 : firstDow - 1);
        var start = new Date(view.getFullYear(), view.getMonth(), 1 - offset);
        var days = document.getElementById('cal-days');
        days.innerHTML = '';

        var maxDate = new Date(today);
        maxDate.setDate(maxDate.getDate() + FUTURE_MAX);

        for (var i = 0; i < 42; i++) {
            var d = new Date(start);
            d.setDate(start.getDate() + i);
            var btn = document.createElement('button');
            btn.className = 'day';
            btn.textContent = d.getDate();
            btn.type = 'button';

            var other = d.getMonth() !== view.getMonth();
            var past = d < today;
            var tooFar = d > maxDate;

            if (other) {
                btn.classList.add('other');
                btn.disabled = true;
            } else if (past) {
                btn.classList.add('past');
                btn.disabled = true;
            } else if (tooFar) {
                btn.disabled = true;
            } else {
                btn.classList.add('avail');
                (function (dateStr, b) {
                    b.onclick = function () { selectDay(dateStr, b); };
                })(ymd(d), btn);
            }

            if (selectedDate === ymd(d)) {
                btn.classList.add('sel');
            }
            days.appendChild(btn);
        }
    }

    function selectDay(dateStr, el) {
        selectedDate = dateStr;
        selectedSlot = null;
        document.querySelectorAll('.day.sel').forEach(function (x) {
            x.classList.remove('sel');
        });
        el.classList.add('sel');
        loadSlots(dateStr);
    }

    function loadSlots(dateStr) {
        var slots = document.getElementById('slots');
        slots.innerHTML = '<div class="empty">' + LOADING_TIMES + '</div>';

        fetch(AVAIL_URL + '?date=' + encodeURIComponent(dateStr) + '&timezone=' + encodeURIComponent(TZ), {
            headers: { 'Accept': 'application/json' }
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.slots || data.slots.length === 0) {
                    slots.innerHTML = '<div class="empty">' + NO_AVAILABLE_TIMES + '</div>';
                    return;
                }
                slots.innerHTML = '';
                data.slots.forEach(function (s) {
                    var b = document.createElement('button');
                    b.className = 'slot';
                    b.textContent = s.label;
                    b.type = 'button';
                    b.onclick = function () { openModal(s); };
                    slots.appendChild(b);
                });
            })
            .catch(function () {
                slots.innerHTML = '<div class="empty">' + COULD_NOT_LOAD_TIMES + '</div>';
            });
    }

    function openModal(slot) {
        selectedSlot = slot;
        var d = new Date(slot.iso);
        document.getElementById('m-when').textContent =
            d.toLocaleDateString(undefined, { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }) +
            AT_TIME_SEPARATOR +
            d.toLocaleTimeString(undefined, { hour: 'numeric', minute: '2-digit' });
        document.getElementById('modal').classList.add('on');
    }

    function closeModal() {
        document.getElementById('modal').classList.remove('on');
    }

    document.getElementById('prev-month').onclick = function () {
        view = new Date(view.getFullYear(), view.getMonth() - 1, 1);
        buildCalendar();
    };
    document.getElementById('next-month').onclick = function () {
        view = new Date(view.getFullYear(), view.getMonth() + 1, 1);
        buildCalendar();
    };
    document.getElementById('cancel').onclick = closeModal;

    document.getElementById('form').addEventListener('submit', function (e) {
        e.preventDefault();
        if (!selectedSlot) {
            return;
        }

        var btn = document.getElementById('submit');
        btn.disabled = true;
        btn.textContent = BOOKING_IN_PROGRESS;
        document.querySelectorAll('.err').forEach(function (x) { x.textContent = ''; });

        var form = e.target;
        var params = new URLSearchParams(window.location.search);
        var body = {
            name: form.name.value,
            email: form.email.value,
            phone: form.phone.value,
            notes: form.notes.value,
            starts_at: selectedSlot.iso,
            timezone: TZ,
            __hp: form.__hp.value,
            utm_source: params.get('utm_source') || '',
            utm_medium: params.get('utm_medium') || '',
            utm_campaign: params.get('utm_campaign') || '',
            utm_content: params.get('utm_content') || '',
            utm_term: params.get('utm_term') || ''
        };

        fetch(BOOK_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': CSRF
            },
            body: JSON.stringify(body)
        })
            .then(function (r) { return r.json().then(function (j) { return { status: r.status, body: j }; }); })
            .then(function (res) {
                if (res.status === 200 && res.body.success) {
                    window.location.href = res.body.redirect;
                    return;
                }
                btn.disabled = false;
                btn.textContent = CONFIRM_BOOKING_LABEL;
                if (res.body.errors) {
                    Object.keys(res.body.errors).forEach(function (k) {
                        var el = document.querySelector('.err[data-for="' + k + '"]');
                        if (el) {
                            el.textContent = res.body.errors[k][0];
                        }
                    });
                } else if (res.body.message) {
                    alert(res.body.message);
                } else {
                    alert(ERROR_GENERIC);
                }
            })
            .catch(function () {
                btn.disabled = false;
                btn.textContent = CONFIRM_BOOKING_LABEL;
                alert(ERROR_NETWORK);
            });
    });

    buildCalendar();
})();
