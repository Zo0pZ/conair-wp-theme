/**
 * ConAir Extract Solutions — Theme JavaScript
 *
 * Handles: mobile nav toggle, scroll-reveal, before/after slider,
 * testimonials carousel, quote form.
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
       5.  TESTIMONIALS CAROUSEL
       Native horizontal scroll + snap does the heavy lifting (and
       covers touch/swipe for free); the chevrons just nudge it by
       roughly one "page" and disable themselves at either end.
    ────────────────────────────────────────────────────────────── */
    const testimonialTrack = document.getElementById('testimonial-track');
    const testimonialPrev  = document.getElementById('testimonial-prev');
    const testimonialNext  = document.getElementById('testimonial-next');

    if (testimonialTrack && testimonialPrev && testimonialNext) {
        const scrollByPage = (direction) => {
            testimonialTrack.scrollBy({
                left: direction * testimonialTrack.clientWidth * 0.9,
                behavior: 'smooth'
            });
        };

        testimonialPrev.addEventListener('click', () => scrollByPage(-1));
        testimonialNext.addEventListener('click', () => scrollByPage(1));

        const updateNavState = () => {
            const maxScroll = testimonialTrack.scrollWidth - testimonialTrack.clientWidth;
            testimonialPrev.disabled = testimonialTrack.scrollLeft <= 1;
            testimonialNext.disabled = testimonialTrack.scrollLeft >= maxScroll - 1;
        };

        let tracking = false;
        testimonialTrack.addEventListener('scroll', () => {
            if (tracking) return;
            tracking = true;
            requestAnimationFrame(() => {
                updateNavState();
                tracking = false;
            });
        });

        window.addEventListener('resize', updateNavState);
        updateNavState();
    }

    /* ──────────────────────────────────────────────────────────────
       6.  QUOTE FORM — real submission via inc/forms.php (admin-post.php)
       The <form> already works with JS disabled (a plain POST that
       redirects back with ?quote=sent / ?quote=error). This intercepts
       the submit to send it via fetch() instead, so the visitor gets an
       inline result without leaving/reloading the page.
    ────────────────────────────────────────────────────────────── */
    const quoteForm = document.getElementById('quote-form');

    if (quoteForm) {
        quoteForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const btn    = document.getElementById('submit-btn');
            const status = document.getElementById('form-status');
            const originalBtnText = btn ? btn.textContent : '';

            if (btn) {
                btn.disabled    = true;
                btn.textContent = 'Sending…';
            }
            if (status) {
                status.textContent = '';
                status.style.color = '#9a9a9a';
            }

            // Use getAttribute() here, not the .action property: the form
            // has a hidden field named "action" (required by WordPress's
            // admin-post.php routing), and a form control named "action"
            // shadows HTMLFormElement's built-in .action property — so
            // quoteForm.action resolves to that <input> element, not the
            // submit URL.
            fetch(quoteForm.getAttribute('action'), {
                method: 'POST',
                body: new FormData(quoteForm),
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                credentials: 'same-origin'
            })
                .then(response => response.json())
                .then(result => {
                    if (result && result.success) {
                        if (btn) {
                            btn.textContent      = '✓ Request Sent';
                            btn.style.background = '#141414';
                            btn.style.color      = '#00b4a2';
                            btn.style.border     = '1.5px solid rgba(0,180,162,0.5)';
                        }
                        if (status) {
                            status.textContent = (result.data && result.data.message) || 'Your quotation request has been sent. We will respond within 24 hours.';
                            status.style.color = '#00b4a2';
                        }
                        quoteForm.reset();
                    } else {
                        throw new Error((result && result.data && result.data.message) || 'Something went wrong.');
                    }
                })
                .catch(error => {
                    if (btn) {
                        btn.disabled    = false;
                        btn.textContent = originalBtnText;
                    }
                    if (status) {
                        status.textContent = error.message || 'Sorry, something went wrong sending your request — please call us instead.';
                        status.style.color = '#ff6b6b';
                    }
                });
        });
    }

});
