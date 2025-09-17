<?php
/**
 * Plugin Name: PDF Carousel Footer
 * Description: Displays a Slick-powered PDF carousel in the footer widget or via shortcode. Includes admin settings for URLs, size, and autoplay.
 * Version: 1.0.0
 * Author: Nikolay Djemerenov
 * Author URI: www.nikweb.eu
 * License: GPLv2 or later
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PCF_PLUGIN_FILE', __FILE__);
define('PCF_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PCF_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PCF_VERSION', '1.0.0');

// Include required files
require_once PCF_PLUGIN_DIR . 'includes/class-pdf-carousel-plugin.php';
require_once PCF_PLUGIN_DIR . 'includes/class-pdf-carousel-widget.php';

// Initialize the plugin
PDF_Carousel_Footer_Plugin::instance();


