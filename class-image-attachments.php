<?php
/**
 * Plugin Name.
 *
 * @package   Image_Attachments
 * @author    Andrew Heins <andrew@andrewheins.ca>
 * @license   GPL-2.0+
 * @link      http://andrewheins.ca
 * @copyright 2013 Andrew Heins
 */

/**
 * Plugin class.
 *
 * @package Image_Attachments
 * @author  Andrew Heins <andrew@andrewheins.ca>
 */
class Image_Attachments {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since   1.0.0
	 *
	 * @var     string
	 */
	protected $version = '0.0.1';

	/**
	 * Unique identifier for your plugin.
	 *
	 * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
	 * match the Text Domain file header in the main plugin file.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'image-attachments';

	/**
	 * Instance of this class.
	 *
	 * @since    0.0.1
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    0.0.1
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;
	
	
	/**
	 * List of any post types that should NEVER allow attachments
	 *
	 * @since    0.0.1
	 *
	 * @var      array
	 */
	private $forbidden_post_types = array(
		'attachment',
		'nav_menu_item',
		'acf',
		'revision',
	);

	/**
	 * Initialize the plugin by setting localization, filters, and administration functions.
	 *
	 * @since     0.0.1
	 */
	private function __construct() {

		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );

		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		
		add_action( 'add_meta_boxes', array( $this, 'display_attachments_metabox' ) );		
		
		add_action( 'wp_ajax_attach_images', array( $this, 'attach_images' ) );
		add_action( 'wp_ajax_get_all_image_attachments', array( $this, 'get_all_image_attachments' ) );
		add_action( 'wp_ajax_remove_image_attachment', array( $this, 'remove_image_attachment' ) );
		add_action( 'wp_ajax_remove_all_attachments', array( $this, 'remove_all_attachments' ) );
		
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     0.0.1
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    0.0.1
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     0.0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}
		
		// We're going to load styles globally for now, namespace everything.
		wp_enqueue_style( 
			$this->plugin_slug .'-admin-styles', 
			plugins_url( 'css/admin.css', __FILE__ ), 
			array(), 
			$this->version 
		);

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since     0.0.1
	 *
	 * @return    null    Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		// We're going to load styles globally for now, namespace everything.
		wp_enqueue_script( 
			$this->plugin_slug . '-admin-script', 
			plugins_url( 'js/admin.js', __FILE__ ), 
			array( 'jquery' ), 
			$this->version 
		);

	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.0.1
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_plugins_page(
			//__( 'Page Title', $this->plugin_slug ),
			'Attachment Images',
			//__( 'Menu Text', $this->plugin_slug ),
			'Attachment Images',
			'read',
			$this->plugin_slug,
			array( $this, 'display_plugin_admin_page' )
		);

	}



	/******************************************************
	 View Rendering Functions
	 ******************************************************/
	 
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
	
	public function post_types_section_description() {
		include_once( 'views/descriptions/post_types.php' );
	}
	
	public function post_types_section_field() {
		include_once( 'views/fields/post_types.php' );
	}



	/******************************************************
	 Settings
	 ******************************************************/
		
	public function register_plugin_settings() { 
		add_settings_section(  
	        'post_types_section',
	        'Post Types',
	        array( $this, 'post_types_section_description' ),
	        $this->plugin_slug
	    );  
	    
		add_settings_field(	
			'ai_active_post_types',
			'Active Post Types',
			array( $this, 'post_types_section_field' ),
			$this->plugin_slug,
			'post_types_section'
		);
		
		register_setting( $this->plugin_slug, 'ai_active_post_types' );
	}
	
	
	/******************************************************
	 Admin Setup
	 ******************************************************/
	
	public function display_attachments_metabox(){
	 	$active_post_types = get_option( 'ai_active_post_types' );
	 	
	 	if( false !== $active_post_types ) {
		 	foreach( $active_post_types as $k => $v ) {
		 		if( $v ) {
				 	add_meta_box(
						"image-attachments", 	// ID
						"Image Attachments", 	// Title
						array( $this, "image_attachments_metabox" ), 			// Callback
						$k,						// Post Type
						"normal", 				// Context
						"low",					// Priority
						"null"					// Callback Args
					);
				}
		 	}
	 	}
	}
	
	public function image_attachments_metabox( $post ){
		
		if( $post ) {
			include_once( 'views/image-upload-button.php' );
			echo( '<div class="attached_images">' );
			include_once( 'views/attached-images-list-with-controls.php' );
			echo( '</div>' );
		}
	
	}
	
	
	/******************************************************
	 Attachment Management Functions
	 ******************************************************/

	public function get_all_image_attachments() {
		global $wpdb;
		
		$post_id = $_POST['post_id'];
		$post = get_post( $post_id );

		echo( include( 'views/attached-images-list-with-controls.php' ) );
		die();
	}
	
	public function attach_images() {
		global $wpdb;
		
		$images = $_POST['images'];
		$post_id = $_POST['post_id'];
		
		foreach( $images as $img_id ) {
			$post = get_post( $img_id );
			$this->set_post_parent( $post, $post_id );
		}
		
		// Do Stuff 
		echo( $post_id );
		die();
	}
	
	public function remove_image_attachment() {
		global $wpdb;
		
		$attachment_id = $_POST['attachment_id'];
		$post_id = $_POST['post_id'];
		
		$post = get_post( $attachment_id );
		$post = $this->remove_post_parent( $post );		
		
		// Do Stuff 
		echo( $post->ID );
		die();
	}
	
	public function remove_all_attachments() {
		global $wpdb;
		
		$post_id = $_POST['post_id'];
		
		$attachment_args = array(
			'posts_per_page'   => -1,
			'post_type'        => 'attachment',
			'post_parent'      => $post_id,
		);
		$attachments = get_posts( $attachment_args );
		
		foreach ( $attachments as $post ) {
			$post = $this->remove_post_parent( $post );
		}

		echo( $post->ID );
		die();
	}
	
	private function set_post_parent( $post, $id ) {
		$post->post_parent = $id;
		wp_update_post( $post );
		return $post;
	}
	
	private function remove_post_parent( $post ) {
		return $this->set_post_parent( $post, null );
	}
	
}