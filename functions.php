<?php
/**
 * ConAir Extract Solutions — Theme Functions
 *
 * @package conair-theme
 */

define( 'CONAIR_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/seed-content.php';
require_once get_template_directory() . '/inc/customizer.php';

/**
 * Cache-busting version for a theme asset — the file's own mtime, so every
 * edit forces browsers to fetch the new copy instead of reusing whatever
 * they cached under the old (otherwise-static) CONAIR_VERSION query string.
 */
function conair_asset_version( string $relative_path ): string {
	$path = get_template_directory() . $relative_path;
	$mtime = file_exists( $path ) ? filemtime( $path ) : false;
	return $mtime ? (string) $mtime : CONAIR_VERSION;
}

// ═══════════════════════════════════════════════════════════════
//  1. THEME SETUP
// ═══════════════════════════════════════════════════════════════

function conair_setup(): void {
	load_theme_textdomain( 'conair-theme', get_template_directory() . '/languages' );

	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'block-template-parts' );

	add_theme_support(
		'html5',
		[ 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ]
	);

	// Colour palette (mirrors theme.json — kept in sync so classic widgets work too)
	add_theme_support(
		'editor-color-palette',
		[
			[ 'name' => __( 'Teal',       'conair-theme' ), 'slug' => 'teal',       'color' => '#00b4a2' ],
			[ 'name' => __( 'Teal Light', 'conair-theme' ), 'slug' => 'teal-light', 'color' => '#00ccb8' ],
			[ 'name' => __( 'Teal Dark',  'conair-theme' ), 'slug' => 'teal-dark',  'color' => '#009688' ],
			[ 'name' => __( 'Ink',        'conair-theme' ), 'slug' => 'ink',        'color' => '#0c0c0c' ],
			[ 'name' => __( 'Ink 800',    'conair-theme' ), 'slug' => 'ink-800',    'color' => '#141414' ],
			[ 'name' => __( 'Ink 700',    'conair-theme' ), 'slug' => 'ink-700',    'color' => '#1c1c1c' ],
			[ 'name' => __( 'Ink 600',    'conair-theme' ), 'slug' => 'ink-600',    'color' => '#242424' ],
			[ 'name' => __( 'White',      'conair-theme' ), 'slug' => 'white',      'color' => '#ffffff' ],
			[ 'name' => __( 'Muted',      'conair-theme' ), 'slug' => 'muted',      'color' => '#9a9a9a' ],
			[ 'name' => __( 'Subtle',     'conair-theme' ), 'slug' => 'subtle',     'color' => '#d4d4d4' ],
			[ 'name' => __( 'Surface',    'conair-theme' ), 'slug' => 'surface',    'color' => '#111111' ],
		]
	);

	// Navigation menus
	register_nav_menus(
		[
			'primary' => __( 'Primary Navigation', 'conair-theme' ),
			'footer'  => __( 'Footer Navigation',  'conair-theme' ),
		]
	);
}
add_action( 'after_setup_theme', 'conair_setup' );

// ═══════════════════════════════════════════════════════════════
//  2. ENQUEUE SCRIPTS & STYLES
// ═══════════════════════════════════════════════════════════════

function conair_enqueue_assets(): void {
	// Google Fonts — Inter (variable weight: 400–900, including italic 700)
	wp_enqueue_style(
		'conair-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap',
		[],
		null
	);

	// Compiled Tailwind utilities (built via `npm run build:css` — see package.json
	// and tailwind.config.js). Preflight is disabled in tailwind.config.js since this
	// is a WordPress theme, not a standalone page — WP core's own block-library CSS
	// and conair-theme.css already provide the base styles, and a global reset here
	// previously overrode WP core defaults (e.g. the nav block's default hidden state).
	wp_enqueue_style(
		'conair-tailwind',
		get_template_directory_uri() . '/assets/css/tailwind.css',
		[ 'conair-fonts' ],
		conair_asset_version( '/assets/css/tailwind.css' )
	);

	// Custom theme stylesheet (all hand-crafted CSS from the original <style> block)
	wp_enqueue_style(
		'conair-theme-style',
		get_template_directory_uri() . '/assets/css/conair-theme.css',
		[ 'conair-tailwind' ],
		conair_asset_version( '/assets/css/conair-theme.css' )
	);

	// Theme JavaScript — mobile menu, scroll reveal, before/after slider, quote form
	wp_enqueue_script(
		'conair-theme-js',
		get_template_directory_uri() . '/assets/js/conair-theme.js',
		[],
		conair_asset_version( '/assets/js/conair-theme.js' ),
		true // load in footer
	);
}
add_action( 'wp_enqueue_scripts', 'conair_enqueue_assets' );

// ═══════════════════════════════════════════════════════════════
//  3. BLOCK EDITOR — enqueue same fonts/styles in editor iframe
// ═══════════════════════════════════════════════════════════════

function conair_enqueue_editor_assets(): void {
	wp_enqueue_style(
		'conair-fonts-editor',
		'https://fonts.googleapis.com/css2?family=Inter:ital,wght@0,400;0,500;0,600;0,700;0,800;0,900;1,700&display=swap',
		[],
		null
	);

	wp_enqueue_style(
		'conair-tailwind-editor',
		get_template_directory_uri() . '/assets/css/tailwind.css',
		[],
		conair_asset_version( '/assets/css/tailwind.css' )
	);

	wp_enqueue_style(
		'conair-theme-style-editor',
		get_template_directory_uri() . '/assets/css/conair-theme.css',
		[ 'conair-tailwind-editor' ],
		conair_asset_version( '/assets/css/conair-theme.css' )
	);
}
add_action( 'enqueue_block_editor_assets', 'conair_enqueue_editor_assets' );

// ═══════════════════════════════════════════════════════════════
//  4. BLOCK PATTERN CATEGORIES
// ═══════════════════════════════════════════════════════════════

function conair_register_pattern_categories(): void {
	register_block_pattern_category(
		'conair-sections',
		[ 'label' => __( 'ConAir — Page Sections',  'conair-theme' ) ]
	);
	register_block_pattern_category(
		'conair-cards',
		[ 'label' => __( 'ConAir — Cards & Grids',  'conair-theme' ) ]
	);
	register_block_pattern_category(
		'conair-cta',
		[ 'label' => __( 'ConAir — Calls to Action', 'conair-theme' ) ]
	);
	register_block_pattern_category(
		'conair-interactive',
		[ 'label' => __( 'ConAir — Interactive',    'conair-theme' ) ]
	);
}
add_action( 'init', 'conair_register_pattern_categories' );

// ═══════════════════════════════════════════════════════════════
//  5. CUSTOM POST TYPES  (optional — add custom CPTs here later)
// ═══════════════════════════════════════════════════════════════

// Example placeholder — uncomment and expand when needed:
// function conair_register_cpts(): void { ... }
// add_action( 'init', 'conair_register_cpts' );

// ═══════════════════════════════════════════════════════════════
//  6. SCHEMA / SEO HELPERS
// ═══════════════════════════════════════════════════════════════

/**
 * Output LocalBusiness JSON-LD schema in <head>.
 * Values can be filtered to allow child-theme or plugin overrides.
 */
function conair_output_schema(): void {
	$schema = apply_filters( 'conair_local_business_schema', [
		'@context'    => 'https://schema.org',
		'@type'       => 'LocalBusiness',
		'name'        => 'ConAir Extract Solutions Limited',
		'description' => 'Commercial kitchen extract and ventilation cleaning in Weston-super-Mare, Somerset.',
		'url'         => home_url( '/' ),
		'telephone'   => '+441934528450',
		'priceRange'  => '££',
		'address'     => [
			'@type'           => 'PostalAddress',
			'streetAddress'   => '2 Laurel House, 1 Station Road',
			'addressLocality' => 'Worle',
			'addressRegion'   => 'Somerset',
			'postalCode'      => 'BS22 6AR',
			'addressCountry'  => 'GB',
		],
		'geo'         => [
			'@type'     => 'GeoCoordinates',
			'latitude'  => '51.16540',
			'longitude' => '-2.95290',
		],
		'openingHoursSpecification' => [
			'@type'       => 'OpeningHoursSpecification',
			'dayOfWeek'   => [ 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday' ],
			'opens'       => '07:00',
			'closes'      => '18:00',
		],
		'aggregateRating' => [
			'@type'       => 'AggregateRating',
			'ratingValue' => '4.8',
			'reviewCount' => '12',
		],
		'sameAs' => [
			'https://www.facebook.com/ConAirExtractSolutions',
			'https://www.linkedin.com/company/conair-extract-solutions',
		],
	] );

	echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'conair_output_schema' );

/**
 * Output Service + BreadcrumbList JSON-LD for pages using the "Service Page"
 * template. Needs the current post's title/excerpt/permalink, which a .html
 * block template can't produce (no PHP execution there) — this is why it's
 * a functions.php hook instead of markup in templates/page-service.html.
 */
function conair_output_service_schema(): void {
	if ( ! is_page_template( 'page-service' ) ) {
		return;
	}

	$post_id = get_queried_object_id();

	$service_schema = [
		'@context'    => 'https://schema.org',
		'@type'       => 'Service',
		'serviceType' => get_the_title( $post_id ),
		'description' => get_the_excerpt( $post_id ),
		'provider'    => [
			'@type'     => 'LocalBusiness',
			'name'      => 'ConAir Extract Solutions Limited',
			'telephone' => '+441934528450',
			'url'       => home_url( '/' ),
			'address'   => [
				'@type'           => 'PostalAddress',
				'streetAddress'   => '2 Laurel House, 1 Station Road',
				'addressLocality' => 'Weston-super-Mare',
				'addressRegion'   => 'Somerset',
				'postalCode'      => 'BS22 6AR',
				'addressCountry'  => 'GB',
			],
		],
		'areaServed'  => 'Bristol and Somerset, UK',
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $service_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";

	$breadcrumb_schema = [
		'@context'        => 'https://schema.org',
		'@type'           => 'BreadcrumbList',
		'itemListElement' => [
			[
				'@type'    => 'ListItem',
				'position' => 1,
				'name'     => 'Home',
				'item'     => home_url( '/' ),
			],
			[
				'@type'    => 'ListItem',
				'position' => 2,
				'name'     => 'Services',
				'item'     => home_url( '/services/' ),
			],
			[
				'@type'    => 'ListItem',
				'position' => 3,
				'name'     => get_the_title( $post_id ),
				'item'     => get_permalink( $post_id ),
			],
		],
	];

	echo '<script type="application/ld+json">' . wp_json_encode( $breadcrumb_schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) . '</script>' . "\n";
}
add_action( 'wp_head', 'conair_output_service_schema' );

// ═══════════════════════════════════════════════════════════════
//  7. SERVICE PAGE HERO — optional per-page featured image
// ═══════════════════════════════════════════════════════════════

/**
 * Add a body class when the current service page has a Featured Image set,
 * so conair-theme.css can opt that page's hero into the homepage's photo
 * background treatment instead of the plain dark hero. Off by default —
 * a service page with no Featured Image keeps today's look untouched.
 */
function conair_service_hero_body_class( array $classes ): array {
	if ( is_page_template( 'page-service' ) && has_post_thumbnail( get_queried_object_id() ) ) {
		$classes[] = 'has-service-hero-image';
	}
	return $classes;
}
add_filter( 'body_class', 'conair_service_hero_body_class' );

/**
 * Expose the current service page's Featured Image as a CSS custom
 * property — the same technique inc/customizer.php uses for the homepage
 * hero (see that file's comment for why the CSS rule must read a var
 * rather than have the image declared directly on the rule's selector).
 */
function conair_output_service_hero_image(): void {
	if ( ! is_page_template( 'page-service' ) ) {
		return;
	}

	$image_url = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
	if ( ! $image_url ) {
		return;
	}

	printf(
		'<style>:root{--service-hero-image:url("%s");}</style>',
		esc_url_raw( $image_url )
	);
}
add_action( 'wp_head', 'conair_output_service_hero_image' );

// ═══════════════════════════════════════════════════════════════
//  8. SERVICE PAGE EXCERPT — strip the duplicate hidden hero out
// ═══════════════════════════════════════════════════════════════

/**
 * Some service pages still carry legacy post_content from before
 * page-service.html grew its own hero — a duplicate, CSS-hidden copy of
 * the badge/title/subtitle/CTA buttons at the very top of the body (see
 * conair-theme.css section 14, the ".conair-*" classes). With no manual
 * excerpt set, WordPress's default excerpt is just strip_tags() +
 * word-trim of that raw post_content, so it picks up the hidden
 * duplicate's text verbatim ("OUR SERVICES Commercial Grease Extract
 * Cleaning ... Contact Us Call 01934 528 450 ...") instead of a real
 * description. That duplicate's own subtitle (.conair-page-subtitle) is
 * exactly the short description these pages need, so pull it out
 * directly when present instead of re-trimming the whole body. Falls
 * back to a cleaned generic trim (hero section stripped first) for any
 * page where that class isn't found. A manually set excerpt in the
 * editor always wins — this only fires when post_excerpt is empty.
 */
function conair_fix_service_page_excerpt( string $excerpt, $post ): string {
	if ( ! is_page_template( 'page-service' ) || '' !== $post->post_excerpt ) {
		return $excerpt;
	}

	$content = apply_filters( 'the_content', get_the_content( '', false, $post ) );

	if ( preg_match( '#<p class="conair-page-subtitle">(.*?)</p>#s', $content, $matches ) ) {
		return trim( wp_strip_all_tags( $matches[1] ) );
	}

	$content = preg_replace( '#<section class="conair-hero-section".*?</section>#s', '', $content, 1 );
	if ( null === $content || '' === trim( wp_strip_all_tags( $content ) ) ) {
		return $excerpt;
	}

	$excerpt_length = apply_filters( 'excerpt_length', 55 );
	$excerpt_more   = apply_filters( 'excerpt_more', ' [&hellip;]' );

	return wp_trim_words( wp_strip_all_tags( $content ), $excerpt_length, $excerpt_more );
}
add_filter( 'get_the_excerpt', 'conair_fix_service_page_excerpt', 20, 2 );

// ═══════════════════════════════════════════════════════════════
//  9. AREA PAGE HERO — optional per-page featured image
// ═══════════════════════════════════════════════════════════════

/**
 * Same mechanism as conair_service_hero_body_class() above, scoped to the
 * page-area template instead — kept as its own pair of functions (rather
 * than generalised across both templates) so an area page's photo can
 * never leak onto a service page or vice versa.
 */
function conair_area_hero_body_class( array $classes ): array {
	if ( is_page_template( 'page-area' ) && has_post_thumbnail( get_queried_object_id() ) ) {
		$classes[] = 'has-area-hero-image';
	}
	return $classes;
}
add_filter( 'body_class', 'conair_area_hero_body_class' );

/**
 * Same mechanism as conair_output_service_hero_image() above, scoped to
 * the page-area template instead.
 */
function conair_output_area_hero_image(): void {
	if ( ! is_page_template( 'page-area' ) ) {
		return;
	}

	$image_url = get_the_post_thumbnail_url( get_queried_object_id(), 'full' );
	if ( ! $image_url ) {
		return;
	}

	printf(
		'<style>:root{--area-hero-image:url("%s");}</style>',
		esc_url_raw( $image_url )
	);
}
add_action( 'wp_head', 'conair_output_area_hero_image' );
