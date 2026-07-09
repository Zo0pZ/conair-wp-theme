<?php
/**
 * Title: Testimonials — Customer Reviews
 * Slug: conair-theme/testimonials
 * Categories: conair-sections, conair-cards
 * Description: Three customer review cards with star ratings, quotes, and reviewer details.
 */

$star_svg = '<svg class="w-4 h-4" style="color:#00b4a2;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';

$five_stars = str_repeat( $star_svg, 5 );
$four_stars = str_repeat( $star_svg, 4 );
?>
<!-- wp:html -->
<section class="py-16 sm:py-24" style="background:#111111;border-top:1px solid #242424;">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-xl mx-auto mb-10 sm:mb-14 reveal">
      <div class="badge mb-4 mx-auto" style="display:inline-flex;">Testimonials</div>
      <h2 class="font-black text-white leading-tight" style="font-size:clamp(1.8rem,3vw,2.4rem);">Trusted Across Bristol &amp; Somerset</h2>
    </div>
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">

      <blockquote class="glow-card rounded-2xl p-7 reveal" style="background:#141414;border:1px solid #2e2e2e;">
        <p class="sr-only">5 out of 5 stars</p>
        <div class="flex gap-0.5 mb-4" aria-hidden="true"><?php echo $five_stars; ?></div>
        <p style="font-size:15px;line-height:1.7;color:#9ca3af;" class="italic mb-5">&ldquo;ConAir saved us from what could have been a very costly insurance claim. The TR19 certificate was accepted immediately. Absolutely professional.&rdquo;</p>
        <footer class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:rgba(0,180,162,0.15);color:#00b4a2;" aria-hidden="true">JH</div>
          <div>
            <p class="font-bold text-white text-sm">James H.</p>
            <p class="text-sm" style="color:#9a9a9a;">Head Chef, Weston-super-Mare</p>
          </div>
        </footer>
      </blockquote>

      <blockquote class="glow-card rounded-2xl p-7 reveal" style="background:#141414;border:1px solid #2e2e2e;">
        <p class="sr-only">5 out of 5 stars</p>
        <div class="flex gap-0.5 mb-4" aria-hidden="true"><?php echo $five_stars; ?></div>
        <p style="font-size:15px;line-height:1.7;color:#9ca3af;" class="italic mb-5">&ldquo;We had the full ductwork system cleaned and the difference was remarkable. They were professional throughout, minimal disruption, and the report photos were extremely thorough.&rdquo;</p>
        <footer class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:rgba(0,180,162,0.15);color:#00b4a2;" aria-hidden="true">SR</div>
          <div>
            <p class="font-bold text-white text-sm">Sarah R.</p>
            <p class="text-sm" style="color:#9a9a9a;">Catering Manager, Bridgwater</p>
          </div>
        </footer>
      </blockquote>

      <blockquote class="glow-card rounded-2xl p-7 reveal" style="background:#141414;border:1px solid #2e2e2e;">
        <p class="sr-only">4 out of 5 stars</p>
        <div class="flex gap-0.5 mb-4" aria-hidden="true"><?php echo $four_stars; ?></div>
        <p style="font-size:15px;line-height:1.7;color:#9ca3af;" class="italic mb-5">&ldquo;Highly recommend to any commercial kitchen in Somerset. Booked on Monday, they were here Wednesday. Quick, tidy, the canopy looks brand new.&rdquo;</p>
        <footer class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:rgba(0,180,162,0.15);color:#00b4a2;" aria-hidden="true">MP</div>
          <div>
            <p class="font-bold text-white text-sm">Mark P.</p>
            <p class="text-sm" style="color:#9a9a9a;">Restaurant Owner, Taunton</p>
          </div>
        </footer>
      </blockquote>

    </div>
  </div>
</section>
<!-- /wp:html -->
