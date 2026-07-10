<?php
/**
 * ConAir Extract Solutions — Customizer settings
 *
 * @package conair-theme
 */

/**
 * Same placeholder used as the fallback --hero-bg-image in conair-theme.css,
 * so the site looks identical until a client picks their own photo.
 */
function conair_default_hero_image(): string {
	return 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=1600&q=80';
}

add_action(
	'customize_register',
	function ( WP_Customize_Manager $wp_customize ): void {
		$wp_customize->add_section(
			'conair_hero_section',
			array(
				'title'    => 'Hero Section',
				'priority' => 30,
			)
		);

		$wp_customize->add_setting(
			'conair_hero_image',
			array(
				'default'           => conair_default_hero_image(),
				'sanitize_callback' => 'esc_url_raw',
			)
		);

		$wp_customize->add_control(
			new WP_Customize_Image_Control(
				$wp_customize,
				'conair_hero_image',
				array(
					'label'   => 'Hero Background Image',
					'section' => 'conair_hero_section',
				)
			)
		);
	}
);

add_action(
	'wp_head',
	function (): void {
		$image = get_theme_mod( 'conair_hero_image', conair_default_hero_image() );
		printf(
			'<style>:root{--hero-bg-image:url("%s");}</style>',
			esc_url_raw( $image )
		);
	}
);
