<?php
/**
 * Products: ka_product post type, ka_series taxonomy, technical-specs meta box.
 *
 * One schema (kestrel_spec_fields) drives meta registration, the admin UI,
 * saving, the spec-strip block, spec sheets and the comparison table.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

add_action( 'init', 'kestrel_register_products' );

/**
 * Register post type, taxonomy and meta.
 */
function kestrel_register_products(): void {
	register_post_type(
		'ka_product',
		array(
			'labels'        => array(
				'name'           => _x( 'Products', 'post type general name', 'kestrel' ),
				'singular_name'  => _x( 'Product', 'post type singular name', 'kestrel' ),
				'add_new_item'   => __( 'Add new product', 'kestrel' ),
				'edit_item'      => __( 'Edit product', 'kestrel' ),
				'all_items'      => __( 'All products', 'kestrel' ),
				'featured_image' => __( 'Product photograph', 'kestrel' ),
			),
			'public'        => true,
			'show_in_rest'  => true,
			'has_archive'   => 'products',
			'rewrite'       => array(
				'slug'       => 'products',
				'with_front' => false,
			),
			'menu_icon'     => 'dashicons-format-audio',
			'menu_position' => 20,
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'revisions', 'page-attributes' ),
		)
	);

	register_taxonomy(
		'ka_series',
		array( 'ka_product' ),
		array(
			'labels'            => array(
				'name'          => _x( 'Series', 'taxonomy general name', 'kestrel' ),
				'singular_name' => _x( 'Series', 'taxonomy singular name', 'kestrel' ),
			),
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => array( 'slug' => 'series' ),
		)
	);

	foreach ( kestrel_spec_fields() as $key => $field ) {
		register_post_meta(
			'ka_product',
			$key,
			array(
				'type'              => 'checkbox' === $field['type'] ? 'boolean' : 'string',
				'single'            => true,
				'show_in_rest'      => true,
				'sanitize_callback' => $field['sanitize'],
				'auth_callback'     => static fn(): bool => current_user_can( 'edit_posts' ),
			)
		);
	}
}

/**
 * Technical spec schema.
 *
 * @return array<string, array{label: string, type: string, sanitize: callable, placeholder?: string, spec?: bool}>
 */
function kestrel_spec_fields(): array {
	return array(
		'_ka_frequency' => array(
			'label'       => __( 'Frequency response', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '38 Hz – 22 kHz ±1.5 dB',
			'spec'        => true,
		),
		'_ka_spl'       => array(
			'label'       => __( 'Max SPL / output', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '116 dB SPL',
			'spec'        => true,
		),
		'_ka_impedance' => array(
			'label'       => __( 'Impedance', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '10 kΩ balanced',
			'spec'        => true,
		),
		'_ka_dimensions' => array(
			'label'       => __( 'Dimensions (H × W × D)', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '420 × 260 × 330 mm',
			'spec'        => true,
		),
		'_ka_weight'    => array(
			'label'       => __( 'Weight', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '14.2 kg',
			'spec'        => true,
		),
		'_ka_drivers'   => array(
			'label'       => __( 'Drivers / topology', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => '8 in woofer · 1 in AMT',
			'spec'        => true,
		),
		'_ka_price'     => array(
			'label'       => __( 'Price (USD)', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'kestrel_sanitize_price',
			'placeholder' => '4200',
		),
		'_ka_price_unit' => array(
			'label'       => __( 'Price unit', 'kestrel' ),
			'type'        => 'text',
			'sanitize'    => 'sanitize_text_field',
			'placeholder' => 'per pair',
		),
		'_ka_flagship'  => array(
			'label'    => __( 'Feature as flagship on the home page', 'kestrel' ),
			'type'     => 'checkbox',
			'sanitize' => 'rest_sanitize_boolean',
		),
	);
}

/**
 * Keep digits and a decimal point only.
 *
 * @param mixed $value Raw value.
 */
function kestrel_sanitize_price( $value ): string {
	$clean = preg_replace( '/[^0-9.]/', '', (string) $value );
	return is_numeric( $clean ) ? (string) round( (float) $clean, 2 ) : '';
}

add_action( 'add_meta_boxes_ka_product', 'kestrel_add_specs_meta_box' );

/**
 * Register the meta box.
 */
function kestrel_add_specs_meta_box(): void {
	add_meta_box( 'kestrel-specs', __( 'Technical specifications', 'kestrel' ), 'kestrel_render_specs_meta_box', 'ka_product', 'normal', 'high' );
}

/**
 * Meta box UI: a two-column table of spec inputs.
 *
 * @param WP_Post $post Post.
 */
function kestrel_render_specs_meta_box( WP_Post $post ): void {
	wp_nonce_field( 'kestrel_save_specs', 'kestrel_specs_nonce' );
	echo '<table class="form-table" role="presentation"><tbody>';
	foreach ( kestrel_spec_fields() as $key => $field ) {
		$id    = 'kestrel' . $key;
		$value = get_post_meta( $post->ID, $key, true );
		echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $field['label'] ) . '</label></th><td>';
		if ( 'checkbox' === $field['type'] ) {
			printf( '<input type="checkbox" id="%1$s" name="%1$s" value="1"%2$s>', esc_attr( $id ), checked( (bool) $value, true, false ) );
		} else {
			printf( '<input class="regular-text" type="text" id="%1$s" name="%1$s" value="%2$s" placeholder="%3$s">', esc_attr( $id ), esc_attr( (string) $value ), esc_attr( $field['placeholder'] ?? '' ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
}

add_action( 'save_post_ka_product', 'kestrel_save_specs', 10, 2 );

/**
 * Save specs with nonce, autosave, revision and capability checks.
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
function kestrel_save_specs( int $post_id, WP_Post $post ): void {
	$nonce = isset( $_POST['kestrel_specs_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['kestrel_specs_nonce'] ) ) : '';
	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'kestrel_save_specs' ) ) {
		return;
	}
	if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}

	foreach ( kestrel_spec_fields() as $key => $field ) {
		$name = 'kestrel' . $key;
		if ( 'checkbox' === $field['type'] ) {
			update_post_meta( $post_id, $key, isset( $_POST[ $name ] ) );
			continue;
		}
		if ( ! isset( $_POST[ $name ] ) ) {
			continue;
		}
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitized by schema.
		$value = call_user_func( $field['sanitize'], wp_unslash( $_POST[ $name ] ) );
		if ( '' === $value ) {
			delete_post_meta( $post_id, $key );
		} else {
			update_post_meta( $post_id, $key, $value );
		}
	}
}

add_action( 'pre_get_posts', 'kestrel_products_archive_query' );

/**
 * Show the whole catalogue in curated order on the products archive.
 *
 * @param WP_Query $query Query.
 */
function kestrel_products_archive_query( WP_Query $query ): void {
	if ( ! is_admin() && $query->is_main_query() && ( $query->is_post_type_archive( 'ka_product' ) || $query->is_tax( 'ka_series' ) ) ) {
		$query->set( 'posts_per_page', 50 );
		$query->set( 'orderby', 'menu_order' );
		$query->set( 'order', 'ASC' );
	}
}
