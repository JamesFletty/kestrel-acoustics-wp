<?php
/**
 * Default page.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();
while ( have_posts() ) :
	the_post();
	?>
	<article <?php post_class( 'ka-section ka-wrap ka-page' ); ?>>
		<h1 class="ka-section__title"><?php the_title(); ?></h1>
		<div class="ka-prose"><?php the_content(); ?></div>
	</article>
	<?php
endwhile;
get_footer();
