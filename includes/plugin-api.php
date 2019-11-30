<?php
/**
 * The plugin API
 *
 * @package vralle-lqip
 */

namespace VRalleLqip;

use function absint;
use function apply_filters;
use function array_keys;
use function in_array;

/**
 * Retrieve sizes to use placeholders on.
 *
 * @return array List of enabled sizes and their radii
 */
function get_enabled_sizes() {
    return apply_filters( 'gaussholder.image_sizes', array() );
}

/**
 * Get the blur radius for a given size.
 *
 * @param string $size Image size to get radius for.
 * @return int|null Radius in pixels if enabled, null if size isn't enabled.
 */
function get_blur_radius_for_size( $size ) {
    $sizes = get_enabled_sizes();
    if ( ! isset( $sizes[ $size ] ) ) {
        return null;
    }
    return absint( $sizes[ $size ] );
}

/**
 * Is the size enabled for placeholders?
 *
 * @param string $size Image size to check.
 * @return boolean True if enabled, false if not. Simple.
 */
function is_enabled_size( $size ) {
    return in_array( $size, array_keys( get_enabled_sizes() ) );
}
