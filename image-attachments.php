<?php
/**
 * The WordPress Plugin Boilerplate.
 *
 * A foundation off of which to build well-documented WordPress plugins that also follow
 * WordPress coding standards and PHP best practices.
 *
 * @package   Image_Attachments
 * @author    Andrew Heins <andrew@andrewheins.ca>
 * @license   GPL-2.0+
 * @link      http://andrewheins.ca
 * @copyright 2013 Andrew Heins
 *
 * @wordpress-plugin
 * Plugin Name: Image Attachments
 * Plugin URI:  http://andrewheins.ca/
 * Description: Allows easy attachment of images to posts
 * Version:     0.0.1
 * Author:      Andrew Heins
 * Author URI:  http://andrewheins.ca
 * Text Domain: image-attachments-locale
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path: /lang
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// TODO: replace `class-image-attachments.php` with the name of the actual plugin's class file
require_once( plugin_dir_path( __FILE__ ) . 'class-image-attachments.php' );

// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
// TODO: replace Image_Attachments with the name of the plugin defined in `class-image-attachments.php`
register_activation_hook( __FILE__, array( 'Image_Attachments', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'Image_Attachments', 'deactivate' ) );

// TODO: replace Image_Attachments with the name of the plugin defined in `class-image-attachments.php`
Image_Attachments::get_instance();