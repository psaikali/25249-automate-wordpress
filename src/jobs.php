<?php

namespace Mosaika\Automate_WordPress\Jobs;

use function Mosaika\Automate_WordPress\API\get_playlist_tracks;
use function Mosaika\Automate_WordPress\API\search_playlists;

defined( 'ABSPATH' ) || exit;

/**
 * Lancer l'import : interroger l'API et créer des posts.
 *
 * @param string $keyword
 * @return void
 */
function fetch_playlists_from_api_and_import_them( $keyword = '' ) {
	$playlists = search_playlists( $keyword );

	if ( ! empty( $playlists ) ) {
		foreach ( $playlists as $playlist ) {
			do_action( 'msk/import-playlist', $playlist );
		}
	}
}
add_action( 'msk/cron/import-playlists', __NAMESPACE__ . '\\fetch_playlists_from_api_and_import_them', 10, 1 );

/**
 * Import d'une playlist Jamendo en un post WordPress.
 *
 * @param object $keyword
 * @return void
 */
function import_jamendo_playlist_in_wp_post( $playlist ) {
	$post_title = sprintf(
		'Playlist #%2$d "%1$s"',
		sanitize_text_field( $playlist->name ),
		$playlist->id
	);

	$post_content = sprintf(
		'<p>Une playlist créée par %1$s.</p><p><a href="%2$s">Voir sur Jamendo</a></p>',
		sanitize_text_field( $playlist->user_name ),
		esc_url( $playlist->shareurl )
	);

	// On crée le post.
	$playlist_wp_id = wp_insert_post( [
		'post_type'    => 'post', // Idéalement, créer un CPT spécifique.
		'post_date'    => date( 'Y-m-d H:i:s', strtotime( $playlist->creationdate ) ),
		'post_title'   => $post_title,
		'post_content' => $post_content,
		'post_status'  => 'draft',
	] );

	if ( $playlist_wp_id > 0 ) {
		// On enregistre les metas qui nous intéressent.
		update_post_meta( $playlist_wp_id, 'name', sanitize_text_field( $playlist->name ) );
		update_post_meta( $playlist_wp_id, 'jamendo_id', (int) $playlist->id );
		update_post_meta( $playlist_wp_id, 'download_url', esc_url( $playlist->zip ) );
		update_post_meta( $playlist_wp_id, 'share_url', esc_url( $playlist->shareurl ) );

		// On planifie l'import des chansons de la playlist.
		as_enqueue_async_action(
			'msk/cron/import-tracks',
			[ 'playlist_wp_id' => $playlist_wp_id, 'playlist_jamendo_id' => $playlist->id ]
		);
	}
}
add_action( 'msk/import-playlist', __NAMESPACE__ . '\\import_jamendo_playlist_in_wp_post', 10, 1 );

/**
 * Récupération des chansons d'une playlist et sauvegarde en métadonnées du post créé.
 *
 * @param integer $playlist_wp_id
 * @param integer $playlist_jamendo_id
 * @return void
 */
function import_jamendo_playlist_tracks( $playlist_wp_id = 0, $playlist_jamendo_id = 0 ) {
	$tracks = get_playlist_tracks( $playlist_jamendo_id );

	if ( ! empty( $tracks ) ) {
		$cleaned_tracks = array_map(
			function( $track ) {
				return (object) [
					'id'       => (int) $track->id,
					'position' => (int) $track->position,
					'name'     => sanitize_text_field( $track->name ),
					'artist'   => sanitize_text_field( $track->artist_name ),
					'duration' => (int) $track->duration,
					'image'    => esc_url_raw( $track->image ),
				];
			},
			$tracks
		);

		$cleaned_tracks = wp_list_sort( $cleaned_tracks, 'position' );

		update_post_meta( $playlist_wp_id, 'tracks', $cleaned_tracks );
		wp_update_post( [ 'ID' => $playlist_wp_id, 'post_status' => 'publish' ] );
	}
}
add_action( 'msk/cron/import-tracks', __NAMESPACE__ . '\\import_jamendo_playlist_tracks', 10, 2 );
