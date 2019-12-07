<?php
/**
 * The plugin bootstrap file
 *
 * @package           vralle-lqip
 *
 * @wordpress-plugin
 * Plugin Name:       vralle.lqip
 * Description:       LQIP (Low Quality Image Placeholder) for vralle.lazyload. Fast and lightweight image previews, using Gaussian blur.
 * Version:           0.0.1
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

const LQIP_META_PREFIX = 'gaussholder_';
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
    die;
}

require plugin_dir_path( __FILE__ ) . 'includes/plugin-api.php';
require plugin_dir_path( __FILE__ ) . 'includes/generate-lqip.php';
require plugin_dir_path( __FILE__ ) . 'includes/image-header.php';
require plugin_dir_path( __FILE__ ) . 'includes/frontend.php';
