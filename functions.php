<?php
/**
 * ConAir Extract Solutions — Theme Functions
 *
 * @package conair-theme
 */

define( 'CONAIR_VERSION', '1.0.0' );

require_once get_template_directory() . '/inc/seed-content.php';

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

	// Tailwind CSS via CDN.
	// NOTE: For production, replace with a compiled stylesheet generated via
	// `npx tailwindcss -i ./src/input.css -o ./assets/css/tailwind.css --minify`
	// and enqueue that file instead. The CDN approach is fine for development.
	wp_enqueue_script(
		'tailwindcss-cdn',
		'https://cdn.tailwindcss.com',
		[],
		null,
		false // must load in <head> so Tailwind can scan DOM before first paint
	);

	// Tailwind config — matches the original inline config from the static site
	wp_add_inline_script(
		'tailwindcss-cdn',
		"tailwind.config = {
			theme: {
				extend: {
					colors: {
						ink: {
							DEFAULT: '#0c0c0c',
							900: '#0c0c0c',
							800: '#141414',
							700: '#1c1c1c',
							600: '#242424'
						},
						teal: {
							DEFAULT: '#00b4a2',
							light: '#00ccb8',
							dark: '#009688'
						}
					},
					fontFamily: {
						sans: ['Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']
					},
					minHeight: { tap: '44px' },
					minWidth:  { tap: '44px' }
				}
			}
		};",
		'after'
	);

	// Custom theme stylesheet (all hand-crafted CSS from the original <style> block)
	wp_enqueue_style(
		'conair-theme-style',
		get_template_directory_uri() . '/assets/css/conair-theme.css',
		[ 'conair-fonts' ],
		CONAIR_VERSION
	);

	// Theme JavaScript — mobile menu, scroll reveal, before/after slider, quote form
	wp_enqueue_script(
		'conair-theme-js',
		get_template_directory_uri() . '/assets/js/conair-theme.js',
		[],
		CONAIR_VERSION,
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
		'conair-theme-style-editor',
		get_template_directory_uri() . '/assets/css/conair-theme.css',
		[],
		CONAIR_VERSION
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
