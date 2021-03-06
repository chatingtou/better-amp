<?php

add_action( 'customize_preview_init', 'better_amp_enqueue_customizer_js' );

/**
 * Callback: enqueue customizer preview javascript
 * Action  : customize_preview_init
 *
 * @since 1.0.0
 */
function better_amp_enqueue_customizer_js() {

	//	better_amp_enqueue_script(
	wp_enqueue_script(
		'better-amp-customizer',
		better_amp_plugin_url( 'template/customizer/customize-preview.js' ),
		array( 'customize-preview', 'jquery' )
	);

}

add_action( 'customize_register', 'better_amp_register_custom_controls' );

function better_amp_register_custom_controls( $wp_customize ) {

	$wp_customize->register_control_type( 'AMP_Customize_Social_Sorter_Control' );
}


add_action( 'customize_controls_enqueue_scripts', 'better_amp_add_customizer_script' );

function better_amp_add_customizer_script() {

	global $wpdb;

	//	better_amp_enqueue_script(
	wp_enqueue_script(
		'better-amp-customizer',
		better_amp_plugin_url( 'template/customizer/customizer.js' ),
		array( 'jquery' )
	);


	$sql    = 'SELECT term_id FROM ' . $wpdb->term_taxonomy . ' WHERE taxonomy=\'category\' ORDER BY count DESC LIMIT 1';
	$cat_ID = (int) $wpdb->get_var( $sql );

	$sql     = 'SELECT ID FROM ' . $wpdb->posts . ' as p INNER JOIN ' . $wpdb->postmeta . ' as pm on(p.ID = pm.post_id)' .
	           ' WHERE p.post_type=\'post\' AND p.post_status=\'publish\' AND pm.meta_value != \'\'' .
	           ' AND NOT EXISTS( SELECT post_id FROM ' . $wpdb->postmeta . ' WHERE post_id = p.ID AND meta_key = \'disable-better-amp\')' .
	           ' AND pm.meta_key = \'_thumbnail_id\' LIMIT 1';
	$post_ID = (int) $wpdb->get_var( $sql );

	wp_localize_script( 'better-amp-customizer', 'better_amp_customizer', array(
		'amp_url'     => better_amp_site_url(),
		'archive_url' => Better_AMP_Content_Sanitizer::transform_to_amp_url( get_category_link( $cat_ID ) ),
		'post_url'    => Better_AMP_Content_Sanitizer::transform_to_amp_url( get_the_permalink( $post_ID ) ),
	) );
}


add_action( 'customize_register', 'better_amp_customize_register' );

/**
 * Callback: Register customizer input fields
 * Action  : customize_register
 *
 * @since 1.0.0
 *
 * @param WP_Customize_Manager $wp_customizer
 */
function better_amp_customize_register( $wp_customizer ) {

	include BETTER_AMP_PATH . 'template/customizer/class-amp-customize-divider-control.php';
	include BETTER_AMP_PATH . 'template/customizer/class-amp-customize-switch-control.php';
	include BETTER_AMP_PATH . 'template/customizer/class-amp-customize-social-sorter-control.php';

	/**
	 * 0. AMP Panel
	 */
	$wp_customizer->add_panel(
		new WP_Customize_Panel(
			$wp_customizer,
			'better-amp-panel',
			array(
				'title'    => __( 'AMP Theme', 'better-amp' ),
				'priority' => 10,
			)
		)
	);


	/**
	 * 1. Add Header section
	 */
	$wp_customizer->add_section( 'better-amp-header-section', array(
		'title'    => better_amp_translation_get( 'header' ),
		'priority' => 5,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 1.1 Logo section
	 */
	$wp_customizer->add_setting( 'better-amp-header-logo-text', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-header-logo-text' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-header-logo-text', array(
		'label'    => __( 'Text Logo', 'better-amp' ),
		'section'  => 'better-amp-header-section',
		'priority' => 8,
	) );

	if ( $wp_customizer->selective_refresh ) {

		$wp_customizer->selective_refresh->add_partial( 'better-amp-header-logo-text', array(
			'settings'            => array( 'better-amp-header-logo-text' ),
			'selector'            => '.branding',
			'render_callback'     => 'better_amp_default_theme_logo',
			'container_inclusive' => TRUE,
		) );
	}


	$wp_customizer->add_setting( 'better-amp-header-logo-img', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-header-logo-img' ),
		'transport' => 'postMessage',
	) );
	$logo_settings = better_amp_get_default_theme_setting( 'logo' );

	$control_class = class_exists( 'WP_Customize_Cropped_Image_Control' ) ? 'WP_Customize_Cropped_Image_Control' : 'WP_Customize_Image_Control';
	$wp_customizer->add_control( new $control_class( $wp_customizer, 'better-amp-header-logo-img', array(
		'label'         => __( 'Logo', 'better-amp' ),
		'section'       => 'better-amp-header-section',
		'priority'      => 10,
		'height'        => $logo_settings['height'],
		'width'         => $logo_settings['width'],
		'flex_height'   => $logo_settings['flex-height'],
		'flex_width'    => $logo_settings['flex-width'],
		'button_labels' => array(
			'select'       => __( 'Select logo', 'better-amp' ),
			'change'       => __( 'Change logo', 'better-amp' ),
			'remove'       => __( 'Remove', 'better-amp' ),
			'default'      => __( 'Default', 'better-amp' ),
			'placeholder'  => __( 'No logo selected', 'better-amp' ),
			'frame_title'  => __( 'Select logo', 'better-amp' ),
			'frame_button' => __( 'Choose logo', 'better-amp' ),
		),
	) ) );

	if ( $wp_customizer->selective_refresh ) {

		$wp_customizer->selective_refresh->add_partial( 'better-amp-header-logo-img', array(
			'settings'            => array( 'better-amp-header-logo-img' ),
			'selector'            => '.branding',
			'render_callback'     => 'better_amp_default_theme_logo',
			'container_inclusive' => TRUE,
		) );
	}


	/**
	 * 1.2 Divider
	 */
	$wp_customizer->add_setting( 'better-amp-header-divider-1', array() );
	$wp_customizer->add_control( new AMP_Customize_Divider_Control( $wp_customizer, 'better-amp-header-divider-1', array(
		'section'  => 'better-amp-header-section',
		'priority' => 12,
	) ) );


	/**
	 * 1.3 Toggle Search
	 */
	$wp_customizer->add_setting( 'better-amp-header-show-search', array(
		'transport' => 'postMessage',
		'default'   => better_amp_get_default_theme_setting( 'better-amp-header-show-search' ),
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-header-show-search', array(
		'label'    => __( 'Show Search', 'better-amp' ),
		'section'  => 'better-amp-header-section',
		'priority' => 14,
	) ) );


	/**
	 * 2. Add Sidebar section
	 */
	$wp_customizer->add_section( 'better-amp-sidebar-section', array(
		'title'    => __( 'Sidebar', 'better-amp' ),
		'priority' => 7,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 2.1 Toggle Sidebar
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-show', array(
		'transport' => 'postMessage',
		'default'   => better_amp_get_default_theme_setting( 'better-amp-sidebar-show' ),
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-sidebar-show', array(
		'label'    => __( 'Show Sidebar', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 8,
	) ) );


	/**
	 * 2.2 Divider
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-divider-1', array() );
	$wp_customizer->add_control( new AMP_Customize_Divider_Control( $wp_customizer, 'better-amp-sidebar-divider-1', array(
		'section'  => 'better-amp-sidebar-section',
		'priority' => 10,
	) ) );


	/**
	 * 2.3 Logo section
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-logo-text', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-sidebar-logo-text' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-sidebar-logo-text', array(
		'label'    => __( 'Text Logo', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 12,
	) );
	if ( $wp_customizer->selective_refresh ) {

		$wp_customizer->selective_refresh->add_partial( 'better-amp-sidebar-logo-text', array(
			'settings'            => array( 'better-amp-sidebar-logo-text' ),
			'selector'            => '.sidebar-brand .brand-name .logo',
			'render_callback'     => 'better_amp_default_theme_sidebar_logo',
			'container_inclusive' => TRUE,
		) );
	}

	$wp_customizer->add_setting( 'better-amp-sidebar-logo-img', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-sidebar-logo-img' ),
		'transport' => 'postMessage',
	) );
	$logo_settings = better_amp_get_default_theme_setting( 'sidebar-logo' );

	$control_class = class_exists( 'WP_Customize_Cropped_Image_Control' ) ? 'WP_Customize_Cropped_Image_Control' : 'WP_Customize_Image_Control';
	$wp_customizer->add_control( new $control_class( $wp_customizer, 'better-amp-sidebar-logo-img', array(
		'label'         => __( 'Logo', 'better-amp' ),
		'section'       => 'better-amp-sidebar-section',
		'priority'      => 14,
		'height'        => $logo_settings['height'],
		'width'         => $logo_settings['width'],
		'flex_height'   => $logo_settings['flex-height'],
		'flex_width'    => $logo_settings['flex-width'],
		'button_labels' => array(
			'select'       => __( 'Select logo', 'better-amp' ),
			'change'       => __( 'Change logo', 'better-amp' ),
			'remove'       => __( 'Remove', 'better-amp' ),
			'default'      => __( 'Default', 'better-amp' ),
			'placeholder'  => __( 'No logo selected', 'better-amp' ),
			'frame_title'  => __( 'Select logo', 'better-amp' ),
			'frame_button' => __( 'Choose logo', 'better-amp' ),
		),
	) ) );

	if ( $wp_customizer->selective_refresh ) {

		$wp_customizer->selective_refresh->add_partial( 'better-amp-sidebar-logo-img', array(
			'settings'            => array( 'better-amp-sidebar-logo-img' ),
			'selector'            => '.sidebar-brand .brand-name .logo',
			'render_callback'     => 'better_amp_default_theme_sidebar_logo',
			'container_inclusive' => TRUE,
		) );
	}


	/**
	 * 2.4 Social icons
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-divider-2', array() );
	$wp_customizer->add_control( new AMP_Customize_Divider_Control( $wp_customizer, 'better-amp-sidebar-divider-2', array(
		'section'  => 'better-amp-sidebar-section',
		'priority' => 16,
	) ) );
	$wp_customizer->add_setting( 'better-amp-facebook', array(
		'default'   => '#',
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-facebook', array(
		'label'    => __( 'Facebook', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 18,
	) );
	$wp_customizer->add_setting( 'better-amp-twitter', array(
		'default'   => '#',
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-twitter', array(
		'label'    => __( 'Twitter', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 20,
	) );
	$wp_customizer->add_setting( 'better-amp-google_plus', array(
		'default'   => '#',
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-google_plus', array(
		'label'    => __( 'Google Plus', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 22,
	) );
	$wp_customizer->add_setting( 'better-amp-email', array(
		'default'   => '#',
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-email', array(
		'label'    => __( 'email', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 24,
	) );


	/**
	 * 2.5 Copyright text
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-footer-text', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-sidebar-footer-text' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-sidebar-footer-text', array(
		'label'    => __( 'Copyright text', 'better-amp' ),
		'section'  => 'better-amp-sidebar-section',
		'priority' => 26,
		'type'     => 'textarea',
	) );


	/**
	 * 1.2 Divider
	 */
	$wp_customizer->add_setting( 'better-amp-sidebar-divider-3', array() );
	$wp_customizer->add_control( new AMP_Customize_Divider_Control( $wp_customizer, 'better-amp-sidebar-divider-3', array(
		'section'  => 'better-amp-sidebar-section',
		'priority' => 27,
	) ) );


	/**
	 * 3. Footer
	 */
	$wp_customizer->add_section( 'better-amp-footer-section', array(
		'title'    => __( 'Footer', 'better-amp' ),
		'priority' => 7,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 3.1 Footer copyright text
	 */
	$wp_customizer->add_setting( 'better-amp-footer-copyright-text', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-footer-copyright-text' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( 'better-amp-footer-copyright-text', array(
		'label'    => __( 'Copyright text', 'better-amp' ),
		'section'  => 'better-amp-footer-section',
		'priority' => 18,
		'type'     => 'textarea',
	) );


	/**
	 * 3.2 Footer toggle none AMP version link
	 */
	$wp_customizer->add_setting( 'better-amp-footer-main-link', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-footer-main-link' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-footer-main-link', array(
		'label'    => __( 'Show none AMP version link', 'better-amp' ),
		'section'  => 'better-amp-footer-section',
		'priority' => 21,
	) ) );


	/**
	 * 4. Archive pages
	 */
	$wp_customizer->add_section( 'better-amp-archive-section', array(
		'title'    => __( 'Archive', 'better-amp' ),
		'priority' => 9,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 4.1 Archive listing
	 */
	$wp_customizer->add_setting( 'better-amp-archive-listing', array(
		'default' => better_amp_get_default_theme_setting( 'better-amp-archive-listing' ),
	) );
	$wp_customizer->add_control( 'better-amp-archive-listing', array(
		'label'    => __( 'Archive listing', 'better-amp' ),
		'section'  => 'better-amp-archive-section',
		'priority' => 20,
		'type'     => 'select',
		'choices'  => array(
			'listing-1' => __( 'Small Image Listing', 'better-amp' ),
			'listing-2' => __( 'Large Image Listing', 'better-amp' ),
		)
	) );


	/**
	 * 5. Post
	 */
	$wp_customizer->add_section( 'better-amp-post-section', array(
		'title'    => __( 'Posts', 'better-amp' ),
		'priority' => 11,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 5.1 Post thumbnail
	 */
	$wp_customizer->add_setting( 'better-amp-post-show-thumbnail', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-post-show-thumbnail' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-post-show-thumbnail', array(
		'label'    => __( 'Show Thumbnail', 'better-amp' ),
		'section'  => 'better-amp-post-section',
		'priority' => 2,
	) ) );


	/**
	 * 5.2 Show comments
	 */
	$wp_customizer->add_setting( 'better-amp-post-show-comment', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-post-show-comment' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-post-show-comment', array(
		'label'    => __( 'Show comment', 'better-amp' ),
		'section'  => 'better-amp-post-section',
		'priority' => 4,
	) ) );


	/**
	 * 5.3 Divider
	 */
	$wp_customizer->add_setting( 'better-amp-post-divider-1', array() );
	$wp_customizer->add_control( new AMP_Customize_Divider_Control( $wp_customizer, 'better-amp-post-divider-1', array(
		'section'  => 'better-amp-post-section',
		'priority' => 6,
	) ) );

	/**
	 * 5.4 Show Share Box
	 */
	$wp_customizer->add_setting( 'better-amp-post-social-share-show', array(
		'transport' => 'postMessage',
		'default'   => better_amp_get_default_theme_setting( 'better-amp-post-social-share-show' ),
	) );
	$wp_customizer->add_control( 'better-amp-post-social-share-show', array(
		'label'    => __( 'Show Share Box In Posts?', 'better-amp' ),
		'section'  => 'better-amp-post-section',
		'priority' => 7,
		'type'     => 'select',
		'choices'  => array(
			'show' => __( 'Show', 'better-amp' ),
			'hide' => __( 'Hide', 'better-amp' ),
		)
	) );

	/**
	 * 5.5 Show share count
	 */
	$wp_customizer->add_setting( 'better-amp-post-social-share-count', array(
		'transport' => 'postMessage',
		'default'   => better_amp_get_default_theme_setting( 'better-amp-post-social-share-count' ),
	) );
	$wp_customizer->add_control( 'better-amp-post-social-share-count', array(
		'label'    => __( 'Show share count?', 'better-amp' ),
		'section'  => 'better-amp-post-section',
		'priority' => 8,
		'type'     => 'select',
		'choices'  => array(
			'total'          => __( 'Show, Total share count', 'better-amp' ),
			'total-and-site' => __( 'Show, Total share count + Each site count', 'better-amp' ),
			'hide'           => __( 'No, Don\'t show.', 'better-amp' ),
		)
	) );


	/**
	 * 5.6 Social share sorter
	 */
	$wp_customizer->add_setting( 'better-amp-post-social-share', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-post-social-share' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( new AMP_Customize_Social_Sorter_Control( $wp_customizer, 'better-amp-post-social-share', array(
		'label'    => __( 'Drag and Drop To Sort The share sites', 'better-amp' ),
		'section'  => 'better-amp-post-section',
		'priority' => 9,
	) ) );


	/**
	 * 6. Homepage
	 */
	$wp_customizer->add_section( 'better-amp-home-section', array(
		'title'    => __( 'Homepage', 'better-amp' ),
		'priority' => 11,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 6.1 SlideShow toggle
	 */
	$wp_customizer->add_setting( 'better-amp-home-show-slide', array(
		'default'   => better_amp_get_default_theme_setting( 'better-amp-home-show-slide' ),
		'transport' => 'postMessage',
	) );
	$wp_customizer->add_control( new AMP_Customize_Switch_Control( $wp_customizer, 'better-amp-home-show-slide', array(
		'label'    => __( 'Show slider', 'better-amp' ),
		'section'  => 'better-amp-home-section',
		'priority' => 4,
	) ) );


	/**
	 * 6.2 Homepage listing
	 */
	$wp_customizer->add_setting( 'better-amp-home-listing', array(
		'default' => better_amp_get_default_theme_setting( 'better-amp-home-listing' ),
	) );
	$wp_customizer->add_control( 'better-amp-home-listing', array(
		'label'    => __( 'Homepage listing', 'better-amp' ),
		'section'  => 'better-amp-home-section',
		'priority' => 20,
		'type'     => 'select',
		'choices'  => array(
			'default'   => __( '-- Default Listing --', 'better-amp' ),
			'listing-1' => __( 'Small Image Listing', 'better-amp' ),
			'listing-2' => __( 'Large Image Listing', 'better-amp' ),
		)
	) );


	/**
	 * 7. Color
	 */
	$wp_customizer->add_section( 'better-amp-color-section', array(
		'title'    => __( 'Color', 'better-amp' ),
		'priority' => 13,
		'panel'    => 'better-amp-panel'
	) );


	/**
	 * 7.1 Theme Color
	 */
	$wp_customizer->add_setting( 'better-amp-color-theme', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-theme' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-theme', array(
		'label'   => __( 'Theme Color', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 7.2 BG Color
	 */
	$wp_customizer->add_setting( 'better-amp-color-bg', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-bg' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-bg', array(
		'label'   => __( 'Background Color', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 7.3 Content BG Color
	 */
	$wp_customizer->add_setting( 'better-amp-color-content-bg', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-content-bg' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-content-bg', array(
		'label'   => __( 'Content Background Color', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 7.4 Footer BG
	 */
	$wp_customizer->add_setting( 'better-amp-color-footer-bg', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-footer-bg' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-footer-bg', array(
		'label'   => __( 'Footer Background', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 7.5 Footer nav BG
	 */
	$wp_customizer->add_setting( 'better-amp-color-footer-nav-bg', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-footer-nav-bg' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-footer-nav-bg', array(
		'label'   => __( 'Footer Navigation Color', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 7.6 Text color
	 */
	$wp_customizer->add_setting( 'better-amp-color-text', array(
		'default'              => better_amp_get_default_theme_setting( 'better-amp-color-text' ),
		'sanitize_js_callback' => 'maybe_hash_hex_color',
		'transport'            => 'postMessage',
	) );
	$wp_customizer->add_control( new WP_Customize_Color_Control( $wp_customizer, 'better-amp-color-text', array(
		'label'   => __( 'Text Color', 'better-amp' ),
		'section' => 'better-amp-color-section',
	) ) );


	/**
	 * 8. Google Analytics
	 */
	$wp_customizer->add_section( 'better-amp-analytic-section', array(
		'title'    => __( 'Google Analytics', 'better-amp' ),
		'priority' => 14,
		'panel'    => 'better-amp-panel'
	) );

	/**
	 * 8.1 Google Analytics
	 */
	$wp_customizer->add_setting( 'better-amp-footer-analytics', array(
		'default' => better_amp_get_default_theme_setting( 'better-amp-footer-analytics' ),
	) );
	$wp_customizer->add_control( 'better-amp-footer-analytics', array(
		'label'       => __( 'Google Analytics', 'better-amp' ),
		'section'     => 'better-amp-analytic-section',
		'priority'    => 24,
		'description' => __( 'Insert google analytics account number.<br/> It’ll be in the format UA-XXXXXXXX-X', 'better-amp' ),
	) );


}

add_action( 'admin_menu', 'better_amp_add_customizer_admin_link', 999 );

function better_amp_add_customizer_admin_link() {

	$customize_url = add_query_arg( array(
		'return'    => urlencode( wp_unslash( $_SERVER['REQUEST_URI'] ) ),
		'url'       => urlencode( better_amp_site_url() ),
		'autofocus' => array( 'panel' => 'better-amp-panel' )
	), 'customize.php' );

	add_submenu_page(
		'better-amp-translation',
		_x( 'Customize AMP Theme', 'better-amp' ),
		_x( 'Customize AMP', 'better-amp' ),
		'manage_options',
		$customize_url
	);

}
