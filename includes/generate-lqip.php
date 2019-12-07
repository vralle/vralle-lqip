<?php
/**
 * Generate Placeholders
 *
 * @package vralle-lqip
 */

namespace VRalleLqip;

use function add_action;
use function add_filter;
use function array_unshift;
use function get_post_mime_type;
use function file_exists;
use function in_array;
use function is_array;
use function is_callable;
use function is_wp_error;
use function remove_filter;
use function sprintf;
use function str_replace;
use function update_post_meta;
use function wp_get_attachment_image_src;
use function wp_get_image_editor;
use function wp_schedule_single_event;
use function wp_upload_dir;
use WP_Error;

/**
 * Schedule a background task to generate placeholders.
 *
 * @param  array $metadata      An array of attachment meta data.
 * @param  int   $attachment_id Current attachment ID.
 * @return array Metadata for attachment.
 */
function queue_generate_placeholders_on_save( $metadata, $attachment_id ) {
    $mime_type = get_post_mime_type( $attachment_id );
    if ( $mime_type ) {
        // Is this a JPEG?
        if ( in_array( $mime_type, array( 'image/jpg', 'image/jpeg' ) ) ) {
            $event = wp_schedule_single_event( time() + 5, 'vll_generate_lqip', array( $attachment_id ) );
            if ( ! $event ) {
                new WP_Error( 'create_lqip_error', __( 'Error triggering a scheduled event.', 'vralle-lqip' ), 'vll_generate_lqip' );
            }
        }
    }

    return $metadata;
}
add_filter( 'wp_update_attachment_metadata', __NAMESPACE__ . '\\queue_generate_placeholders_on_save', 10, 2 );

/**
 * Save extracted colors to image metadata
 *
 * @param int $attachment_id Current attachment ID.
 */
function generate_placeholders( $attachment_id ) {
    $sizes = get_enabled_sizes();
    foreach ( $sizes as $size => $sample_factor ) {
        $data = generate_placeholder( $attachment_id, $size, $sample_factor );
        if ( $data ) {
            update_post_meta( $attachment_id, VRALLE_LQIP_META_PREFIX . $size, $data );
        }
    }
}
add_action( 'vll_generate_lqip', __NAMESPACE__ . '\\generate_placeholders' );

/**
 * Generate a placeholder at a given size.
 *
 * @param int    $id Attachment ID.
 * @param string $size Image size.
 * @param int    $sample_factor Scaling factor.
 * @return array|null 3-tuple of binary image data (string), width (int), height (int) on success; null on error.
 */
function generate_placeholder( $id, $size, $sample_factor ) {
    $uploads = wp_upload_dir();
    $img     = wp_get_attachment_image_src( $id, $size );
    if ( $img && $img[3] ) { // if image size exist.
        if ( strpos( $img[0], $uploads['baseurl'] ) === 0 ) { // check if local.
            $path = str_replace( $uploads['baseurl'], $uploads['basedir'], $img[0] );

            // Prevent phar deserialization vulnerability.
            if ( false !== strpos( strtolower( trim( $path ) ), 'phar://' ) ) {
                new WP_Error( 'generate_placeholder', 'phar deserialization vulnerability identified', $id );
                return null;
            }

            if ( file_exists( $path ) ) {
                return create_lqip( $path, $sample_factor );
            }
        }
    }
    return null;
}

/**
 * Get data for a file
 *
 * @param string $path Image file path.
 * @param int    $sample_factor Scaling factor.
 * @return array 3-tuple of binary image data (string), width (int), and height (int).
 */
function create_lqip( $path, $sample_factor ) {
    add_filter( 'wp_image_editors', __NAMESPACE__ . '\\add_editor' );
    $editor = wp_get_image_editor( $path );
    remove_filter( 'wp_image_editors', __NAMESPACE__ . '\\add_editor' );
    if ( ! is_wp_error( $editor ) && is_callable( array( $editor, 'create_lqip' ) ) ) {
        return $editor->create_lqip( $sample_factor );
    }

    return array();
}

/**
 * List of available image editors.
 *
 * @param array $editors List of available image editors.
 * @return $editors
 */
function add_editor( $editors ) {
    require_once VRALLE_LQIP_PLUGIN_DIR . 'includes/class-gaussholder-image-editor-imagick.php';
    if ( is_array( $editors ) ) {
        array_unshift( $editors, __NAMESPACE__ . '\\Gaussholder_Image_Editor_Imagick' );
    }

    return $editors;
}
