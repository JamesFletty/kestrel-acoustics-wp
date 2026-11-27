<?php
/**
 * Footer.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;
?>
<footer class="ka-footer">
	<div class="ka-wrap">
		<div class="ka-footer__grid">
			<div>
				<p class="ka-footer__brand"><?php bloginfo( 'name' ); ?><span>.</span></p>
				<p class="ka-footer__tag"><?php esc_html_e( 'Studio monitors and analog mastering hardware, designed, measured and hand-assembled in one building.', 'kestrel' ); ?></p>
			</div>
			<div>
				<p class="ka-mono ka-footer__h"><?php esc_html_e( 'Workshop', 'kestrel' ); ?></p>
				<address><?php esc_html_e( '77 Foundry Lane, Unit 4', 'kestrel' ); ?><br><?php esc_html_e( 'Asheville, NC 28801', 'kestrel' ); ?></address>
			</div>
			<div>
				<p class="ka-mono ka-footer__h"><?php esc_html_e( 'Contact', 'kestrel' ); ?></p>
				<p><a href="mailto:lab@kestrel-acoustics.example">lab@kestrel-acoustics.example</a><br><a href="tel:+18285550134">(828) 555-0134</a></p>
			</div>
			<nav aria-label="<?php esc_attr_e( 'Footer', 'kestrel' ); ?>">
				<p class="ka-mono ka-footer__h"><?php esc_html_e( 'Index', 'kestrel' ); ?></p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'ka-footer__menu',
						'depth'          => 1,
						'fallback_cb'    => 'kestrel_fallback_menu',
					)
				);
				?>
			</nav>
		</div>
		<div class="ka-footer__base ka-mono">
			<span>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php esc_html_e( 'Kestrel Acoustics (demo)', 'kestrel' ); ?></span>
			<span><?php esc_html_e( 'Measured at 1 m, 2.83 V, anechoic. 10-year warranty.', 'kestrel' ); ?></span>
		</div>
	</div>
</footer>
