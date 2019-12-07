<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package vralle-lqip
 */

namespace VRalleLqip;

use function add_action;
use function add_filter;
use function defined;
use function dirname;
use function file_exists;
use function get_post_meta;
use function wp_json_encode;
use SCRIPT_DEBUG;

/**
 * Add preview image data to image tag.
 *
 * @param array  $attrs    Image tag attribute list.
 * @param string $tag_name HTML tag name.
 * @param mixed  $id       The attachment ID or null if not present.
 * @param string $size     Attachment size name or null if not present.
 * @return array List of the image tag attributes.
 */
function add_lqip_attr( $attrs, $tag_name, $id, $size ) {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        return $attrs;
    }
    if ( $id && $size ) {
        if ( is_enabled_size( $size ) ) {
            if ( ! isset( $attrs['data-gaussholder'] ) ) {
                $meta = get_post_meta( absint( $id ), LQIP_META_PREFIX . $size, true );
                if ( $meta && ! empty( $meta ) ) {
                    $attrs['data-gaussholder'] = $meta;
                }
            }
        }
    }
    return $attrs;
}
add_filter( 'vll_tag_attributes', __NAMESPACE__ . '\\add_lqip_attr', 11, 4 );

/**
 * Output script onto the page.
 */
function output_script() {
    if ( function_exists( 'is_amp_endpoint' ) && is_amp_endpoint() ) {
        return;
    }
    $ext    = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '.js' : '.min.js';
    $file   = dirname( dirname( __FILE__ ) ) . '/dist/lqip' . $ext;

    // Prevent phar deserialization vulnerability.
    if ( false !== strpos( strtolower( trim( $file ) ), 'phar://' ) ) {
        return new WP_Error( 'load_js', 'phar deserialization vulnerability identified' );
    }
    $header = build_header();
    ?>
<script>
window.lazySizesConfig = window.lazySizesConfig || {};
window.lazySizesConfig.loadMode = 1;
    <?php
    echo 'var GaussholderHeader = ' . wp_json_encode( $header ) . ";\n";
    if ( file_exists( $file ) ) {
        require $file;
    }
    ?>
</script>
    <?php
}
add_action( 'wp_footer', __NAMESPACE__ . '\\output_script', 10, 1 );

/**
 * Output script onto the page.
 */
function output_styles() {
    ?>
<style>
    .guessholder-loading {
        opacity: .5;
    }
    .guessholder-loaded {
        opacity: 1;
        transition: opacity 1s;
    }
</style>
    <?php
}
add_action( 'wp_head', __NAMESPACE__ . '\\output_styles', );
