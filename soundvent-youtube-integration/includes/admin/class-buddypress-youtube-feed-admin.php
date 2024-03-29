<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://lukehertzler.com
 * @since      1.0.0
 *
 * @package    Buddypress_Youtube_Feed
 * @subpackage Buddypress_Youtube_Feed/includes/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Youtube_Feed
 * @subpackage Buddypress_Youtube_Feed/includes/admin
 * @author     Luke Hertzler <lukehertzler@gmail.com>
 */
class Buddypress_Youtube_Feed_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		// Enqueue Script and Style for Admin.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Youtube_Feed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Youtube_Feed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_style( $this->plugin_name, BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/css/buddypress-youtube-feed-admin.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Buddypress_Youtube_Feed_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Buddypress_Youtube_Feed_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_register_script( $this->plugin_name, BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/js/buddypress-youtube-feed-admin.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name );
	}

}
