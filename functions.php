<?php
/**
 * Kestrel Acoustics — theme bootstrap. Thin loader; concerns live in inc/.
 *
 * @package Kestrel
 */

defined( 'ABSPATH' ) || exit;

define( 'KESTREL_VERSION', '1.0.0' );
define( 'KESTREL_DIR', get_template_directory() );

foreach ( array( 'setup', 'assets', 'cpt-products', 'hardening', 'blocks', 'template-tags' ) as $kestrel_module ) {
	require_once KESTREL_DIR . '/inc/' . $kestrel_module . '.php';
}
unset( $kestrel_module );
