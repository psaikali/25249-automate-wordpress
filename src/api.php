<?php

namespace Mosaika\Automate_WordPress\API;

defined( 'ABSPATH' ) || exit;

/**
 * Recherche les X dernières playlists Jamendo répondant à un mot-clé de recherche.
 *
 * @param string $keyword
 * @param integer $amount
 * @return array
 */
function search_playlists( $keyword = '', $amount = 10 ) {
	$playlists = [];

	$response = wp_remote_request(
		'https://api.jamendo.com/v3.0/playlists/',
		[
			'timeout' => 15,
			'method'  => 'GET',
			'body' => [
				'client_id'  => MSK_AUTOMATE_WP_JAMENDO_CLIENT_ID,
				'order'      => 'creationdate_desc',
				'namesearch' => sanitize_text_field( $keyword ),
				'limit'      => (int) $amount,
			]
		]
	);

	if ( (int) wp_remote_retrieve_response_code( $response ) === 200 ) {
		$response  = json_decode( wp_remote_retrieve_body( $response ) );
		$playlists = ( isset( $response->results ) && is_array( $response->results ) ) ? $response->results : [];
	}

	return $playlists;
}

/**
 * Récupère les chansons d'une playlist spécifique.
 *
 * @param integer $playlist_id
 * @return array
 */
function get_playlist_tracks( $playlist_id ) {
	$tracks = [];

	$response = wp_remote_request(
		'https://api.jamendo.com/v3.0/playlists/tracks/',
		[
			'timeout' => 15,
			'method'  => 'GET',
			'body' => [
				'client_id' => MSK_AUTOMATE_WP_JAMENDO_CLIENT_ID,
				'id'        => $playlist_id,
			]
		]
	);

	if ( (int) wp_remote_retrieve_response_code( $response ) === 200 ) {
		$response  = json_decode( wp_remote_retrieve_body( $response ) );
		$tracks = ( isset( $response->results ) && is_array( $response->results ) ) ? $response->results[0]->tracks : [];
	}

	return $tracks;
}
