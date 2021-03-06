<?php

better_amp_get_header();

better_amp_the_post();

?>
	<div <?php better_amp_post_classes( 'single-post clearfix' ) ?>>

		<h3 class="post-title">
			<?php the_title() ?>
		</h3>

		<?php better_amp_post_subtitle(); ?>

		<?php if ( better_amp_get_default_theme_mod( 'better-amp-post-show-thumbnail' ) && has_post_thumbnail() ): ?>
			<div class="post-thumbnail" <?php better_amp_customizer_hidden_attr( 'better-amp-post-show-thumbnail' ) ?>>
				<?php the_post_thumbnail( 'better-amp-large' ); ?>
			</div>
		<?php endif ?>


		<div class="post-meta">
			<?php

			$author_ID = get_the_author_meta( 'ID' );

			?>
			<a href="<?php echo esc_url( get_author_posts_url( $author_ID ) ); ?>"
			   title="<?php better_amp_translation_echo( 'browse_author_articles' ); ?>"
			   class="post-author-avatar"><?php echo get_avatar( $author_ID, 26 ); ?></a><?php

			$meta_text = str_replace(
				array(
					'%s1',
					'%s2'
				),
				array(
					'<a href="%1$s">%2$s</a>',
					'%3$s'
				),
				better_amp_translation_get( 'by_on' )
			);

			printf( $meta_text,
				get_author_posts_url( get_the_author_meta( 'ID' ) ),
				get_the_author(),
				get_the_date()
			);

			?>
		</div>

		<div class="post-content entry-content">
			<?php the_content() ?>
		</div>

		<?php

		the_tags(
			'<div class="post-terms tags"><span class="term-type"><i class="fa fa-tags"></i></span>',
			'',
			'</div>'
		);

		$cats = get_the_category_list( '' );
		if ( ! empty( $cats ) ) {

			?>
			<div class="post-terms cats"><span class="term-type"><i class="fa fa-folder-open"></i></span>
				<?php echo $cats; ?>
			</div>
			<?php
		}


		?>
	</div>

<?php

better_amp_template_part( 'social-share' );

if ( better_amp_get_default_theme_mod( 'better-amp-post-show-comment' ) && ( comments_open() || get_comments_number() ) ) { ?>
	<div class="comments-wrapper"<?php better_amp_customizer_hidden_attr( 'better-amp-post-show-comment' ) ?>>

		<div class="comments-label strong-label">
			<i class="fa fa-comments" aria-hidden="true"></i>
			<?php better_amp_translation_echo( 'comments' ); ?>

			<span class="counts-label">(<?php echo number_format_i18n( get_comments_number() ); ?>)</span>

		</div>

		<a href="<?php better_amp_comment_link() ?>"
		   class="button add-comment"><?php better_amp_translation_echo( 'add_comment' ); ?></a>
	</div>
	<?php

}

better_amp_get_footer();
