<?php
/**
 * The template to display the background video in the header
 *
 * @package ALLIANCE
 * @since ALLIANCE 1.0.14
 */
$alliance_header_video = alliance_get_header_video();
$alliance_embed_video  = '';
if ( ! empty( $alliance_header_video ) && ! alliance_is_from_uploads( $alliance_header_video ) ) {
	if ( alliance_is_youtube_url( $alliance_header_video ) && preg_match( '/[=\/]([^=\/]*)$/', $alliance_header_video, $matches ) && ! empty( $matches[1] ) ) {
		?><div id="background_video" data-youtube-code="<?php echo esc_attr( $matches[1] ); ?>"></div>
		<?php
	} else {
		?>
		<div id="background_video"><?php alliance_show_layout( alliance_get_embed_video( $alliance_header_video ) ); ?></div>
		<?php
	}
}
