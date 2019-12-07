<?php
/**
 * The plugin bootstrap file
 *
 * @package           vralle-lqip
 *
 * @wordpress-plugin
 * Plugin Name:       vralle.lqip
 * Version:           0.0.1
 * Description:       LQIP (Low Quality Image Placeholder) for vralle.lazyload. Fast and lightweight image previews.
 * Plugin URI:        https://github.com/vralle/vralle-lqip
 * Author:            V.Ralle
 * Author URI:        https://github.com/vralle
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       vralle-lqip
 * Domain Path:       /languages
 * Requires WP:       4.9
 * Requires PHP:      5.6
 * GitHub Plugin URI: https://github.com/vralle/vralle-lqip
 **/

namespace VRalleLqip;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

define( 'VRALLE_LQIP_META_PREFIX', 'gaussholder_' );
define( 'VRALLE_LQIP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require VRALLE_LQIP_PLUGIN_DIR . 'includes/plugin-api.php';
require VRALLE_LQIP_PLUGIN_DIR . 'includes/generate-lqip.php';
require VRALLE_LQIP_PLUGIN_DIR . 'includes/image-header.php';
require VRALLE_LQIP_PLUGIN_DIR . 'includes/frontend.php';
