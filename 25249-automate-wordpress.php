<?php
/**
 * Plugin Name: Mosaika — Automatiser des logiques WordPress en arrière-plan avec un Cron ou Action Scheduler
 * Description: Exemple de code accompagnant l'article de blog expliquant comment utiliser les Crons WordPress et Action Scheduler.
 * Author: Pierre Saïkali
 * Author URI: https://mosaika.fr/cron-wordpress-action-scheduler/
 * Version: 1.0.0
 */

namespace Mosaika\Automate_WordPress;

defined( 'ABSPATH' ) || exit;

define( 'MSK_AUTOMATE_WP_DIR', plugin_dir_path( __FILE__ ) );
define( 'MSK_AUTOMATE_WP_BASENAME', plugin_basename( __FILE__ ) );

define( 'MSK_AUTOMATE_WP_JAMENDO_CLIENT_ID', '67b86bcb' );

// Chargement de la librairie Action Scheduler.
require_once MSK_AUTOMATE_WP_DIR . 'lib/action-scheduler/action-scheduler.php';

/**
 * Chargement des fichiers vitaux de cette extension.
 *
 * @return void
 */
function require_files() {
	require_once MSK_AUTOMATE_WP_DIR . '/src/api.php';
	require_once MSK_AUTOMATE_WP_DIR . '/src/frontend.php';
	require_once MSK_AUTOMATE_WP_DIR . '/src/jobs.php';
	require_once MSK_AUTOMATE_WP_DIR . '/src/utils.php';
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\\require_files' );

/**
 * Enregistrement de notre Cron lors de l'activation de cette extension.
 *
 * @param string $plugin
 * @param boolean $network
 * @return void
 */
function on_activation( $plugin, $network ) {
	if ( $plugin === MSK_AUTOMATE_WP_BASENAME && ! wp_next_scheduled( 'msk/cron/import-playlists' ) ) {
		wp_schedule_event( strtotime( 'tomorrow 02:00' ), 'daily', 'msk/cron/import-playlists', [ 'keyword' => 'electro' ] );
	}
}
add_action( 'activate_plugin', __NAMESPACE__ . '\\on_activation', 10, 2 );

/**
 * Suppression de notre Cron lors de la désactivation de cette extension.
 *
 * @param string $plugin
 * @param boolean $network
 * @return void
 */
function on_deactivation( $plugin, $network ) {
	if ( $plugin === MSK_AUTOMATE_WP_BASENAME ) {
		wp_clear_scheduled_hook( 'msk/cron/import-playlists' );
	}
}
add_action( 'deactivate_plugin', __NAMESPACE__ . '\\on_deactivation', 10, 2 );
