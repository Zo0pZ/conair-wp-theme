<?php
/**
 * Title: Get a Quote — Contact Form
 * Slug: conair-theme/quote-form
 * Categories: conair-sections, conair-cta
 * Description: Full quotation request form with name, phone, email, service selector, and message. Submits to inc/forms.php via admin-post.php — no form plugin required.
 */

// No-JS fallback: inc/forms.php redirects back here with ?quote=sent or
// ?quote=error when JavaScript didn't intercept the submit.
$conair_quote_result = isset( $_GET['quote'] ) ? sanitize_key( wp_unslash( $_GET['quote'] ) ) : '';
?>
<!-- wp:html -->
<section id="quote" style="background:#111111;border-top:1px solid #242424;">
  <div style="height:3px;background:linear-gradient(90deg,transparent,#00b4a2,transparent);" aria-hidden="true"></div>
  <div class="max-w-4xl mx-auto px-4 sm:px-6 py-16 sm:py-20 text-center reveal">
    <div class="badge mb-5 mx-auto" style="display:inline-flex;">Get a Quote</div>
    <h2 class="font-black text-white leading-tight mb-4" style="font-size:clamp(1.8rem,4vw,3rem);">Ready to Get Compliant?</h2>
    <div class="rounded-2xl p-6 sm:p-8 max-w-3xl mx-auto text-left" style="background:#141414;border:1px solid #2e2e2e;">
      <h3 class="text-white font-bold mb-6" style="font-size:1.1rem;">Request a Quotation</h3>
      <form id="quote-form" class="space-y-5" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate aria-label="Quotation request">
        <input type="hidden" name="action" value="conair_submit_quote_form">
        <?php wp_nonce_field( 'conair_quote_form', 'conair_quote_nonce' ); ?>
        <div style="position:absolute;left:-9999px;" aria-hidden="true">
          <label for="hp-website">Website</label>
          <input type="text" id="hp-website" name="hp_website" tabindex="-1" autocomplete="off">
        </div>
        <div>
          <label for="f-name" class="block font-semibold mb-2 uppercase tracking-wider" style="font-size:12px;color:#9a9a9a;">
            Full Name <span style="color:#00b4a2;" aria-hidden="true">*</span><span class="sr-only">(required)</span>
          </label>
          <input id="f-name" name="name" type="text" required autocomplete="name" placeholder="e.g. Sarah Johnson" class="field" aria-required="true"/>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label for="f-phone" class="block font-semibold mb-2 uppercase tracking-wider" style="font-size:12px;color:#9a9a9a;">
              Phone <span style="color:#00b4a2;" aria-hidden="true">*</span><span class="sr-only">(required)</span>
            </label>
            <input id="f-phone" name="phone" type="tel" required autocomplete="tel" placeholder="07700 000 000" class="field" aria-required="true"/>
          </div>
          <div>
            <label for="f-email" class="block font-semibold mb-2 uppercase tracking-wider" style="font-size:12px;color:#9a9a9a;">Email</label>
            <input id="f-email" name="email" type="email" autocomplete="email" placeholder="you@business.co.uk" class="field"/>
          </div>
        </div>
        <div>
          <label for="f-service" class="block font-semibold mb-2 uppercase tracking-wider" style="font-size:12px;color:#9a9a9a;">Service Required</label>
          <select id="f-service" name="service" class="field">
            <option value="">Select a service…</option>
            <option>Grease Extract Cleaning (NOS BSEHV11 / BESA TR19)</option>
            <option>General Ventilation Cleaning (BESA TR19)</option>
            <option>Tumble Dryer Extraction Cleaning</option>
            <option>Pizza Flue Sweeping</option>
            <option>Access Door Supply &amp; Installation</option>
            <option>Extractor Fan Replacement</option>
            <option>Canopy Hood System Installation</option>
            <option>Filtered Mechanical Supply Air System</option>
            <option>Ductwork Alterations</option>
            <option>Filter Supply</option>
            <option>Not sure &#8212; need advice</option>
          </select>
        </div>
        <div>
          <label for="f-message" class="block font-semibold mb-2 uppercase tracking-wider" style="font-size:12px;color:#9a9a9a;">Additional Details</label>
          <textarea id="f-message" name="message" rows="3" placeholder="e.g. Pub kitchen, last cleaned 12 months ago, 3 canopies…" class="field" style="resize:none;min-height:96px;"></textarea>
        </div>
        <button id="submit-btn" type="submit" class="btn-teal w-full font-bold py-4 px-6 rounded-xl min-h-tap" style="font-size:1rem;background:#00b4a2;color:#0c0c0c;">
          Send My Quotation Request
        </button>
        <div id="form-status" role="status" aria-live="polite" aria-atomic="true" class="text-center text-sm" style="min-height:1.4rem;color:<?php echo 'sent' === $conair_quote_result ? '#00b4a2' : ( 'error' === $conair_quote_result ? '#ff6b6b' : '#9a9a9a' ); ?>;"><?php
			if ( 'sent' === $conair_quote_result ) {
				esc_html_e( 'Your quotation request has been sent. We will respond within 24 hours.', 'conair-theme' );
			} elseif ( 'error' === $conair_quote_result ) {
				esc_html_e( 'Sorry, something went wrong sending your request — please call us instead.', 'conair-theme' );
			}
		?></div>
      </form>
      <p class="text-sm mt-5 text-center" style="color:#9a9a9a;">
        We respond within 24 hours — or call us directly:
        <a href="tel:+441934528450" class="font-semibold underline-offset-2 contact-link" style="color:#00b4a2;">01934 528 450</a>
        <span style="margin-left:8px;color:#9a9a9a;">or mobile <a href="tel:+447891240743" style="color:#00b4a2;" class="font-semibold contact-link">07891 240 743</a></span>
      </p>
    </div>
  </div>
</section>
<!-- /wp:html -->
