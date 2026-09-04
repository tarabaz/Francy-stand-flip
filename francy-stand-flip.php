<?php
/**
 * Plugin Name:       Francy Stand Flip
 * Plugin URI:        https://francystore3d.it
 * Description:       Box vetrina per gli stand FrancyStore3D: carta Pokémon che sborda dal box, foto dello stand, titolo, descrizione e link a post/reel Instagram. Si inserisce in Avada (o qualsiasi tema) tramite shortcode globale o per singolo stand.
 * Version:           1.2.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            FrancyStore3D
 * License:           GPL-2.0-or-later
 * Text Domain:       francy-stand-flip
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FSF_VERSION', '1.2.0' );
define( 'FSF_FILE', __FILE__ );
define( 'FSF_DIR', plugin_dir_path( __FILE__ ) );
define( 'FSF_URL', plugin_dir_url( __FILE__ ) );
define( 'FSF_POST_TYPE', 'fsf_stand' );
define( 'FSF_OPTION', 'fsf_settings' );

require_once FSF_DIR . 'includes/class-fsf-settings.php';
require_once FSF_DIR . 'includes/class-fsf-cpt.php';
require_once FSF_DIR . 'includes/class-fsf-metabox.php';
require_once FSF_DIR . 'includes/class-fsf-render.php';
require_once FSF_DIR . 'includes/class-fsf-shortcodes.php';
require_once FSF_DIR . 'includes/class-fsf-admin.php';

/**
 * Avvio del plugin.
 */
function fsf_boot() {
	FSF_CPT::init();
	FSF_Settings::init();
	FSF_Metabox::init();
	FSF_Shortcodes::init();
	FSF_Admin::init();
}
add_action( 'plugins_loaded', 'fsf_boot' );

/**
 * Attivazione: registra il CPT e riscrive i permalink.
 */
function fsf_activate() {
	FSF_CPT::register();
	flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'fsf_activate' );

/**
 * Disattivazione: pulisce i permalink.
 */
function fsf_deactivate() {
	flush_rewrite_rules();
}
register_deactivation_hook( __FILE__, 'fsf_deactivate' );
