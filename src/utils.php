<?php

namespace Mosaika\Automate_WordPress\Utils;

defined( 'ABSPATH' ) || exit;

/**
 * Récupère tous les détails de la playlist que l'on a en base de données.
 *
 * @param integer $playlist_id
 * @return object
 */
function get_playlist_details( $playlist_id ) {
	return (object) [
		'name'         => get_post_meta( $playlist_id, 'name', true ),
		'id'           => get_post_meta( $playlist_id, 'jamendo_id', true ),
		'download_url' => get_post_meta( $playlist_id, 'download_url', true ),
		'share_url'    => get_post_meta( $playlist_id, 'share_url', true ),
		'tracks'       => get_post_meta( $playlist_id, 'tracks', true ),
	];
}
