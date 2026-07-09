<?php
/**
 * ConAir Extract Solutions — Content Seeder
 *
 * Creates the pages, page templates, and front-page setting that the theme's
 * templates and patterns already assume exist (breadcrumb links to /services/
 * and /areas/, the site-editor Navigation blocks in parts/header.html and
 * parts/footer.html, and the service-card links in patterns/services-grid.php).
 *
 * Safe to run more than once: existing pages (matched by slug) are never
 * overwritten, so any edits made afterwards in wp-admin are preserved.
 *
 * Runs automatically the moment the theme is activated. Can also be re-run
 * manually via WP-CLI:  wp eval-file inc/seed-content.php
 *
 * @package conair-theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Create a page if a page with this slug doesn't already exist.
 * Never overwrites an existing page's content — safe to run repeatedly.
 */
function conair_seed_page( array $args ): int {
	$existing = get_page_by_path( $args['slug'] );

	if ( $existing ) {
		// Page already exists (created here previously, or hand-made by an editor).
		// Only backfill the template assignment if it's missing — never touch content.
		if ( ! empty( $args['template'] ) && ! get_page_template_slug( $existing->ID ) ) {
			update_post_meta( $existing->ID, '_wp_page_template', $args['template'] );
		}
		return $existing->ID;
	}

	$post_id = wp_insert_post(
		[
			'post_title'   => $args['title'],
			'post_name'    => $args['slug'],
			'post_excerpt' => $args['excerpt'] ?? '',
			'post_content' => $args['content'] ?? '',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $args['parent'] ?? 0,
		],
		true
	);

	if ( is_wp_error( $post_id ) ) {
		return 0;
	}

	if ( ! empty( $args['template'] ) ) {
		update_post_meta( $post_id, '_wp_page_template', $args['template'] );
	}

	return $post_id;
}

/**
 * The 9 services shown in patterns/services-grid.php, keyed by the slug
 * already hardcoded into that pattern's "Learn more" links.
 */
function conair_service_pages(): array {
	return include __DIR__ . '/data/service-pages.php';
}

/**
 * The 4 areas shown in the header/footer Navigation blocks.
 */
function conair_area_pages(): array {
	return include __DIR__ . '/data/area-pages.php';
}

function conair_services_index_content(): string {
	$rows = '';
	foreach ( conair_service_pages() as $slug => $data ) {
		$rows .= sprintf(
			'<a href="/services/%1$s/" class="flex items-center justify-between gap-3 p-4 rounded-xl mb-3" style="background:#141414;border:1px solid #2e2e2e;color:#ffffff;text-decoration:none;">
	<span class="font-semibold" style="font-size:0.95rem;">%2$s</span>
	<span style="color:#00b4a2;" aria-hidden="true">&#8594;</span>
</a>',
			esc_attr( $slug ),
			esc_html( $data['title'] )
		);
	}

	return '<div class="max-w-3xl">' . $rows . '</div>';
}

function conair_areas_index_content(): string {
	$rows = '';
	foreach ( conair_area_pages() as $slug => $data ) {
		$rows .= sprintf(
			'<a href="/areas/%1$s/" class="flex items-center justify-between gap-3 p-4 rounded-xl mb-3" style="background:#141414;border:1px solid #2e2e2e;color:#ffffff;text-decoration:none;">
	<span class="font-semibold" style="font-size:0.95rem;">%2$s</span>
	<span style="color:#00b4a2;" aria-hidden="true">&#8594;</span>
</a>',
			esc_attr( $slug ),
			esc_html( $data['title'] )
		);
	}

	return '<div class="max-w-3xl">' . $rows . '</div>';
}

function conair_privacy_policy_content(): string {
	return '<p style="color:#9a9a9a;line-height:1.8;">This is a placeholder Privacy Policy generated when the theme was set up. It has not been reviewed by a solicitor and does not yet describe ConAir Extract Solutions Limited\'s actual data handling practices (what personal data is collected via the quote form, how long it is retained, which third parties — e.g. your form plugin, email provider, analytics — process it, and how site visitors can exercise their UK GDPR rights). <strong style="color:#ffffff;">Replace this content with a proper policy before the site goes live.</strong></p>';
}

/**
 * Seed the full page tree. Hooked to run once on theme activation, and
 * re-runnable safely (see conair_seed_page — never overwrites existing pages).
 */
function conair_seed_content(): void {
	// Home — front page.
	$home_id = conair_seed_page(
		[
			'slug'     => 'home',
			'title'    => 'Home',
			'template' => 'front-page',
		]
	);
	if ( $home_id && ! get_option( 'page_on_front' ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', $home_id );
	}

	// Services — parent + 9 children (slugs match patterns/services-grid.php).
	$services_id = conair_seed_page(
		[
			'slug'     => 'services',
			'title'    => 'Services',
			'excerpt'  => 'Specialist ventilation cleaning and compliance services for commercial kitchens across Bristol & Somerset.',
			'content'  => conair_services_index_content(),
			'template' => 'page-no-hero',
		]
	);
	foreach ( conair_service_pages() as $slug => $data ) {
		conair_seed_page(
			[
				'slug'     => $slug,
				'title'    => $data['title'],
				'excerpt'  => $data['excerpt'],
				'content'  => $data['content'],
				'template' => 'page-service',
				'parent'   => $services_id,
			]
		);
	}

	// Areas — parent + 4 children.
	$areas_id = conair_seed_page(
		[
			'slug'     => 'areas',
			'title'    => 'Service Areas',
			'excerpt'  => 'Based in Weston-super-Mare, covering commercial kitchen ventilation cleaning across Bristol & Somerset.',
			'content'  => conair_areas_index_content(),
			'template' => 'page-no-hero',
		]
	);
	foreach ( conair_area_pages() as $slug => $data ) {
		conair_seed_page(
			[
				'slug'     => $slug,
				'title'    => $data['title'],
				'excerpt'  => $data['excerpt'],
				'content'  => $data['content'],
				'template' => 'page-area',
				'parent'   => $areas_id,
			]
		);
	}

	// Privacy Policy — simple stub, needs real legal content before launch.
	conair_seed_page(
		[
			'slug'     => 'privacy-policy',
			'title'    => 'Privacy Policy',
			'content'  => conair_privacy_policy_content(),
			'template' => 'page-no-hero',
		]
	);
}

/**
 * Run once automatically when the theme is activated.
 */
function conair_seed_content_on_activation(): void {
	conair_seed_content();
}
add_action( 'after_switch_theme', 'conair_seed_content_on_activation' );

/**
 * Also run once on any admin page load if it's never run before — covers the
 * case where this code lands via `git pull` on a site where the theme is
 * already active (after_switch_theme won't fire again for an existing site).
 */
function conair_maybe_seed_content(): void {
	if ( get_option( 'conair_content_seeded' ) ) {
		return;
	}
	conair_seed_content();
	update_option( 'conair_content_seeded', true );
}
add_action( 'admin_init', 'conair_maybe_seed_content' );

/**
 * WP-CLI: `wp conair seed` — force a manual re-run (e.g. after editing the
 * source content in inc/data/*.php).
 */
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::add_command(
		'conair seed',
		function () {
			conair_seed_content();
			WP_CLI::success( 'ConAir content seeded.' );
		}
	);
}
