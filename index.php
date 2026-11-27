<?php
/**
 * Fallback: journal / posts index.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<section class="ka-section ka-wrap ka-page">
	<h1 class="ka-section__title"><?php esc_html_e( 'Lab notes', 'kestrel' ); ?></h1>
	<?php if ( have_posts() ) : ?>
		<ul class="ka-notes" role="list">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<li <?php post_class(); ?>><time class="ka-mono" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time><h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2><?php the_excerpt(); ?></li>
			<?php endwhile; ?>
		</ul>
		<?php the_posts_pagination(); ?>
	<?php else : ?>
		<p><?php esc_html_e( 'No notes yet.', 'kestrel' ); ?></p>
	<?php endif; ?>
</section>
<?php
get_footer();
