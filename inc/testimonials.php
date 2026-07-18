<?php
/**
 * ConAir Extract Solutions — Testimonials
 *
 * Registers a "Testimonial" custom post type so the homepage testimonials
 * section (patterns/testimonials.php) can be edited from wp-admin like a
 * normal post list — no code changes needed to add, edit, reorder, or
 * remove a review.
 *
 * @package conair-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// ═══════════════════════════════════════════════════════════════
//  1. CUSTOM POST TYPE
// ═══════════════════════════════════════════════════════════════

function conair_register_testimonial_cpt(): void {
	register_post_type(
		'testimonial',
		[
			'labels'             => [
				'name'               => __( 'Testimonials', 'conair-theme' ),
				'singular_name'      => __( 'Testimonial', 'conair-theme' ),
				'add_new'            => __( 'Add New', 'conair-theme' ),
				'add_new_item'       => __( 'Add New Testimonial', 'conair-theme' ),
				'edit_item'          => __( 'Edit Testimonial', 'conair-theme' ),
				'new_item'           => __( 'New Testimonial', 'conair-theme' ),
				'view_item'          => __( 'View Testimonial', 'conair-theme' ),
				'all_items'          => __( 'Testimonials', 'conair-theme' ),
				'search_items'       => __( 'Search Testimonials', 'conair-theme' ),
				'not_found'          => __( 'No testimonials found.', 'conair-theme' ),
				'not_found_in_trash' => __( 'No testimonials found in Trash.', 'conair-theme' ),
			],
			'description'        => __( 'Customer reviews shown in the homepage Testimonials section.', 'conair-theme' ),
			'public'             => false,
			'publicly_queryable' => false,
			'exclude_from_search'=> true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_rest'       => true,
			'menu_icon'          => 'dashicons-testimonial',
			'menu_position'      => 25,
			'supports'           => [ 'title', 'editor', 'page-attributes' ],
			'has_archive'        => false,
			'rewrite'            => false,
			'capability_type'    => 'post',
		]
	);
}
add_action( 'init', 'conair_register_testimonial_cpt' );

/**
 * Title = reviewer name (e.g. "James H."). Editor content = the quote.
 * Everything else (role/location, star rating) needs its own field —
 * handled by the meta box below.
 */
function conair_testimonial_title_placeholder( string $title ): string {
	global $post;
	if ( $post && 'testimonial' === $post->post_type ) {
		return __( 'Reviewer name, e.g. James H.', 'conair-theme' );
	}
	return $title;
}
add_filter( 'enter_title_here', 'conair_testimonial_title_placeholder' );

// ═══════════════════════════════════════════════════════════════
//  2. META BOX — role/location + star rating
// ═══════════════════════════════════════════════════════════════

function conair_testimonial_meta_box(): void {
	add_meta_box(
		'conair_testimonial_details',
		__( 'Reviewer Details', 'conair-theme' ),
		'conair_render_testimonial_meta_box',
		'testimonial',
		'side',
		'default'
	);
}
add_action( 'add_meta_boxes', 'conair_testimonial_meta_box' );

function conair_render_testimonial_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'conair_save_testimonial', 'conair_testimonial_nonce' );

	$role   = get_post_meta( $post->ID, '_testimonial_role', true );
	$rating = (int) get_post_meta( $post->ID, '_testimonial_rating', true );
	if ( $rating < 1 || $rating > 5 ) {
		$rating = 5;
	}
	?>
	<p>
		<label for="conair_testimonial_role"><strong><?php esc_html_e( 'Role / Location', 'conair-theme' ); ?></strong></label><br>
		<input
			type="text"
			id="conair_testimonial_role"
			name="conair_testimonial_role"
			value="<?php echo esc_attr( $role ); ?>"
			class="widefat"
			placeholder="<?php esc_attr_e( 'e.g. Head Chef, Weston-super-Mare', 'conair-theme' ); ?>"
		>
	</p>
	<p>
		<label for="conair_testimonial_rating"><strong><?php esc_html_e( 'Star Rating', 'conair-theme' ); ?></strong></label><br>
		<select id="conair_testimonial_rating" name="conair_testimonial_rating" class="widefat">
			<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
				<option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $rating, $i ); ?>>
					<?php echo esc_html( sprintf( _n( '%d star', '%d stars', $i, 'conair-theme' ), $i ) ); ?>
				</option>
			<?php endfor; ?>
		</select>
	</p>
	<p class="description">
		<?php esc_html_e( 'Title = reviewer name. Content below = the review quote. Use the Order field (below) to control display order.', 'conair-theme' ); ?>
	</p>
	<?php
}

function conair_save_testimonial_meta( int $post_id ): void {
	if ( ! isset( $_POST['conair_testimonial_nonce'] )
		|| ! wp_verify_nonce( $_POST['conair_testimonial_nonce'], 'conair_save_testimonial' ) ) {
		return;
	}

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	if ( isset( $_POST['conair_testimonial_role'] ) ) {
		update_post_meta(
			$post_id,
			'_testimonial_role',
			sanitize_text_field( wp_unslash( $_POST['conair_testimonial_role'] ) )
		);
	}

	if ( isset( $_POST['conair_testimonial_rating'] ) ) {
		$rating = max( 1, min( 5, (int) $_POST['conair_testimonial_rating'] ) );
		update_post_meta( $post_id, '_testimonial_rating', $rating );
	}
}
add_action( 'save_post_testimonial', 'conair_save_testimonial_meta' );

// ═══════════════════════════════════════════════════════════════
//  3. DISPLAY HELPERS — shared with patterns/testimonials.php
// ═══════════════════════════════════════════════════════════════

/**
 * Render N of the theme's teal star icon (1–5). Used for both the visible
 * star row and to build the visually-hidden "N out of 5 stars" text.
 */
function conair_render_star_rating( int $rating ): string {
	static $star_svg = '<svg class="w-4 h-4" style="color:#00b4a2;" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>';

	$rating = max( 1, min( 5, $rating ) );

	return str_repeat( $star_svg, $rating );
}

/**
 * Up to two initials for the reviewer avatar circle, e.g. "James H." → "JH".
 */
function conair_testimonial_initials( string $name ): string {
	$words    = preg_split( '/\s+/', trim( $name ), -1, PREG_SPLIT_NO_EMPTY );
	$initials = '';

	foreach ( array_slice( $words, 0, 2 ) as $word ) {
		$initials .= mb_strtoupper( mb_substr( $word, 0, 1 ) );
	}

	return '' !== $initials ? $initials : '?';
}

// ═══════════════════════════════════════════════════════════════
//  4. SEED CONTENT — the 3 reviews the homepage already shipped with
// ═══════════════════════════════════════════════════════════════

/**
 * Creates the original 3 testimonials as CPT posts the first time this
 * runs on a site, so switching the pattern over to the CPT doesn't blank
 * the homepage. Skips entirely if any testimonial already exists (covers
 * both "already seeded" and "client has started managing their own"),
 * matching the safe-to-run-repeatedly convention in inc/seed-content.php.
 */
function conair_seed_testimonials(): void {
	$existing = get_posts(
		[
			'post_type'      => 'testimonial',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		]
	);

	if ( ! empty( $existing ) ) {
		return;
	}

	$testimonials = [
		[
			'name'   => 'James H.',
			'role'   => 'Head Chef, Weston-super-Mare',
			'quote'  => 'ConAir saved us from what could have been a very costly insurance claim. The TR19 certificate was accepted immediately. Absolutely professional.',
			'rating' => 5,
		],
		[
			'name'   => 'Sarah R.',
			'role'   => 'Catering Manager, Bridgwater',
			'quote'  => 'We had the full ductwork system cleaned and the difference was remarkable. They were professional throughout, minimal disruption, and the report photos were extremely thorough.',
			'rating' => 5,
		],
		[
			'name'   => 'Mark P.',
			'role'   => 'Restaurant Owner, Taunton',
			'quote'  => 'Highly recommend to any commercial kitchen in Somerset. Booked on Monday, they were here Wednesday. Quick, tidy, the canopy looks brand new.',
			'rating' => 4,
		],
	];

	foreach ( $testimonials as $order => $t ) {
		$post_id = wp_insert_post(
			[
				'post_title'   => $t['name'],
				'post_content' => $t['quote'],
				'post_status'  => 'publish',
				'post_type'    => 'testimonial',
				'menu_order'   => $order,
			],
			true
		);

		if ( is_wp_error( $post_id ) ) {
			continue;
		}

		update_post_meta( $post_id, '_testimonial_role', $t['role'] );
		update_post_meta( $post_id, '_testimonial_rating', $t['rating'] );
	}
}

/**
 * Run once automatically when the theme is activated.
 */
add_action( 'after_switch_theme', 'conair_seed_testimonials' );

/**
 * Also run once on any admin page load if it's never run before — covers
 * the case where this code lands via `git pull` on a site where the theme
 * is already active (after_switch_theme won't fire again for an existing
 * site). Uses its own option flag, independent of inc/seed-content.php's
 * page-seeding flag, so it still runs on sites that were seeded before
 * testimonials became a CPT.
 */
function conair_maybe_seed_testimonials(): void {
	if ( get_option( 'conair_testimonials_seeded' ) ) {
		return;
	}
	conair_seed_testimonials();
	update_option( 'conair_testimonials_seeded', true );
}
add_action( 'admin_init', 'conair_maybe_seed_testimonials' );
