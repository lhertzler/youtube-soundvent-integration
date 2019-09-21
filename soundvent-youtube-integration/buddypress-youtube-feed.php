<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://lukehertzler.com/
 * @since             1.0.0
 * @package           Buddypress_Youtube_Feed
 *
 * @wordpress-plugin
 * Plugin Name:       SoundVent YouTube Integration
 * Plugin URI:        http://lukehertzler.com
 * Description:       Integrate user profiles with YouTube
 * Version:           1.0.0
 * Author:            Luke Hertzler
 * Author URI:        http://lukehertzler.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       buddypress-youtube-feed
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


/**
 * Define constants.
 * Required minimum versions, paths, urls, etc.
 *
 * @since    1.0.0
 */
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_VERSION' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_VERSION', '1.0.0' );
}
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_SLUG' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_SLUG', 'buddypress-youtube-feed' );
}
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_FILE' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_FILE', __FILE__ );
}
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_DIR' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_DIR', dirname( BUDDYPRESS_YOUTUBE_FEED_PLUGIN_FILE ) );
}
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'BUDDYPRESS_YOUTUBE_FEED_BASENAME' ) ) {
	define( 'BUDDYPRESS_YOUTUBE_FEED_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! class_exists( 'Buddypress_Youtube_Feed' ) ) :

	/**
	 * Buddypress_Youtube_Feed Class
	 *
	 * @package Buddypress_Youtube_Feed
	 * @since   1.0
	 */
	final class Buddypress_Youtube_Feed {

		/**
		 * Holds the instance
		 *
		 * Ensures that only one instance of Give_Gift_Aid exists in memory at any one
		 * time and it also prevents needing to define globals all over the place.
		 *
		 * TL;DR This is a static property property that holds the singleton instance.
		 *
		 * @var object
		 * @static
		 */
		private static $instance;

		/**
		 * The ID of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 *
		 * @var      string $plugin_name The ID of this plugin.
		 * @static
		 */
		private static $plugin_name = 'buddypress-youtube-feed';

		/**
		 * The version of this plugin.
		 *
		 * @since    1.0.0
		 * @access   private
		 *
		 * @var      string $version The current version of this plugin.
		 * @static
		 */
		private static $version = '1.0.0';

		/**
		 * Notices (array).
		 *
		 * @var array
		 */
		public static $notices = array();

		/**
		 * BuddyPress Youtube Feed Admin Object.
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @var    Buddypress_Youtube_Feed_Admin object.
		 */
		public $plugin_admin;

		/**
		 * BuddyPress Youtube Feed Frontend Object.
		 *
		 * @since  1.0
		 * @access public
		 *
		 * @var    Buddypress_Youtube_Feed_Public object.
		 */
		public $plugin_public;

		/**
		 * Get the instance and store the class inside it. This plugin utilises
		 * the PHP singleton design pattern.
		 *
		 * @since     1.0.0
		 * @static
		 * @staticvar array $instance
		 * @access    public
		 *
		 * @see       Buddypress_Youtube_Feed();
		 *
		 * @uses      Buddypress_Youtube_Feed::hooks() Setup hooks and actions.
		 * @uses      Buddypress_Youtube_Feed::includes() Loads all the classes.
		 * @uses      Buddypress_Youtube_Feed::licensing() Add BuddyPress Youtube Feed License.
		 *
		 * @return object self::$instance Instance
		 */
		public static function get_instance() {

			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Buddypress_Youtube_Feed ) ) {
				self::$instance = new Buddypress_Youtube_Feed();
				self::$instance->hooks();
				self::$instance->includes();
			}

			return self::$instance;
		}

		/**
		 * Throw error on object clone.
		 *
		 * The whole idea of the singleton design pattern is that there is a single
		 * object therefore, we don't want the object to be cloned.
		 *
		 * @since  1.0.0
		 * @access protected
		 *
		 * @return void
		 */
		public function __clone() {
			// Cloning instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'buddypress-youtube-feed' ), '1.0' );
		}

		/**
		 * Disable Unserialize of the class.
		 *
		 * @since  1.0.0
		 * @access protected
		 *
		 * @return void
		 */
		public function __wakeup() {
			// Unserialize instances of the class is forbidden.
			_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'buddypress-youtube-feed' ), '1.0' );
		}

		/**
		 * Constructor Function.
		 *
		 * @since  1.0.0
		 * @access protected
		 */
		public function __construct() {
			self::$instance = $this;
		}

		/**
		 * Reset the instance of the class
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public static function reset() {
			self::$instance = null;
		}

		/**
		 * Includes.
		 *
		 * @since  1.0.0
		 * @access private
		 */
		private function includes() {

			/**
			 * The class responsible for defining all actions that occur in the admin area.
			 */
			require_once( BUDDYPRESS_YOUTUBE_FEED_PLUGIN_DIR . '/includes/admin/class-buddypress-youtube-feed-admin.php' );

			/**
			 * The class responsible for defining all actions that occur in the public-facing
			 * side of the site.
			 */
			require_once( BUDDYPRESS_YOUTUBE_FEED_PLUGIN_DIR . '/includes/public/class-buddypress-youtube-feed-public.php' );

			/**
			 * Give - Gift Aid helper functions.
			 */
			require_once( BUDDYPRESS_YOUTUBE_FEED_PLUGIN_DIR . '/includes/buddypress-youtube-feed-helpers.php' );

			$plugin_name = self::$plugin_name;
			$version     = self::$version;

			self::$instance->plugin_admin  = new Buddypress_Youtube_Feed_Admin( $plugin_name, $version );
			self::$instance->plugin_public = new Buddypress_Youtube_Feed_Public( $plugin_name, $version );

		}

		/**
		 * Hooks.
		 *
		 * @since  1.0.0
		 * @access public
		 */
		public function hooks() {

			add_action( 'admin_init', array( $this, 'buddypress_youtube_feed_check_plugin_requirements' ) );
			add_action( 'init', array( $this, 'buddypress_youtube_feed_load_plugin_textdomain' ) );

		}


		/**
		 * Load Plugin Text Domain
		 *
		 * Looks for the plugin translation files in certain directories and loads
		 * them to allow the plugin to be localised
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool True on success, false on failure.
		 */
		public function buddypress_youtube_feed_load_plugin_textdomain() {
			// Traditional WordPress plugin locale filter.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'buddypress-youtube-feed' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'buddypress-youtube-feed', $locale );

			// Setup paths to current locale file.
			$mofile_local = trailingslashit( plugin_dir_path( __FILE__ ) . 'languages' ) . $mofile;

			if ( file_exists( $mofile_local ) ) {
				// Look in the /wp-content/plugins/give-gift-aid/languages/ folder.
				load_textdomain( 'buddypress-youtube-feed', $mofile_local );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'buddypress-youtube-feed', false, trailingslashit( plugin_dir_path( __FILE__ ) . 'languages' ) );
			}

			return false;
		}

		/**
		 * Check the plugin dependency.
		 *
		 * @since  1.0.0
		 * @access public
		 *
		 * @return bool
		 */
		public function buddypress_youtube_feed_check_plugin_requirements() {
		}



		/**
		 * Activation Check.
		 *
		 * @since 1.0.0
		 *
		 * @return bool
		 */
		public static function buddypress_youtube_feed_activation_check() {
			global $wpdb;
			$table_name = $wpdb->prefix . "bp_youtube_feed";
			if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
				$sql = "CREATE TABLE $table_name (
				id int(11) unsigned NOT NULL AUTO_INCREMENT,
				`user_id` int(11) NOT NULL,
				`channel_id` varchar(255) NOT NULL,
				`channel_name` varchar(255) NOT NULL,
				`video_id` varchar(255) NOT NULL,
				`video_title` varchar(255) NOT NULL,
				`video_description` text NOT NULL,
				`video_publish_date` varchar(255) NOT NULL,
				`video_tags` text,
				`video_duration` varchar(255) NOT NULL,
				`video_dimension` varchar(255),
				`video_definition` varchar(255),
				`video_projection` varchar(255),
				`video_viewCount` varchar(255),
				`video_likeCount` varchar(255),
				`video_dislikeCount` varchar(255),
				`video_favoriteCount` varchar(255),
				`video_commentCount` varchar(255),
				`video_thumb_default` varchar(255),
				`video_thumb_medium` varchar(255),
				`video_thumb_high` varchar(255),
				PRIMARY KEY  (id)
				);";
				require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
				dbDelta( $sql );
				//add_option( 'contact_db_version', $contact_db_version );
			}
		}

		/**
		 * Deactivation function.
		 *
		 * Delete all default templates from database.
		 *
		 * @since      1.0
		 * @access     public
		 *
		 * @return void
		 */
		public static function buddypress_youtube_feed_deactivation() {
		}


	} //End Buddypress_Youtube_Feed Class.

	/**
	 * Loads a single instance of BuddyPress Youtube Feed.
	 *
	 * This follows the PHP singleton design pattern.
	 *
	 * Use this function like you would a global variable, except without needing
	 * to declare the global.
	 *
	 * @example <?php $buddypress_youtube_feed = buddypress_youtube_feed(); ?>
	 *
	 * @since   1.0.0
	 *
	 * @see     Buddypress_Youtube_Feed::get_instance()
	 *
	 * @return object Buddypress_Youtube_Feed Returns an instance of the class
	 */
	function buddypress_youtube_feed() {
		return Buddypress_Youtube_Feed::get_instance();
	}
	/**
	 * Loads BuddyPress Youtube Feed.
	 */
	add_action( 'plugins_loaded', 'buddypress_youtube_feed' );


	register_deactivation_hook( __FILE__, array( 'Buddypress_Youtube_Feed', 'buddypress_youtube_feed_deactivation' ) );
	register_activation_hook( __FILE__, array( 'Buddypress_Youtube_Feed', 'buddypress_youtube_feed_activation_check' ) );

endif;
