<?php
/**
 * Front page.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

get_header();

$kestrel_flagship = kestrel_resolve_product();
$kestrel_all      = new WP_Query(
	array(
		'post_type'      => 'ka_product',
		'posts_per_page' => 24,
		'orderby'        => 'menu_order',
		'order'          => 'ASC',
		'no_found_rows'  => true,
	)
);
$kestrel_by_series = array();
foreach ( $kestrel_all->posts as $kestrel_post ) {
	$kestrel_s                               = kestrel_series( $kestrel_post->ID );
	$kestrel_by_series[ $kestrel_s->slug ?? 'other' ][] = $kestrel_post;
}
$kestrel_monitors = array_values( array_filter( $kestrel_all->posts, static fn( $p ) => 'monitors' === ( kestrel_series( $p->ID )->slug ?? '' ) ) );
?>

<section class="ka-hero" aria-labelledby="ka-hero-title">
	<div class="ka-wrap">
		<div class="ka-hero__top">
			<div>
				<p class="ka-mono ka-hero__kicker"><span class="ka-dot" aria-hidden="true"></span><?php esc_html_e( 'Active monitors & analog mastering hardware', 'kestrel' ); ?></p>
				<h1 id="ka-hero-title" class="ka-hero__title"><?php echo wp_kses( get_theme_mod( 'kestrel_hero_title', __( 'Hear the mix.<br>Not the room.', 'kestrel' ) ), array( 'br' => array() ) ); ?></h1>
			</div>
			<?php if ( $kestrel_flagship ) : ?>
				<aside class="ka-hero__flagship" aria-label="<?php esc_attr_e( 'Flagship product', 'kestrel' ); ?>">
					<p class="ka-mono"><?php esc_html_e( 'Flagship / Rev. C', 'kestrel' ); ?></p>
					<p class="ka-hero__name"><?php echo esc_html( get_the_title( $kestrel_flagship ) ); ?></p>
					<p class="ka-hero__lead"><?php echo esc_html( get_the_excerpt( $kestrel_flagship ) ); ?></p>
					<div class="ka-hero__actions">
						<a class="ka-btn ka-btn--signal" href="<?php echo esc_url( get_permalink( $kestrel_flagship ) ); ?>"><?php echo esc_html( sprintf( /* translators: %s price */ __( 'Explore · %s', 'kestrel' ), kestrel_price( $kestrel_flagship->ID ) ) ); ?></a>
						<a class="ka-btn ka-btn--ghost" href="#compare"><?php esc_html_e( 'Compare specs', 'kestrel' ); ?></a>
					</div>
				</aside>
			<?php endif; ?>
		</div>
		<?php get_template_part( 'template-parts/hero-schematic', null, array( 'product' => $kestrel_flagship ) ); ?>
		<?php echo kestrel_render_spec_strip( array( 'productId' => $kestrel_flagship->ID ?? 0 ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in renderer. ?>
	</div>
</section>

<?php
$kestrel_sections = array(
	'monitors' => array( 'lineup', __( '01 / Monitors', 'kestrel' ), __( 'Active monitors, nearfield to main.', 'kestrel' ), __( 'Every cabinet is braced birch ply, every pair is matched within ±0.5 dB before it leaves the bench.', 'kestrel' ) ),
	'analog'   => array( 'analog', __( '02 / Analog', 'kestrel' ), __( 'Signal path you can feel in the knobs.', 'kestrel' ), __( 'Discrete Class A circuits, stepped controls and recall sheets you can actually trust.', 'kestrel' ) ),
);
foreach ( $kestrel_sections as $kestrel_slug => [ $kestrel_anchor, $kestrel_label, $kestrel_title, $kestrel_intro ] ) :
	if ( empty( $kestrel_by_series[ $kestrel_slug ] ) ) {
		continue;
	}
	?>
	<section class="ka-section ka-wrap" id="<?php echo esc_attr( $kestrel_anchor ); ?>" aria-labelledby="ka-<?php echo esc_attr( $kestrel_anchor ); ?>-title">
		<header class="ka-section__head">
			<p class="ka-mono ka-section__label"><?php echo esc_html( $kestrel_label ); ?></p>
			<h2 id="ka-<?php echo esc_attr( $kestrel_anchor ); ?>-title" class="ka-section__title"><?php echo esc_html( $kestrel_title ); ?></h2>
			<p class="ka-section__intro"><?php echo esc_html( $kestrel_intro ); ?></p>
		</header>
		<ul class="ka-grid<?php echo count( $kestrel_by_series[ $kestrel_slug ] ) <= 2 ? ' ka-grid--duo' : ''; ?>" role="list">
			<?php
			foreach ( $kestrel_by_series[ $kestrel_slug ] as $kestrel_post ) {
				$GLOBALS['post'] = $kestrel_post; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- standard setup_postdata pattern.
				setup_postdata( $kestrel_post );
				get_template_part( 'template-parts/product-card' );
			}
			wp_reset_postdata();
			?>
		</ul>
	</section>
<?php endforeach; ?>

<section class="ka-section ka-wrap" id="compare" aria-labelledby="ka-compare-title">
	<header class="ka-section__head">
		<p class="ka-mono ka-section__label"><?php esc_html_e( '03 / Spec sheets', 'kestrel' ); ?></p>
		<h2 id="ka-compare-title" class="ka-section__title"><?php esc_html_e( 'Published numbers. No asterisks.', 'kestrel' ); ?></h2>
		<p class="ka-section__intro"><?php esc_html_e( 'Every figure comes from our own anechoic measurements, not a driver supplier’s datasheet.', 'kestrel' ); ?></p>
	</header>
	<?php get_template_part( 'template-parts/spec-tabs', null, array( 'products' => $kestrel_monitors ) ); ?>
</section>

<section class="ka-section ka-wrap ka-lab" id="lab" aria-labelledby="ka-lab-title">
	<header class="ka-section__head">
		<p class="ka-mono ka-section__label"><?php esc_html_e( '04 / The Lab', 'kestrel' ); ?></p>
		<h2 id="ka-lab-title" class="ka-section__title"><?php esc_html_e( 'Measured, matched, signed.', 'kestrel' ); ?></h2>
	</header>
	<ol class="ka-lab__list" role="list">
		<li data-ka-reveal><span class="ka-lab__n">±0.5<small>dB</small></span><h3><?php esc_html_e( 'Pair matching', 'kestrel' ); ?></h3><p><?php esc_html_e( 'Each monitor is swept 20 Hz to 20 kHz and paired with its closest twin. The plot ships in the box, signed by the tech who ran it.', 'kestrel' ); ?></p></li>
		<li data-ka-reveal><span class="ka-lab__n">0.0008<small>%</small></span><h3><?php esc_html_e( 'THD+N, analog line', 'kestrel' ); ?></h3><p><?php esc_html_e( 'Discrete Class A gain stages, hand-selected transistors and Lundahl output transformers on every analog unit.', 'kestrel' ); ?></p></li>
		<li data-ka-reveal><span class="ka-lab__n">10<small>yr</small></span><h3><?php esc_html_e( 'Warranty, transferable', 'kestrel' ); ?></h3><p><?php esc_html_e( 'Every part is serviceable, and every schematic is published. Gear you can hand down.', 'kestrel' ); ?></p></li>
	</ol>
</section>

<section class="ka-listen" id="listen" aria-labelledby="ka-listen-title">
	<div class="ka-wrap ka-listen__inner">
		<h2 id="ka-listen-title" class="ka-listen__title"><?php esc_html_e( 'Bring a mix. Hear it properly.', 'kestrel' ); ?></h2>
		<p class="ka-listen__text"><?php esc_html_e( 'Two-hour sessions in our treated listening room, by appointment. Bring your own sessions on a drive; we’ll set up a blind A/B against your current monitors.', 'kestrel' ); ?></p>
		<a class="ka-btn ka-btn--dark" href="mailto:lab@kestrel-acoustics.example?subject=Listening%20session"><?php esc_html_e( 'Book a session', 'kestrel' ); ?></a>
	</div>
</section>

<?php
get_footer();
