<?php
/**
 * Plugin Name: Sample Structured Export
 * Plugin URI: http://github.com/wordpress-phoenix
 * Description: This plugin packages WordPress data for export and consumption via REST API endpoints in JSON format.
 * Version: 0.2.1
 * Author: Migration Minds
 * Text Domain: sample-structured-export
 * License: GNU GPL v2.0+
 *
 * @wordpress-plugin
 * @package        WordPress
 * @subpackage     sample_structured_export
 * @license        GNU GPL v2.0+
 *
 * Sample Structured Export
 *
 * Built with WP PHX WordPress Development Toolkit v3.1.0 on 25th of June 2019
 * @link           https://github.com/WordPress-Phoenix/wordpress-development-toolkit
 */

defined( 'ABSPATH' ) || die(); // WordPress must exist.

$current_dir = trailingslashit( dirname( __FILE__ ) );

/**
 * 3RD PARTY DEPENDENCIES
 * (manually include_once dependencies installed via composer for safety)
 */
if ( ! class_exists( 'WPAZ_Plugin_Base\\V_2_6\\Abstract_Plugin' ) ) {
	include_once $current_dir . 'lib/wordpress-phoenix/abstract-plugin-base/src/abstract-plugin.php';
}

/**
 * INTERNAL DEPENDENCIES (autoloader defined in main plugin class)
 */
require_once $current_dir . 'app/class-plugin.php';

Phoenix\Sample_Structured_Export\Plugin::run( __FILE__ );
