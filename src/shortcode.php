<?php

namespace Mosaika\Automate_WordPress\Shortcode;

use function Mosaika\Automate_WordPress\Utils\get_user_gists;

defined( 'ABSPATH' ) || exit;

/**
 * Affiche les derniers Gists d'un utilisateur via le shortcode [gists user="X" amount="Y"]
 *
 * @param array $atts
 * @return string
 */
function output_shortcode( $atts ) {
	$atts = shortcode_atts(
		[
			'user'   => '',
			'amount' => 10,
		],
		$atts
	);

	if ( empty( $atts['user'] ) ) {
		return '<!-- Paramètre "user" manquant pour le shortcode [gists] -->';
	}

	$gists = get_user_gists( 'psaikali', (int) $atts['amount'] );

	if ( empty( $gists ) ) {
		return "<!-- Pas de Gists trouvés pour l'utilisateur {$atts['user']} -->";
	}

	ob_start();

	include sprintf( '%1$s%2$stemplates%2$sgists.php', untrailingslashit( MSK_HTTP_API_DIR ), DIRECTORY_SEPARATOR );

	return ob_get_clean();
}
add_shortcode( 'gists', __NAMESPACE__ . '\\output_shortcode' );
