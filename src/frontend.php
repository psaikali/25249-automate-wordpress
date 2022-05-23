<?php

namespace Mosaika\Automate_WordPress\Frontend;

use function Mosaika\Automate_WordPress\Utils\get_playlist_details;

defined( 'ABSPATH' ) || exit;

/**
 * Affiche les détails de la playlist et des chansons dans le contenu du post.
 *
 * @param string $content
 * @return string
 */
function display_playlist_data_in_post_content( $content ) {
	global $post;

	// Idéalement, créer un CPT spécifique pour ça. Pour ce tutoriel, faisons simple :)
	if ( is_singular( 'post' ) && isset( $post->post_type ) && $post->post_type === 'post' ) {
		ob_start();

		echo '<pre><code>';
		var_dump( get_playlist_details( $post->ID ) );
		echo '</code></pre>';

		$content .= ob_get_clean();
	}

	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\\display_playlist_data_in_post_content', 10, 1 );
