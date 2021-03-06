<?php

add_image_size( 'better-amp-small', 100, 100, array( 'center', 'center' ) );  // Main Post Image In Full Width
add_image_size( 'better-amp-large', 450, 300, array( 'center', 'center' ) );  // Main Post Image In Full Width

add_theme_support( 'title-tag' );

register_nav_menu( 'amp-sidebar-nav', __( 'AMP Sidebar', 'better-amp' ) );

register_nav_menu( 'better-amp-footer', __( 'AMP Footer Navigation', 'better-amp' ) );

add_action( 'better-amp/template/enqueue-scripts', 'better_amp_custom_styles', 20 );

function better_amp_custom_styles() {

	$theme_color = better_amp_get_default_theme_mod( 'better-amp-color-theme', FALSE );

	$text_color = better_amp_get_default_theme_mod( 'better-amp-color-text', FALSE );

	ob_start();

	?>

	/*
	* => Theme Color
	*/
	.listing-item a.post-read-more:hover,
	.post-terms.cats .term-type,
	.post-terms a:hover,
	.search-form .search-submit,
	.better-amp-main-link a,
	.sidebar-brand,
	.site-header{
	background:<?php echo $theme_color ?>;
	}
	.single-post .post-meta a,
	.entry-content ul.bs-shortcode-list li:before,
	a{
	color: <?php echo $theme_color ?>;
	}


	/*
	* => Other Colors
	*/
	body.body {
	background:<?php echo better_amp_get_default_theme_mod( 'better-amp-color-bg', FALSE ) ?>;
	}
	.better-amp-wrapper {
	background:<?php echo better_amp_get_default_theme_mod( 'better-amp-color-content-bg', FALSE ) ?>;
	color: <?php echo $text_color ?>;
	}
	.better-amp-footer {
	background:<?php echo better_amp_get_default_theme_mod( 'better-amp-color-footer-bg', FALSE ) ?>;
	}
	.better-amp-footer-nav {
	background:<?php echo better_amp_get_default_theme_mod( 'better-amp-color-footer-nav-bg', FALSE ) ?>;
	}

	<?php


	better_amp_add_inline_style( ob_get_clean() );
}

/**
 * Enqueue static file for amp version
 */
add_action( 'better-amp/template/enqueue-scripts', 'better_amp_enqueue_static' );

function better_amp_enqueue_static() {

	better_amp_enqueue_style( 'font-awesome', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css' );
	better_amp_enqueue_style( 'google-fonts', 'https://fonts.googleapis.com/css?family=Lato:400,600|Roboto:300,400,500,700' );
	better_amp_enqueue_inline_style( dirname( __FILE__ ) . '/style.css' );

	better_amp_enqueue_script( 'amp-sidebar', 'https://cdn.ampproject.org/v0/amp-sidebar-0.1.js' );
	better_amp_enqueue_script( 'amp-sidebar', 'https://cdn.ampproject.org/v0/amp-accordion-0.1.js' );

	if ( better_amp_get_default_theme_mod( 'better-amp-footer-analytics' ) ) {

		better_amp_enqueue_script( 'amp-analytics', 'https://cdn.ampproject.org/v0/amp-analytics-0.1.js' );
	}
}

function better_amp_get_default_theme_setting( $setting_id, $setting_index = '' ) {

	$settings = array(
		'logo'                               => array(
			'height'      => 40,
			'width'       => 230,
			'flex-height' => FALSE,
			'flex-width'  => TRUE,
		),
		'sidebar-logo'                       => array(
			'height'      => 150,
			'width'       => 150,
			'flex-height' => TRUE,
			'flex-width'  => TRUE,
		),
		//
		'better-amp-header-logo-img'         => '',
		'better-amp-header-logo-text'        => '',
		'better-amp-header-show-search'      => TRUE,
		//
		'better-amp-sidebar-show'            => TRUE,
		'better-amp-sidebar-logo-text'       => '',
		'better-amp-sidebar-logo-img'        => '',
		'better-amp-facebook'                => '#',
		'better-amp-twitter'                 => '#',
		'better-amp-google_plus'             => '#',
		'better-amp-email'                   => '#',
		'better-amp-sidebar-footer-text'     => '',
		//
		'better-amp-footer-copyright-text'   => 'Powered by BetterAMP',
		'better-amp-footer-main-link'        => TRUE,
		//
		'better-amp-archive-listing'         => 'listing-1',
		//
		'better-amp-post-show-thumbnail'     => TRUE,
		'better-amp-post-show-comment'       => TRUE,
		'better-amp-post-social-share-show'  => 'show',
		'better-amp-post-social-share-count' => 'total',
		'better-amp-post-social-share'       => array(
			'facebook'    => 1,
			'twitter'     => 1,
			'reddit'      => 1,
			'google_plus' => 1,
			'email'       => 1,
			'pinterest'   => 0,
			'linkedin'    => 0,
			'tumblr'      => 0,
			'telegram'    => 0,
			'vk'          => 0,
			'whatsapp'    => 0,
			'stumbleupon' => 0,
			'digg'        => 0,
		),
		//
		'better-amp-home-show-slide'         => '1',
		'better-amp-home-listing'            => 'default',
		//
		'better-amp-color-theme'             => '#0379c4',
		'better-amp-color-bg'                => '#e8e8e8',
		'better-amp-color-content-bg'        => '#ffffff',
		'better-amp-color-footer-bg'         => '#f3f3f3',
		'better-amp-color-footer-nav-bg'     => '#ffffff',
		'better-amp-color-text'              => '#363636',
		//
		'better-amp-footer-analytics'        => '',
	);

	if ( $setting_index ) {
		if ( isset( $settings[ $setting_id ][ $setting_index ] ) ) {
			return $settings[ $setting_id ][ $setting_index ];
		}
	} else {
		if ( isset( $settings[ $setting_id ] ) ) {
			return $settings[ $setting_id ];
		}
	}
}


include BETTER_AMP_PATH . 'template/customizer/customizer.php';

function better_amp_default_theme_logo() {
	ob_start();
	$site_branding = better_amp_get_branding_info();
	?>
	<a href="<?php echo esc_attr( better_amp_site_url() ); ?>"
	   class="branding <?php echo ! empty( $site_branding['logo-tag'] ) ? 'image-logo' : 'text-logo'; ?> ">
		<?php

		if ( ! empty( $site_branding['logo-tag'] ) ) {
			echo $site_branding['logo-tag']; // escaped before
		} else {
			echo $site_branding['name']; // escaped before
		}

		?>
	</a>
	<?php

	return ob_get_clean();
}

function better_amp_default_theme_sidebar_logo() {
	ob_start();
	$site_branding = better_amp_get_branding_info( 'sidebar' );
	?>
	<a href="<?php echo esc_attr( better_amp_site_url() ); ?>"
	   class="branding <?php echo ! empty( $site_branding['logo-tag'] ) ? 'image-logo' : 'text-logo'; ?> ">
		<?php

		if ( ! empty( $site_branding['logo-tag'] ) ) {
			echo $site_branding['logo-tag']; // escaped before
		} else {
			echo $site_branding['name']; // escaped before
		}

		?>
	</a>
	<?php

	return ob_get_clean();
}


if ( ! function_exists( 'better_amp_page_listing' ) ) {
	/**
	 * Detects and returns current page listing style
	 *
	 * @return string
	 */
	function better_amp_page_listing() {

		static $listing;

		if ( $listing ) {
			return $listing;
		}

		$listing = 'default';

		if ( is_home() ) {
			$listing = better_amp_get_default_theme_mod( 'better-amp-home-listing' );
		}

		if ( empty( $listing ) || $listing === 'default' ) {
			$listing = better_amp_get_default_theme_mod( 'better-amp-archive-listing' );
		}

		return $listing;
	}
}


add_filter( 'better-amp/translation/fields', 'better_amp_translation_fields' );

if ( ! function_exists( 'better_amp_translation_fields' ) ) {
	/**
	 * Adds translation fields into panel
	 *
	 * @param array $fields
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function better_amp_translation_fields( $fields = array() ) {

		$fields['prev']                     = array(
			'id'      => 'prev',
			'type'    => 'text',
			'title'   => 'Previous',
			'default' => 'Previous'
		);
		$fields['next']                     = array(
			'id'      => 'next',
			'type'    => 'text',
			'title'   => 'Next',
			'default' => 'Next'
		);
		$fields['page']                     = array(
			'id'      => 'page',
			'type'    => 'text',
			'title'   => 'Page',
			'default' => 'Page'
		);
		$fields['page_of']                  = array(
			'id'       => 'page_of',
			'type'     => 'text',
			'title'    => 'of %d',
			'default'  => 'of %d',
			'subtitle' => __( '%d will be replace with page number.', 'better-amp' ),
		);
		$fields['by_on']                    = array(
			'id'       => 'by_on',
			'type'     => 'text',
			'title'    => 'By %s1 on %s2',
			'default'  => 'By %s1 on %s2',
			'subtitle' => __( '%s1 is author name and %s2 is post publish date.', 'better-amp' ),

		);
		$fields['browse_author_articles']   = array(
			'id'      => 'browse_author_articles',
			'type'    => 'text',
			'title'   => 'Browse Author Articles',
			'default' => 'Browse Author Articles',
		);
		$fields['comments']                 = array(
			'id'      => 'comments',
			'type'    => 'text',
			'title'   => 'Comments',
			'default' => 'Comments',
		);
		$fields['add_comment']              = array(
			'id'      => 'add_comment',
			'type'    => 'text',
			'title'   => 'Add Comment',
			'default' => 'Add Comment',
		);
		$fields['share']                    = array(
			'id'      => 'share',
			'type'    => 'text',
			'title'   => 'Share',
			'default' => 'Share',
		);
		$fields['view_desktop']             = array(
			'id'      => 'view_desktop',
			'type'    => 'text',
			'title'   => 'View Desktop Version',
			'default' => 'View Desktop Version',
		);
		$fields['read_more']                = array(
			'id'      => 'read_more',
			'type'    => 'text',
			'title'   => 'Read more',
			'default' => 'Read more',
		);
		$fields['listing_2_date']           = array(
			'id'      => 'listing_2_date',
			'type'    => 'text',
			'title'   => 'Large Listing Date Format',
			'default' => 'M d, Y',
		);
		$fields['search_on_site']           = array(
			'id'      => 'search_on_site',
			'type'    => 'text',
			'title'   => 'Search on site:',
			'default' => 'Search on site:',
		);
		$fields['search_input_placeholder'] = array(
			'id'      => 'search_input_placeholder',
			'type'    => 'text',
			'title'   => 'Search input placeholder',
			'default' => 'Search &hellip;',
		);
		$fields['search_button']            = array(
			'id'      => 'search_button',
			'type'    => 'text',
			'title'   => 'Search button',
			'default' => 'Search',
		);
		$fields['header']                   = array(
			'id'      => 'header',
			'type'    => 'text',
			'title'   => 'Header',
			'default' => 'Header',
		);
		$fields['tags']                     = array(
			'id'      => 'tags',
			'type'    => 'text',
			'title'   => 'Tags:',
			'default' => 'Tags:',
		);
		$fields['mr_404']                   = array(
			'id'      => 'mr_404',
			'type'    => 'text',
			'title'   => '404 Page Message',
			'default' => 'Oops! That page cannot be found.',
		);

		$fields['browsing']          = array(
			'id'      => 'browsing',
			'type'    => 'text',
			'title'   => 'Browsing',
			'default' => 'Browsing',
		);
		$fields['archive']           = array(
			'id'      => 'archive',
			'type'    => 'text',
			'title'   => 'Archive',
			'default' => 'Archive',
		);
		$fields['browsing_category'] = array(
			'id'      => 'browsing_category',
			'type'    => 'text',
			'title'   => 'Browsing category',
			'default' => 'Browsing category',
		);
		$fields['browsing_tag']      = array(
			'id'      => 'browsing_tag',
			'type'    => 'text',
			'title'   => 'Browsing tag',
			'default' => 'Browsing tag',
		);
		$fields['browsing_author']   = array(
			'id'      => 'browsing_author',
			'type'    => 'text',
			'title'   => 'Browsing author',
			'default' => 'Browsing author',
		);
		$fields['browsing_yearly']   = array(
			'id'      => 'browsing_yearly',
			'type'    => 'text',
			'title'   => 'Browsing yearly archive',
			'default' => 'Browsing yearly archive',
		);
		$fields['browsing_monthly']  = array(
			'id'      => 'browsing_monthly',
			'type'    => 'text',
			'title'   => 'Browsing monthly archive',
			'default' => 'Browsing monthly archive',
		);
		$fields['browsing_daily']    = array(
			'id'      => 'browsing_daily',
			'type'    => 'text',
			'title'   => 'Browsing daily archive',
			'default' => 'Browsing daily archive',
		);
		$fields['browsing_archive']  = array(
			'id'      => 'browsing_archive',
			'type'    => 'text',
			'title'   => 'Browsing archive',
			'default' => 'Browsing archive',
		);


		$fields['asides']    = array(
			'id'      => 'asides',
			'type'    => 'text',
			'title'   => 'Asides',
			'default' => 'Asides',
		);
		$fields['galleries'] = array(
			'id'      => 'galleries',
			'type'    => 'text',
			'title'   => 'Galleries',
			'default' => 'Galleries',
		);
		$fields['images']    = array(
			'id'      => 'images',
			'type'    => 'text',
			'title'   => 'Images',
			'default' => 'Images',
		);
		$fields['videos']    = array(
			'id'      => 'videos',
			'type'    => 'text',
			'title'   => 'Videos',
			'default' => 'Videos',
		);
		$fields['quotes']    = array(
			'id'      => 'quotes',
			'type'    => 'text',
			'title'   => 'Quotes',
			'default' => 'Quotes',
		);
		$fields['links']     = array(
			'id'      => 'links',
			'type'    => 'text',
			'title'   => 'Links',
			'default' => 'Links',
		);
		$fields['statuses']  = array(
			'id'      => 'statuses',
			'type'    => 'text',
			'title'   => 'Statuses',
			'default' => 'Statuses',
		);
		$fields['audio']     = array(
			'id'      => 'audio',
			'type'    => 'text',
			'title'   => 'Audio',
			'default' => 'Audio',
		);
		$fields['chats']     = array(
			'id'      => 'chats',
			'type'    => 'text',
			'title'   => 'Chats',
			'default' => 'Chats',
		);

		return $fields;

	} // better_amp_translation_fields
}


add_filter( 'better-amp/translation/std', 'better_amp_translation_stds' );

if ( ! function_exists( 'better_amp_translation_stds' ) ) {
	/**
	 * Prepares translation default values
	 *
	 * @param array $fields
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function better_amp_translation_stds( $fields = array() ) {

		$std['prev']                     = 'Previous';
		$std['next']                     = 'Next';
		$std['page']                     = 'Page';
		$std['page_of']                  = 'of %d';
		$std['by_on']                    = 'By %s1 on %s2';
		$std['browse_author_articles']   = 'Browse Author Articles';
		$std['comments']                 = 'Comments';
		$std['add_comment']              = 'Add Comment';
		$std['share']                    = 'Share';
		$std['header']                   = 'Header';
		$std['tags']                     = 'Tags:';
		$std['mr_404']                   = 'Oops! That page cannot be found.';
		$std['view_desktop']             = 'View Desktop Version';
		$std['read_more']                = 'Read more';
		$std['listing_2_date']           = 'M d, Y';
		$std['search_on_site']           = 'Search on site:';
		$std['search_input_placeholder'] = 'Search &hellip;';
		$std['search_button']            = 'Search';

		$std['browsing']          = 'Browsing';
		$std['archive']           = 'Archive';
		$std['browsing_category'] = 'Browsing category';
		$std['browsing_tag']      = 'Browsing tag';
		$std['browsing_author']   = 'Browsing author';
		$std['browsing_yearly']   = 'Browsing yearly archive';
		$std['browsing_monthly']  = 'Browsing monthly archive';
		$std['browsing_daily']    = 'Browsing daily archive';

		$std['asides']    = 'Asides';
		$std['galleries'] = 'Galleries';
		$std['images']    = 'Images';
		$std['videos']    = 'Videos';
		$std['quotes']    = 'Quotes';
		$std['links']     = 'Links';
		$std['statuses']  = 'Statuses';
		$std['audio']     = 'Audio';
		$std['chats']     = 'Chats';

		return $fields;

	} // better_amp_translation_stds
}
