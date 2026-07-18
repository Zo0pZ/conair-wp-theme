<?php
/**
 * Title: Testimonials — Customer Reviews
 * Slug: conair-theme/testimonials
 * Categories: conair-sections, conair-cards
 * Description: Customer review cards with star ratings, quotes, and reviewer details, pulled from the Testimonials post type (Testimonials in the wp-admin sidebar) so the client can add, edit, reorder, or remove reviews without touching code. Displays as a horizontally scrolling carousel with chevron controls (assets/js/conair-theme.js) and native touch/swipe support.
 */

$conair_testimonials_query = new WP_Query(
	[
		'post_type'      => 'testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => 20, // Carousel scrolls, so this is just a sanity cap, not a "how many fit" limit.
		'orderby'        => [ 'menu_order' => 'ASC', 'date' => 'DESC' ],
		'no_found_rows'  => true,
	]
);

// Nothing to show yet (e.g. a fresh install before seeding runs) — skip the
// whole section rather than render an empty grid.
if ( ! $conair_testimonials_query->have_posts() ) {
	return '';
}
?>
<!-- wp:html -->
<section class="py-16 sm:py-24" style="background:#111111;border-top:1px solid #242424;">
  <div class="max-w-6xl mx-auto px-4 sm:px-6">
    <div class="text-center max-w-xl mx-auto mb-10 sm:mb-14 reveal">
      <div class="badge mb-4 mx-auto" style="display:inline-flex;">Testimonials</div>
      <h2 class="font-black text-white leading-tight" style="font-size:clamp(1.8rem,3vw,2.4rem);">Trusted Across Bristol &amp; Somerset</h2>
    </div>
    <div class="testimonial-carousel">
      <button type="button" id="testimonial-prev" class="testimonial-nav-btn testimonial-nav-prev" aria-label="Previous testimonials" aria-controls="testimonial-track">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15.75 19.5 8.25 12l7.5-7.5"/></svg>
      </button>
      <button type="button" id="testimonial-next" class="testimonial-nav-btn testimonial-nav-next" aria-label="Next testimonials" aria-controls="testimonial-track">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m8.25 4.5 7.5 7.5-7.5 7.5"/></svg>
      </button>

      <div class="testimonial-track" id="testimonial-track" tabindex="0" aria-label="Customer testimonials — scroll or use the previous and next buttons to see more">
<?php
while ( $conair_testimonials_query->have_posts() ) :
	$conair_testimonials_query->the_post();

	$rating = (int) get_post_meta( get_the_ID(), '_testimonial_rating', true );
	if ( $rating < 1 || $rating > 5 ) {
		$rating = 5;
	}

	$role     = get_post_meta( get_the_ID(), '_testimonial_role', true );
	$name     = get_the_title();
	$initials = conair_testimonial_initials( $name );
	$quote    = trim( wp_strip_all_tags( apply_filters( 'the_content', get_the_content() ) ) );
	?>
        <blockquote class="glow-card testimonial-card rounded-2xl p-7 reveal" style="background:#141414;border:1px solid #2e2e2e;">
          <p class="sr-only"><?php echo esc_html( $rating ); ?> out of 5 stars</p>
          <div class="flex gap-0.5 mb-4" aria-hidden="true"><?php echo conair_render_star_rating( $rating ); ?></div>
          <p style="font-size:15px;line-height:1.7;color:#9ca3af;" class="italic mb-5">&ldquo;<?php echo esc_html( $quote ); ?>&rdquo;</p>
          <footer class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0" style="background:rgba(0,180,162,0.15);color:#00b4a2;" aria-hidden="true"><?php echo esc_html( $initials ); ?></div>
            <div>
              <p class="font-bold text-white text-sm"><?php echo esc_html( $name ); ?></p>
              <?php if ( $role ) : ?><p class="text-sm" style="color:#9a9a9a;"><?php echo esc_html( $role ); ?></p><?php endif; ?>
            </div>
          </footer>
        </blockquote>
<?php endwhile; wp_reset_postdata(); ?>
      </div>
    </div>
  </div>
</section>
<!-- /wp:html -->
<?php
