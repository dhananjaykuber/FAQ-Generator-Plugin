<?php
/**
 * Plugin Name:       Smart FAQ Generator
 * Description:       A smart FAQ generator block using Gemini AI.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            Dhananjay Kuber
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       smart-faq-generator
 *
 * @package smart-faq-generator
 */

use SmartFAQGenerator\Classes\Block;
use SmartFAQGenerator\Classes\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SMART_FAQ_GENERATOR_VERSION', '0.1.0' );
define( 'SMART_FAQ_GENERATOR_PLUGIN_URI', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'SMART_FAQ_GENERATOR_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

require_once SMART_FAQ_GENERATOR_PLUGIN_DIR . '/classes/class-block.php';
require_once SMART_FAQ_GENERATOR_PLUGIN_DIR . '/classes/class-settings.php';

new Block();
new Settings();
