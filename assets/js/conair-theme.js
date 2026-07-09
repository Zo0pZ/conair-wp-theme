/**
 * ConAir Extract Solutions — Theme JavaScript
 *
 * Handles: mobile nav toggle, scroll-reveal, before/after slider, quote form.
 * All logic wrapped in DOMContentLoaded so it's safe to load from footer.
 */
document.addEventListener('DOMContentLoaded', function () {

    /* ──────────────────────────────────────────────────────────────
       1.  COPYRIGHT YEAR
       (Keeps the footer year current without a rebuild.)
    ────────────────────────────────────────────────────────────── */
    const yearEl = document.getElementById('year');
    if (yearEl) {
        yearEl.textContent = new Date().getFullYear();
    }

    /* ──────────────────────────────────────────────────────────────
       2.  MOBILE MENU TOGGLE
    ────────────────────────────────────────────────────────────── */
    const menuBtn    = document.getElementById('menu-btn');
    const mobileMenu = document.getElementById('mobile-menu');
    const iconOpen   = document.getElementById('icon-open');
    const iconClose  = document.getElementById('icon-close');

    if (menuBtn && mobileMenu) {
        menuBtn.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            menuBtn.setAttribute('aria-expanded', String(isOpen));
            menuBtn.setAttribute(
                'aria-label',
                isOpen ? 'Close navigation menu' : 'Open navigation menu'
            );
            if (iconOpen)  iconOpen.classList.toggle('hidden', isOpen);
            if (iconClose) iconClose.classList.toggle('hidden', !isOpen);
        });

        // Close menu when a nav link is tapped
        mobileMenu.querySelectorAll('a').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                menuBtn.setAttribute('aria-expanded', 'false');
                menuBtn.setAttribute('aria-label', 'Open navigation menu');
                if (iconOpen)  iconOpen.classList.remove('hidden');
                if (iconClose) iconClose.classList.add('hidden');
            });
        });
    }

    /* ──────────────────────────────────────────────────────────────
       3.  SCROLL REVEAL
       Staggers siblings so cards animate in sequence.
    ────────────────────────────────────────────────────────────── */
    const revealObs = new IntersectionObserver(
        (entries) => {
            entries.forEach(entry => {
                if (!entry.isIntersecting) return;

                // Stagger siblings that haven't yet become visible
                const pending = [
                    ...entry.target.parentElement.querySelectorAll('.reveal:not(.visible)')
                ];
                const delay = pending.indexOf(entry.target) * 80;
                setTimeout(() => entry.target.classList.add('visible'), delay);
                revealObs.unobserve(entry.target);
            });
        },
        { threshold: 0.1, rootMargin: '0px 0px -30px 0px' }
    );

    document.querySelectorAll('.reveal').forEach(el => revealObs.observe(el));

    /* ──────────────────────────────────────────────────────────────
       4.  BEFORE / AFTER SLIDER
       WCAG 2.1.1 keyboard support, 2.5.7 draggable alternative.
    ────────────────────────────────────────────────────────────── */
    const slider     = document.getElementById('ba-slider');
    const afterLayer = document.getElementById('ba-after-layer');
    const divider    = document.getElementById('ba-divider');

    if (slider && afterLayer && divider) {
        let pct      = 50;
        let dragging = false;

        function setPos(newPct) {
            pct = Math.min(Math.max(newPct, 3), 97);
            const s = pct.toFixed(1) + '%';
            divider.style.left     = s;
            afterLayer.style.width = s;
            slider.setAttribute('aria-valuenow', String(Math.round(pct)));
        }

        function fromClientX(clientX) {
            const rect = slider.getBoundingClientRect();
            setPos(((clientX - rect.left) / rect.width) * 100);
        }

        // Mouse
        divider.addEventListener('mousedown', e => { dragging = true; e.preventDefault(); });
        window.addEventListener('mousemove',  e => { if (dragging) fromClientX(e.clientX); });
        window.addEventListener('mouseup',    ()  => { dragging = false; });

        // Touch
        divider.addEventListener('touchstart', e => {
            dragging = true;
            e.preventDefault();
        }, { passive: false });

        window.addEventListener('touchmove', e => {
            if (dragging) {
                fromClientX(e.touches[0].clientX);
                e.preventDefault();
            }
        }, { passive: false });

        window.addEventListener('touchend', () => { dragging = false; });

        // Click anywhere on the slider track
        slider.addEventListener('click', e => { if (!dragging) fromClientX(e.clientX); });

        // Keyboard (WCAG 2.1.1)
        slider.addEventListener('keydown', e => {
            const step = e.shiftKey ? 10 : 5;
            const moves = {
                ArrowLeft:  -step,
                ArrowRight:  step,
                Home:       -100,
                End:         100,
            };
            if (moves[e.key] !== undefined) {
                const dest = e.key === 'Home' ? 3
                           : e.key === 'End'  ? 97
                           : pct + moves[e.key];
                setPos(dest);
                e.preventDefault();
            }
        });
    }

    /* ──────────────────────────────────────────────────────────────
       5.  QUOTE FORM — UI feedback on submit
       Real submission requires a server-side handler or CF7 / WPForms.
       This gives instant visual confirmation while the integration is set up.
    ────────────────────────────────────────────────────────────── */
    const quoteForm = document.getElementById('quote-form');

    if (quoteForm) {
        quoteForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn    = document.getElementById('submit-btn');
            const status = document.getElementById('form-status');

            if (btn) {
                btn.textContent      = '✓ Request Sent';
                btn.style.background = '#141414';
                btn.style.color      = '#00b4a2';
                btn.style.border     = '1.5px solid rgba(0,180,162,0.5)';
                btn.disabled         = true;
            }

            if (status) {
                status.textContent = 'Your quotation request has been sent. We will respond within 24 hours.';
                status.style.color = '#00b4a2';
            }
        });
    }

});
