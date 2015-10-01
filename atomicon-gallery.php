<?php
/*
Plugin Name: Atomicon Gallery
Plugin URI: http://www.atomicon.nl
Description: Atomicon Gallery Description
Version: 1.0
Author: Yvo van Dillen
Author URI: http://www.atomicon.nl/about
Author Email: info@atomicon.nl
License:

  Copyright 2015 Atomicon (info@atomicon.nl)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class Atomicon_Gallery {

	/*--------------------------------------------*
	 * Attributes
	 *--------------------------------------------*/

	/** Refers to a single instance of this class. */
	private static $instance = null;

	/** Atomicon Gallery Core */

	private $core;

	/** Refers to the slug of the plugin screen. */
	private $plugin_screen_slug = null;

	private $messages = null;

	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/

	/**
	 * Creates or returns an instance of this class.
	 *
	 * @return	Atomicon Gallery	A single instance of this class.
	 */
	public function get_instance() {
		return null == self::$instance ? new self : self::$instance;
	} // end get_instance;

	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	private function __construct() {
		$this->messages = array();

		// Get the base directories
		$wp_upload_dir = wp_upload_dir();

		// Load the core
		include dirname(__FILE__).'/inc/atomicon-gallery-core.php';
		$this->core = new Atomicon_Gallery_Core($wp_upload_dir['basedir'], $wp_upload_dir['baseurl']);

		// Load plugin text domain
		add_action( 'init', array( $this, 'plugin_textdomain' ) );

	    // Add the options page and menu item.
	    add_action( 'admin_menu', array( $this, 'plugin_admin_menu' ) );

	    // Register admin styles and scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_admin_scripts' ) );

		// Register site stylesheets and JavaScript
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_scripts' ) );

		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );

	    add_action( 'plugins_loaded', array( $this, 'atomicon_gallery_handle_upload' ) );
	    // add_filter( 'my_filter', array( $this, 'filter_method_name' ) );

	} // end constructor

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function activate( $network_wide ) {
		// Define activation functionality here
	} // end activate

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param	boolean	$network_wide	True if WPMU superadmin uses "Network Activate" action, false if WPMU is disabled or plugin is activated on an individual blog
	 */
	public function deactivate( $network_wide ) {
		// Define deactivation functionality here
	} // end deactivate

	/**
	 * Loads the plugin text domain for translation
	 */
	public function plugin_textdomain() {

		$domain = 'atomicon-gallery';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

        load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
        load_plugin_textdomain( $domain, FALSE, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );

	} // end plugin_textdomain

	/**
	 * Registers and enqueues admin-specific styles.
	 */
	public function register_admin_styles() {

		/*
		 * Check if the plugin has registered a settings page
		 * and if it has, make sure only to enqueue the scripts on the relevant screens
		 */

	    if ( isset( $this->plugin_screen_slug ) ){

			 $screen = get_current_screen();
			 if ( $screen->id == $this->plugin_screen_slug ) {
			 	wp_enqueue_style( 'atomicon-gallery-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ) );
			 } // end if

	    } // end if

	} // end register_admin_styles

	/**
	 * Registers and enqueues admin-specific JavaScript.
	 */
	public function register_admin_scripts() {

		/*
		 * Check if the plugin has registered a settings page
		 * and if it has, make sure only to enqueue the scripts on the relevant screens
		 */

	    if ( isset( $this->plugin_screen_slug ) ){

			 $screen = get_current_screen();
			 if ( $screen->id == $this->plugin_screen_slug ) {

				wp_enqueue_script( 'jquery' );
				//if action == upload
				wp_enqueue_script( 'atomicon-gallery-jquery-knob', plugins_url( 'assets/js/jquery.knob.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'atomicon-gallery-jquery-ui-widget', plugins_url( 'assets/js/jquery.ui.widget.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'atomicon-gallery-jquery-iframe-transport', plugins_url( 'assets/js/jquery.iframe-transport.js', __FILE__ ), array( 'jquery' ) );
				wp_enqueue_script( 'atomicon-gallery-jquery-fileupload', plugins_url( 'assets/js/jquery.fileupload.js', __FILE__ ), array( 'jquery' ) );

			 	wp_enqueue_script( 'atomicon-gallery-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ) );
			 } // end if

	    } // end if

	} // end register_admin_scripts

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
		wp_enqueue_style( 'atomicon-gallery-styles', plugins_url( 'css/display.css', __FILE__ ) );
	} // end register_plugin_styles

	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
		wp_enqueue_script( 'atomicon-gallery-script', plugins_url( 'js/display.js', __FILE__ ), array( 'jquery' ) );
	} // end register_plugin_scripts

	/**
	 * Registers the administration menu for this plugin into the WordPress Dashboard menu.
	 */
	public function plugin_admin_menu() {

    	$this->plugin_screen_slug = add_plugins_page(
    		__( 'Atomicon Gallery', 'atomicon-gallery' ),	// page
    		__( 'Atomicon Gallery', 'atomicon-gallery' ),	// menu
    		__( 'manage_options', 'atomicon-gallery'), // capabilities
    		__( 'atomicon-gallery', 'atomicon-gallery' ),	// slug
    		array( $this, 'admin_controller' )	// callback
    	);

	} // end plugin_admin_menu

	/**
	 * Renders the options page for this plugin.
	 */
	public function admin_controller() {

		$this->action = str_replace('-', '_', (isset($_REQUEST['action']) ? $_REQUEST['action'] : 'listing'));
		$this->folder = isset($_GET['folder']) ? $_GET['folder'] : '';

		if ( ! method_exists($this, 'admin_'.$this->action)) {
			$this->add_message( sprintf(__('Unknown action <strong>%s</strong>', 'atomicon-gallery'), esc_html($this->action) ), 'error' );
			$this->action = 'listing';
		}
		//$this->action = 'upload';
		return call_user_func(array($this, 'admin_'.$this->action));
	} // end admin_controller

	public function admin_listing() {

		add_thickbox();
		include( 'views/admin-listing.php' );
	}

	public function admin_delete() {
		var_dump($this->folder, $_POST, $this->core->all($this->folder));
	}

	public function admin_create_folder() {
		$folder_name = '';
		if (isset($_POST['folder-name']))
		{
			$folder_name = $_POST['folder-name'];
			$result = $this->core->create_folder($this->folder, $folder_name);
			switch($result) {
				case TRUE:
						$info_messages[] = __('Folder created', 'atomicon-gallery');
						$_POST['action'] = 'listing';
						$this->admin_controller();
						break;
				case -1:
						$error_messages[]  = __('Can not create empty folder', 'atomicon-gallery');
						break;
				case -2:
						$error_messages[]  = __('Folder already exists', 'atomicon-gallery');
						break;
				case -3:
						$error_messages[]  = __('Could not create folder', 'atomicon-gallery');
						break;
				default:
						$error_messages[]  = __('Unknown error occurred', 'atomicon-gallery');
						break;
			}
			if ($result === TRUE) {

			}
		}
		include( 'views/admin-create-folder.php' );
	}

	public function admin_create_thumbnails() {
		var_dump($this->folder, $_POST, $this->core->all($this->folder));
	}

	public function admin_upload() {

		if(isset($_FILES['upl']) && $_FILES['upl']['error'] == 0){
			print_r($_FILES);
			exit;
		}

		include( 'views/admin-upload.php' );
	}

	public function atomicon_gallery_handle_upload() {

		if (isset($_GET['action']) && $_GET['action'] == 'atomicon-gallery-handle-upload') {

			if (is_admin() && current_user_can('manage_options')) {


				$allowed = array('png', 'jpg', 'gif','zip');

				$extension = pathinfo($_FILES['upl']['name'], PATHINFO_EXTENSION);

				if(!in_array(strtolower($extension), $allowed)){
					echo '{"status":"error"}';
					exit;
				}

				$this->folder = isset($_GET['folder']) ? $_GET['folder'] : '';
				$path = $this->core->real_folder($this->folder).'/'.$_FILES['upl']['name'];

				if(move_uploaded_file($_FILES['upl']['tmp_name'], $path)){
					echo '{"status":"success"}';
					exit;
				}
			}

			echo '{"status":"error"}';
			exit;
		}
	}

	/*--------------------------------------------*
	 * Core Functions
	 *---------------------------------------------*/

    public function add_message($message, $type = 'updated') {
    	$this->messages[] = compact('message', 'type');
    }

	public function admin_url($data = array()) {
		$data = is_array($data) ? $data : array();
		$current_screen = get_current_screen();

		if (!isset($data['page'])) {
			$data['page'] = isset($_GET['page']) ? $_GET['page'] : 'atomicon-gallery';
		}

		if (!isset($data['folder'])) {
			$data['folder'] = isset($_GET['folder']) ? $_GET['folder'] : '';
		}

		return get_admin_url().$current_screen->parent_file.'?'.http_build_query($data);
	}

} // end class

Atomicon_Gallery::get_instance();