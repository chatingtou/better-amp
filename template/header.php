<!doctype html>
<html <?php better_amp_language_attributes(); ?> AMP>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width,minimum-scale=1,maximum-scale=1,initial-scale=1">

	<?php better_amp_head() ?>
</head>
<body <?php better_amp_body_class( 'sticky-nav body' ) ?>>
<?php

if ( better_amp_get_default_theme_mod( 'better-amp-sidebar-show' ) ) {
	better_amp_get_sidebar();
}

?>
<div class="better-amp-wrapper">
	<header itemscope itemtype="https://schema.org/WPHeader" class="site-header">
		<?php

		if ( better_amp_get_default_theme_mod( 'better-amp-sidebar-show' ) ) {
			?>
			<button class="fa fa-bars navbar-toggle" on="tap:better-ampSidebar.toggle"
				<?php better_amp_customizer_hidden_attr( 'better-amp-sidebar-show' ); ?>></button>
			<?php
		}

		echo better_amp_default_theme_logo();

		if ( better_amp_get_default_theme_mod( 'better-amp-header-show-search' ) ) {
			?>
			<a href="<?php echo better_amp_get_search_page_url() ?>"
			   class="navbar-search" <?php better_amp_customizer_hidden_attr( 'better-amp-header-show-search' ) ?>><i
					class="fa fa-search" aria-hidden="true"></i>
			</a>
			<?php
		}

		?>
	</header><!-- End Main Nav -->

	<div class="wrap">
