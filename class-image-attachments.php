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

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		add_action( 'admin_init', 				array( $this, 'register_plugin_settings' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', 				array( $this, 'add_plugin_admin_menu' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', 	array( $this, 'enqueue_admin_scripts' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', 		array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', 		array( $this, 'enqueue_scripts' ) );
		
		add_action( 'add_meta_boxes', 			array( $this, 'display_attachments_metabox' ) );
		
		//add_action( 'save_post', 				array( $this, 'update_image_attachments' ) );
		//add_action( 'save_post', 				array( $this, 'save_details' ) );
		


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
	 * Fired when the plugin is activated.
	 *
	 * @since    0.0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog.
	 */
	public static function activate( $network_wide ) {
		// TODO: Define activation functionality here
	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    0.0.1
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses "Network Deactivate" action, false if WPMU is disabled or plugin is deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {
		// TODO: Define deactivation functionality here
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

		$screen = get_current_screen();
		if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'css/admin.css', __FILE__ ), array(), $this->version );
		}

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

		// $screen = get_current_screen();
		// if ( $screen->id == $this->plugin_screen_hook_suffix ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'js/admin.js', __FILE__ ), array( 'jquery' ), $this->version );
		// }

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'css/public.css', __FILE__ ), array(), $this->version );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    0.0.1
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'js/public.js', __FILE__ ), array( 'jquery' ), $this->version );
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since    0.0.1
	 */
	public function add_plugin_admin_menu() {

		/*
		 * TODO:
		 *
		 * Change 'Page Title' to the title of your plugin admin page
		 * Change 'Menu Text' to the text for menu item for the plugin settings page
		 * Change 'image-attachments' to the name of your plugin
		 */
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

	/**
	 * Render the settings page for this plugin.
	 *
	 * @since    0.0.1
	 */
	public function display_plugin_admin_page() {
		include_once( 'views/admin.php' );
	}
	
	public function post_types_section_description() {
		include_once( 'views/descriptions/post_types.php' );
	}
	
	public function post_types_section_field() {
		include_once( 'views/fields/post_types.php' );
	}
	
	
	
	public function register_plugin_settings() { 
		add_settings_section(  
	        'post_types_section',        		// ID used to identify this section and with which to register options  
	        'Post Types',                  		// Title to be displayed on the administration page  
	        array( $this, 'post_types_section_description' ), 	// Callback used to render the description of the section  
	        $this->plugin_slug                  // Page on which to add this section of options  
	    );  
	    
		add_settings_field(	
			'ai_active_post_types',				// ID used to identify the field throughout the theme
			'Active Post Types',				// The label to the left of the option interface element
			array( $this, 'post_types_section_field' ),			// The name of the function responsible for rendering the option interface
			$this->plugin_slug,					// The page on which this option will be displayed
			'post_types_section'				// The name of the section to which this field belongs
		);
		
		register_setting( $this->plugin_slug, 'ai_active_post_types' );
	}

	/**
	 * Updates image attachments for the saved post
	 *
	 * @since    0.0.1
	 */
	public function update_image_attachments() {
		// TODO: Define your action hook callback here
	}	
	
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
			include_once( 'views/attached-images-list.php' );
		}
	
	}
	
	public function save_details($post_id){
	
		global $post;
		
		if(isset($_POST['post_type']) && ($_POST['post_type'] == "csp_content_snippet")) {
			foreach($_POST as $k => $v){
				update_post_meta($post_id, $k, $v);
			}
		}
		
	}
	



}