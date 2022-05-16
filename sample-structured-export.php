<?php
/**
 * Sample Structured Export
 *
 * @wordpress-plugin
 * @package        WordPress
 * @subpackage     sample_structured_export
 * @author         Kim Cheung - MUSE Engineering
 * @license        GNU GPL v2.0+
 * @link           http://github.com/wordpress-phoenix
 *
 * Built with WP PHX WordPress Development Toolkit v3.1.0 on Tuesday 25th of June 2019 03:13:47 PM
 * @link           https://github.com/WordPress-Phoenix/wordpress-development-toolkit
 *
 * Plugin Name: Sample Structured Export
 * Plugin URI: http://github.com/wordpress-phoenix
 * Description: This plugin is for Sample export to UDF/UCG; plugin forked from `tempo-sample-structured-export`, an exporter to UDF from Time Inc Legacy brands.
 * Version: 0.2.1
 * Author: Sample Migration Minds
 * Text Domain: sample-structured-export
 * License: GNU GPL v2.0+
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
include_once $current_dir . 'app/class-plugin.php';

Phoenix\Sample_Structured_Export\Plugin::run( __FILE__ );
