<?php
/**
 * Core functions for Better AMP
 *
 * @package    BetterAMP
 * @author     BetterStudio <info@betterstudio.com>
 * @copyright  Copyright (c) 2016, BetterStudio
 */

if ( ! function_exists( 'is_better_amp' ) ) {
	/**
	 * Detect is the query for an AMP page?
	 *
	 * @since 1.0.0
	 *
	 * @param null $wp_query
	 * @param bool $default
	 *
	 * @return bool true when amp page requested
	 */
	function is_better_amp( $wp_query = NULL, $default = FALSE ) {

		// $wp_query will passed in pre_get_posts or other filters
		if ( ! is_null( $wp_query ) ) {
			return $wp_query->get( Better_AMP::STARTPOINT, $default );
		} else {

			global $wp_query;

			// check the $wp_query
			if ( is_null( $wp_query ) ) {
				return FALSE;
			} else {
				return $wp_query->get( Better_AMP::STARTPOINT, $default );
			}
		}

	}
}


/**
 * @param string $component_class component class name
 * @param array  $settings        component settings array {
 *
 * @type array   $tags            component amp tag. Example: amp-img
 * @type array   $scripts_url     component javascript URL. Example: https://cdn.ampproject.org/v0/..
 * }
 *
 * @global array $better_amp_registered_components
 *                                better-amp components information array
 *
 * @since 1.0.0
 *
 * @return bool|WP_Error true on success or WP_Error on failure.
 */
function better_amp_register_component( $component_class, $settings = array() ) {

	global $better_amp_registered_components;

	if ( ! isset( $better_amp_registered_components ) ) {
		$better_amp_registered_components = array();
	}

	try {
		if ( ! class_exists( $component_class ) ) {
			throw new Exception( __( 'invalid component class name.', 'better-amp' ) );
		}

		$interfaces = class_implements( 'Better_AMP_IMG_Component' );

		if ( ! isset( $interfaces ['Better_AMP_Component_Interface'] ) ) {
			throw new Exception( sprintf( __( 'Error! class %s must implements %s contracts!', 'better-amp' ), $component_class, 'Better_AMP_Component_Interface' ) );
		}

		$better_amp_registered_components[] = compact( 'component_class', 'settings' ); // maybe need add some extra indexes like __FILE__ in the future!

		return TRUE;
	} catch( Exception $e ) {

		return new WP_Error( 'error', $e->getMessage() );
	}
} // better_amp_register_component


/**
 * Initialize $better_amp_scripts if it has not been set.
 *
 * @global Better_AMP_Scripts $better_amp_scripts
 *
 * @since 1.0.0
 *
 * @return Better_AMP_Scripts Better_AMP_Scripts instance.
 */
function better_amp_scripts() {

	global $better_amp_scripts;

	if ( ! ( $better_amp_scripts instanceof Better_AMP_Scripts ) ) {
		$better_amp_scripts = new Better_AMP_Scripts();
	}

	return $better_amp_scripts;
}


/**
 * Enqueue a js file for amp version.
 *
 * @see   wp_enqueue_script
 *
 * @param string $handle
 * @param string $src
 * @param array  $deps
 * @param string $media
 *
 * @since 1.0.0
 */
function better_amp_enqueue_script( $handle, $src = '', $deps = array(), $media = 'all' ) {

	$better_amp_scripts = better_amp_scripts();

	if ( $src ) {
		$_handle = explode( '?', $handle );
		$better_amp_scripts->add( $_handle[0], $src, $deps, FALSE, $media );
	}

	$better_amp_scripts->enqueue( $handle );
}

/**
 * Check whether a script has been added to the queue.
 *
 * @param   string $handle
 * @param string   $list
 *
 * @since 1.0.0
 *
 * @return bool
 */
function better_amp_script_is( $handle, $list = 'enqueued' ) {
	return (bool) better_amp_scripts()->query( $handle, $list );
}

/**
 * Callback: Generate and echo scripts HTML tags
 * action  : better-amp/template/head
 *
 * @since 1.0.0
 */
function better_amp_print_scripts() {
	better_amp_scripts()->do_items();
}


/**
 * Callback: Custom hook for enqueue scripts action
 * action  : better-amp/template/head
 *
 * @since 1.0.0
 */
function better_amp_enqueue_scripts() {
	do_action( 'better-amp/template/enqueue-scripts' );
}


/**
 * Initialize $better_amp_styles if it has not been set.
 *
 * @global Better_AMP_Styles $better_amp_styles
 *
 * @since 1.0.0
 *
 * @return Better_AMP_Styles Better_AMP_Styles instance.
 */
function better_amp_styles() {

	global $better_amp_styles;

	if ( ! ( $better_amp_styles instanceof Better_AMP_Styles ) ) {
		$better_amp_styles = new Better_AMP_Styles();
	}

	return $better_amp_styles;
}


/**
 * Enqueue a css file for amp version.
 *
 * @see   wp_enqueue_style
 *
 * @param string           $handle
 * @param string           $src
 * @param array            $deps
 * @param string|bool|null $ver
 * @param string           $media
 *
 *
 * @since 1.0.0
 */
function better_amp_enqueue_style( $handle, $src = '', $deps = array(), $ver = FALSE, $media = 'all' ) {

	$better_amp_styles = better_amp_styles();

	if ( $src ) {
		$_handle = explode( '?', $handle );
		$better_amp_styles->add( $_handle[0], $src, $deps, $ver, $media );
	}

	$better_amp_styles->enqueue( $handle );
}


/**
 * Callback: Generate and echo stylesheet HTML tags
 * action  : better-amp/template/head
 *
 * @since 1.0.0
 */
function better_amp_print_styles() {
	better_amp_styles()->do_items();
}


/**
 * Add extra CSS styles to a registered stylesheet.
 *
 * @see   wp_add_inline_style for more information
 *
 * @param string $handle Name of the stylesheet to add the extra styles to.
 * @param string $data   String containing the CSS styles to be added.
 *
 * @since 1.0.0
 *
 * @return bool True on success, false on failure.
 */
function better_amp_add_inline_style( $data, $handle = '' ) {

	if ( FALSE !== stripos( $data, '</style>' ) ) {
		_doing_it_wrong( __FUNCTION__, sprintf(
			__( 'Do not pass %1$s tags to %2$s.', 'better-amp' ),
			'<code>&lt;style&gt;</code>',
			'<code>better_amp_add_inline_style()</code>'
		), '1.0.0' );
		$data = trim( preg_replace( '#<style[^>]*>(.*)</style>#is', '$1', $data ) );
	}

	return better_amp_styles()->add_inline_style( $handle, $data );
}


/**
 * Add css file data as inline style
 *
 * @see   wp_add_inline_style for more information
 *
 * @param string $handle    Name of the stylesheet to add the extra styles to.
 * @param string $file_path css file path
 *
 * @since 1.0.0
 *
 * @return bool True on success, false on failure.
 */
function better_amp_enqueue_inline_style( $file_path, $handle = '' ) {

	if ( file_exists( $file_path ) ) {
		$data = file_get_contents( $file_path );

		return better_amp_add_inline_style( $data, $handle );
	}

	return FALSE;
}


/**
 * Get url of plugin directory
 *
 * @param string $path path to append the following url
 *
 * @since 1.0.0
 *
 * @return string
 */
function better_amp_plugin_url( $path = '' ) {

	$url = plugin_dir_url( __BETTER_AMP_FILE__ );

	if ( $path ) {
		$url .= $path;
	}

	return $url;
}

/**
 * Handle customizer static files in amp version
 * todo: fix javascript issue  - Live-update changed settings in real time not working :(
 *
 * @param WP_Customize_Manager $customize_manager
 *
 * @since 1.0.0
 */
function better_amp_customize_preview_init( $customize_manager ) {

	//	better_amp_enqueue_script( 'customize-preview' );
	wp_enqueue_script( 'customize-preview' );
	add_action( 'better-amp/template/head', array( $customize_manager, 'customize_preview_base' ) );
	add_action( 'better-amp/template/head', array( $customize_manager, 'customize_preview_html5' ) );
	add_action( 'better-amp/template/head', array( $customize_manager, 'customize_preview_loading_style' ) );
	add_action( 'better-amp/template/footer', array( $customize_manager, 'customize_preview_settings' ), 20 );

	do_action( 'better_amp_customize_preview_init', $customize_manager );
}


/**
 * Detects Non-AMP URL of current page
 *
 * @since 1.0.0
 *
 * @return string|void
 */
function better_amp_guess_none_amp_url() {
	$abspath_fix         = str_replace( '\\', '/', ABSPATH );
	$script_filename_dir = dirname( $_SERVER['SCRIPT_FILENAME'] );

	if ( $script_filename_dir . '/' == $abspath_fix ) {
		// Strip off any file/query params in the path
		$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['PHP_SELF'] );

	} else {
		if ( FALSE !== strpos( $_SERVER['SCRIPT_FILENAME'], $abspath_fix ) ) {
			// Request is hitting a file inside ABSPATH
			$directory = str_replace( ABSPATH, '', $script_filename_dir );
			// Strip off the sub directory, and any file/query params
			$path = preg_replace( '#/' . preg_quote( $directory, '#' ) . '/[^/]*$#i', '', $_SERVER['REQUEST_URI'] );
		} elseif ( FALSE !== strpos( $abspath_fix, $script_filename_dir ) ) {
			// Request is hitting a file above ABSPATH
			$subdirectory = substr( $abspath_fix, strpos( $abspath_fix, $script_filename_dir ) + strlen( $script_filename_dir ) );
			// Strip off any file/query params from the path, appending the sub directory to the install
			$path = preg_replace( '#/[^/]*$#i', '', $_SERVER['REQUEST_URI'] ) . $subdirectory;
		} else {
			$path = $_SERVER['REQUEST_URI'];
		}
	}


	$amp_qv = Better_AMP::STARTPOINT;
	if ( preg_match( "#^$path/*$amp_qv/+(.*?)$#", $_SERVER['REQUEST_URI'], $matched ) ) {

		return site_url( $matched[1] );
	}
}


if ( ! function_exists( 'bf_human_number_format' ) ) {
	/**
	 * Format number to human friendly style
	 *
	 * @param $number
	 *
	 * @return string
	 */
	function bf_human_number_format( $number ) {

		if ( ! is_numeric( $number ) ) {
			return $number;
		}

		if ( $number >= 1000000 ) {
			return round( ( $number / 1000 ) / 1000, 1 ) . "M";
		} elseif ( $number >= 100000 ) {
			return round( $number / 1000, 0 ) . "k";
		} else {
			return @number_format( $number );
		}

	}
}


if ( ! function_exists( 'better_amp_translation_get' ) ) {
	/**
	 * Returns translation of strings from panel
	 *
	 * @param $key
	 *
	 * @since 1.0.0
	 *
	 * @return mixed|string
	 */
	function better_amp_translation_get( $key ) {

		static $option;

		if ( ! $option ) {
			$option = get_option( 'better-amp-translation' );
		}

		if ( ! empty( $option[ $key ] ) ) {
			return $option[ $key ];
		}

		static $std;

		if ( is_null( $std ) ) {
			$std = apply_filters( 'better-amp/translation/std', array() );
		}

		if ( isset( $std[ $key ] ) ) {

			// save it for next time
			$option[ $key ] = $std[ $key ];
			update_option( 'better-amp-translation', $option );

			return $std[ $key ];
		}

		return '';
	}
}


if ( ! function_exists( 'better_amp_translation_echo' ) ) {
	/**
	 * Prints translation of text
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 */
	function better_amp_translation_echo( $key ) {
		echo better_amp_translation_get( $key );
	}
}

