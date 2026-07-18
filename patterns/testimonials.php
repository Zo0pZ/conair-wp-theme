<?php
/**
 * Title: Testimonials — Customer Reviews
 * Slug: conair-theme/testimonials
 * Categories: conair-sections, conair-cards
 * Description: Customer review cards with star ratings, quotes, and reviewer details, pulled from the Testimonials post type (Testimonials in the wp-admin sidebar) so the client can add, edit, reorder, or remove reviews without touching code.
 */

$conair_testimonials_query = new WP_Query(
	[
		'post_type'      => 'testimonial',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
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
    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6">
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
      <blockquote class="glow-card rounded-2xl p-7 reveal" style="background:#141414;border:1px solid #2e2e2e;">
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
</section>
<!-- /wp:html -->
<?php
