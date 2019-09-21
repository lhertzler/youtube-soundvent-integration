<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://lukehertzler.com
 * @since      1.0.0
 *
 * @package    Buddypress_Youtube_Feed
 * @subpackage Buddypress_Youtube_Feed/includes/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Buddypress_Youtube_Feed
 * @subpackage Buddypress_Youtube_Feed/includes/public
 * @author     Luke Hertzler <lukehertzler@gmail.com>
 */
class Buddypress_Youtube_Feed_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Enqueue Script and Style for Front-end.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'bp_setup_nav', array( $this, 'add_youtube_tabs' ) );
		add_action( 'wp_ajax_my_listing_data', array( $this, 'my_listing_data' ) );
		add_action( 'wp_ajax_nopriv_my_listing_data', array( $this, 'my_listing_data' ) );
		add_action( 'wp_ajax_store_video_db_ajax', array( $this, 'store_video_db_ajax' ) );
		add_action( 'bp_setup_admin_bar', array( $this, 'youtube_bp_admin_bar_add' ) );

		add_action( 'wp_ajax_load_more_video', array( $this, 'load_more_video' ) );
		add_action( 'wp_ajax_nopriv_load_more_video', array( $this, 'load_more_video' ) );

		add_action('bp_right_sidebar', array( $this, 'channel_info'));

		add_action('yt_dash', array($this, 'youtube_settings_content_dash'));
		add_action('yt_dash', array($this, 'load_dash_videos'));
		add_action('bp_profile_header_meta', array($this, 'load_wall_video'), 2);
	}



	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_register_style( $this->plugin_name, BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/css/buddypress-youtube-feed-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name );

		wp_register_style( $this->plugin_name . '-lity', BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/css/lity.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-lity' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		global $bp;
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



		wp_register_script( $this->plugin_name, BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/js/buddypress-youtube-feed-public.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name );
		// set domain to correct path based on where we are
		wp_localize_script( $this->plugin_name, 'myAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' )
		) );


		wp_register_script( $this->plugin_name . '-lity', BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/js/lity.min.js', array( 'jquery' ), $this->version, true );
		wp_enqueue_script( $this->plugin_name . '-lity' );
	}


	public function youtube_bp_admin_bar_add() {
		global $wp_admin_bar, $bp;

		$wp_admin_bar->add_menu( array(
			'parent' => $bp->my_account_menu_id,
			'id'     => 'my-account-youtube',
			'title'  => __( 'Video', 'buddypress-youtube-feed' ),
			'href'   => $bp->loggedin_user->domain . 'video',
			'meta'   => array( 'class' => 'menupop' )
		) );
	}

	public function add_youtube_tabs() {
		global $bp;

		bp_core_new_nav_item( array(
			'name'                => 'Video',
			'slug'                => 'video',
			'parent_url'          => $bp->displayed_user->domain,
			'parent_slug'         => $bp->profile->slug,
			'screen_function'     => array( $this, 'youtube_feed_screen' ),
			'position'            => 200,
			'default_subnav_slug' => 'youtube-feed'
		) );
	}

	/*public function youtube_settings_screen() {
		add_action( 'bp_template_content', array( $this, 'youtube_settings_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}*/

	public function youtube_settings_content() {
		global $user_ID;
		if ( ! $user_ID ) {
			echo '';
		} else {
			$channel_name = get_user_meta( bp_loggedin_user_id(), 'youtube_channel_name', true );
		}
		if ( bp_loggedin_user_id() === bp_displayed_user_id() ) {
			?>
            <div class="content-youtube-feed">

                <div class="image">
                    <img src="<?php echo BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/images/YouTube-icon-full_color.png'; ?>"/>
                </div>
                <h2>Connect your YouTube account to SoundVent.<br>Enter your YouTube Channel ID.</h2>
                <div class="form">
                    <form action="" method="post" enctype="multipart/form-data" name="youtubeusernameform"
                            id="youtubeusernameform" class="youtubeusernameform">
                        <input type="text" name="youtubeusername" placeholder="YouTube Channel ID" id="youtubeusername"
                                class="youtubeusername"/>
                        <input type="submit" value="CONNECT" class="youtubesubmit"
                                style="background-color: #ff0000 !important;" id="youtubesubmit"/>

                    </form>
                </div>

                <iframe width="560" height="315" src="https://www.youtube.com/embed/7dr_DUbY9pA" frameborder="0"
                        allowfullscreen></iframe>

            </div>

            <div class="content-youtube-feed-data-result" id="youtube_result"></div>
			<?php
		}
	}

	/*
	*
	* function mods for dashboard area
	*
	*/

	public function youtube_settings_content_dash() {
		global $user_ID;
		if ( ! $user_ID ) {
			echo '';
		} else {
			$channel_name = get_user_meta( bp_loggedin_user_id(), 'youtube_channel_name', true );
		}
		//if ( bp_loggedin_user_id() === bp_displayed_user_id() ) {
			?>		<?php if(!$channel_name){ ?>
						<div class="video-dash-main">
							<img src="<?php echo BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/images/YouTube-icon-full_color.png'; ?>"/>
						</div>
					<?php } ?>
						<div class="dashboard-sidebar yt-flex">
							<div class="yt-top">
								<img src="<?php echo BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/images/pwrd_yt.png'; ?>"/>
								<p>Enter your YouTube channel ID to connect accounts.</p>

										<form action="" method="post" enctype="multipart/form-data" name="youtubeusernameform"
														id="youtubeusernameform" class="youtubeusernameform">
												<input type="text" name="youtubeusername" placeholder="<?php if($channel_name){echo $channel_name;}else{echo 'YouTube Channel ID';}?>" id="youtubeusername"
																class="youtubeusername"/>
												<input type="submit" value="CONNECT" class="youtubesubmit" id="youtubesubmit"/>

										</form>
							</div>

							<div class="yt-bottom">
								<p>For additional information on where to find your YouTube ID <a href="#">click here</a>.</p>
					</div>
				</div>
						<div class="content-youtube-feed-data-result" id="youtube_result"></div>
			<?php
		//}
	}



	public function youtube_feed_screen() {
		add_action( 'bp_template_content', array( $this, 'youtube_feed_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	public function getVideos( $channel ) {

		$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=50&part=snippet&part=statistics&channelId=' . $channel . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

		if ( is_array( $remoterequest ) ) {
			$header = $remoterequest['headers']; // array of http header lines
			$body   = $remoterequest['body']; // use the content
		}

		$video = json_decode( $body, true );

		$video_array            = array();
		$video_array_pagination = array();


		if ( ! empty( $video ) ) {
			foreach ( $video['items'] as $v_data ) {
				array_push( $video_array, $v_data );
			}

			if ( isset( $video['nextPageToken'] ) && ! empty( $video['nextPageToken'] ) ) {

				$next_page_token = $video['nextPageToken'];
				do {

					$video_data = $this->load_next_page_vidoes( $channel, $next_page_token );
					foreach ( $video_data['items'] as $next_page_video ) {
						array_push( $video_array, $next_page_video );
					}
					$next_page_token = isset( $video_data['nextPageToken'] ) ? $video_data['nextPageToken'] : '';

				} while ( $next_page_token != '' );

			}
			// Store the data.
		}


		return $video_array;
	}

	public function load_next_page_vidoes( $channel_name, $nextpage_token ) {


		if ( empty( $channel_name ) || empty( $nextpage_token ) ) {
			return false;
		}


		$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=50&part=snippet&part=statistics&channelId=' . $channel_name . '&pageToken=' . $nextpage_token . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

		if ( is_array( $remoterequest ) ) {
			$header = $remoterequest['headers']; // array of http header lines
			$body   = $remoterequest['body']; // use the content
		}


		$video_data = json_decode( $body, true );

		return $video_data;

	}


	public function youtube_feed_content() {
		global $bp;

		$channel_name = get_user_meta( bp_displayed_user_id(), 'youtube_channel_name', true );

		if ( ! empty( $channel_name ) ) {
			$this->load_videos( $channel_name );
		} else {
			if ( bp_loggedin_user_id() === bp_displayed_user_id() ) {
				echo __( 'Please enter your channel name into the feed settings.', 'buddypress-youtube-feed' );
			} else {
				echo __( 'No videos found.', 'buddypress-youtube-feed' );
			}
		}
	}


	public function youtube_screen() {
		global $bp;

		add_action( 'bp_template_title', array( $this, 'youtube_screen_title' ) );
		add_action( 'bp_template_content', array( $this, 'youtube_screen_content' ) );
		bp_core_load_template( apply_filters( 'bp_core_template_plugin', 'members/single/plugins' ) );
	}

	public function youtube_screen_title() {

	}

	public function youtube_screen_content() {

	}

	public function load_wall_video() {
		global $bp, $wpdb;
		$channel_name = get_user_meta( bp_displayed_user_id(), 'youtube_channel_name', true );
		if($channel_name){
			echo '<div class="widget"><h4>LATEST VIDEO</h4>';


		$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=25&part=snippet&part=statistics&channelId=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

		if ( is_array( $remoterequest ) ) {
			$header = $remoterequest['headers']; // array of http header lines
			$body   = $remoterequest['body']; // use the content
		}

		$json = json_decode( $body, true );

		// Get the channel details.
		$channel_details = $this->get_statics( $channel_name );

		$remoterequestchannelinfo = wp_remote_get( 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );
		if ( is_array( $remoterequestchannelinfo ) ) {
			$headerremoterequestchannelinfo = $remoterequestchannelinfo['headers']; // array of http header lines
			$bodyremoterequestchannelinfo   = $remoterequestchannelinfo['body']; // use the content
		}

		$remoterequestchannelinfojson = json_decode( $bodyremoterequestchannelinfo, true );
		//echo '<pre>';
		//print_r($remoterequestchannelinfojson);

		if ( $json['pageInfo']['totalResults'] >= 1 ) {

			?>
            <script type="text/javascript">
                $.ajax({
                    type: "post",
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: {
                        action: "store_video_db_ajax",
                        inputvalue: '<?php echo $channel_name; ?>'
                    },
                    success: function ( response ) {

                    }
                });
            </script>


						<?php
						for ( $start = 0; $start <= $json['pageInfo']['totalResults']; $start ++ ) {

							if ($start === 1){
								break;
							}
							if ( ! isset( $json['items'][ $start ]['id']['videoId'] ) ) {
								continue;
							}


							if ( isset( $json['items'][ $start ] ) ) {

								$item_data = $json['items'][ $start ]['snippet'];

								$video_meta = $this->get_video_meta( $json['items'][ $start ]['id']['videoId'] );

								$youtube_url = 'https://www.youtube.com/watch?v=' . $json['items'][ $start ]['id']['videoId'];

								$video_details = $this->get_statics( $json['items'][ $start ]['id']['videoId'], 'video' );

								$duration = new DateInterval( $video_details['items'][0]['contentDetails']['duration'] );
								?>

                                    <div class="main_video">
																			<a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
                                        <div class="video_thumbnail" style="background-image: url(<?php echo esc_url( $item_data['thumbnails']['medium']['url'] ); ?>);">

                                                <span class="video_duration"><?php echo $duration->format( '%I:%S' ); ?></span>
																								<div class="lefty">
					                                        <div class="video_title">

					                                                <h3><?php echo $item_data['title']; ?></h3>

					                                        </div>
					                                        <div class="video_meta">
					                                            <span class="video_views"><?php echo $video_meta['items'][0]['statistics']['viewCount']; ?> views</span> |
					                                            <span class="video_views"><?php echo $this->change_time( $item_data['publishedAt'] ); ?></span>
					                                        </div>
																								</div>




	                                    </div>
																			</a>
																		</div>

								<?php
							}

						}

						?>


				<?php

			//$this->store_video_db_without_ajax($channel_name);
		} else { ?>

			<?php
		}
		echo '</div>';
	}
}

	public function load_dash_videos() {
		global $bp, $wpdb;
		$channel_name = get_user_meta( bp_loggedin_user_id(), 'youtube_channel_name', true );
		$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=25&part=snippet&part=statistics&channelId=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

		if ( is_array( $remoterequest ) ) {
			$header = $remoterequest['headers']; // array of http header lines
			$body   = $remoterequest['body']; // use the content
		}

		$json = json_decode( $body, true );

		// Get the channel details.
		$channel_details = $this->get_statics( $channel_name );

		$remoterequestchannelinfo = wp_remote_get( 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );
		if ( is_array( $remoterequestchannelinfo ) ) {
			$headerremoterequestchannelinfo = $remoterequestchannelinfo['headers']; // array of http header lines
			$bodyremoterequestchannelinfo   = $remoterequestchannelinfo['body']; // use the content
		}

		$remoterequestchannelinfojson = json_decode( $bodyremoterequestchannelinfo, true );
		//echo '<pre>';
		//print_r($remoterequestchannelinfojson);
		?>
				<?php if($channel_name){ ?>
        	<div class="dash-yt-top-bar">
							<a target="_blank" href="<?php echo esc_url( 'https://www.youtube.com/channel/' . $channel_name ); ?>">
									Connected to: <?php echo $remoterequestchannelinfojson['items'][0]['snippet']['title']; ?>
								</a>
                <p class="sub-count"><?php echo number_format($channel_details['subscriberCount']); ?> Subscribers</p>
            </div>
		<?php }

		if ( $json['pageInfo']['totalResults'] >= 1 ) {

			?>
            <script type="text/javascript">
                $.ajax({
                    type: "post",
                    url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
                    data: {
                        action: "store_video_db_ajax",
                        inputvalue: '<?php echo $channel_name; ?>'
                    },
                    success: function ( response ) {

                    }
                });
            </script>
                <div class="row video_container">

						<?php
						for ( $start = 0; $start <= $json['pageInfo']['totalResults']; $start ++ ) {

							if ( ! isset( $json['items'][ $start ]['id']['videoId'] ) ) {
								continue;
							}

							if ( isset( $json['items'][ $start ] ) ) {

								$item_data = $json['items'][ $start ]['snippet'];

								$video_meta = $this->get_video_meta( $json['items'][ $start ]['id']['videoId'] );

								$youtube_url = 'https://www.youtube.com/watch?v=' . $json['items'][ $start ]['id']['videoId'];

								$video_details = $this->get_statics( $json['items'][ $start ]['id']['videoId'], 'video' );

								$duration = new DateInterval( $video_details['items'][0]['contentDetails']['duration'] );
								?>
                                <div class="small-12 medium-6 large-3 columns">
                                    <div class="main_video">
																			<a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
                                        <div class="video_thumbnail" style="background-image: url(<?php echo esc_url( $item_data['thumbnails']['medium']['url'] ); ?>);">

                                                <span class="video_duration"><?php echo $duration->format( '%I:%S' ); ?></span>
																								<div class="lefty">
					                                        <div class="video_title">

					                                                <h3><?php echo $item_data['title']; ?></h3>

					                                        </div>
					                                        <div class="video_meta">
					                                            <span class="video_views"><?php echo $video_meta['items'][0]['statistics']['viewCount']; ?> views</span> |
					                                            <span class="video_views"><?php echo $this->change_time( $item_data['publishedAt'] ); ?></span>
					                                        </div>
																								</div>




	                                    </div>
																			</a>
																		</div>
                                </div>
								<?php
							}

						}

						?>
                    </div>
					<?php
					if ( $json['nextPageToken'] ) {
						?>
                        <div class="load-more-button-container">
                            <button type="button" name="nextPage" id="load-more-videos"
                                    data-channel_name="<?php echo $channel_name; ?>"
                                    data-nextPagetoken="<?php echo $json['nextPageToken']; ?>">
																		Load More
                            </button>
                        </div>
						<?php
					}
					?>

				<?php

			//$this->store_video_db_without_ajax($channel_name);
		} else { ?>

			<?php
		}
	}


	public function load_videos( $channel_name ) {
		global $bp, $wpdb;

		$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=7&part=snippet&part=statistics&channelId=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

		if ( is_array( $remoterequest ) ) {
			$header = $remoterequest['headers']; // array of http header lines
			$body   = $remoterequest['body']; // use the content
		}

		$json = json_decode( $body, true );

		// Get the channel details.
		$channel_details = $this->get_statics( $channel_name );

		$remoterequestchannelinfo = wp_remote_get( 'https://www.googleapis.com/youtube/v3/channels?part=snippet&id=' . $channel_name . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );
		if ( is_array( $remoterequestchannelinfo ) ) {
			$headerremoterequestchannelinfo = $remoterequestchannelinfo['headers']; // array of http header lines
			$bodyremoterequestchannelinfo   = $remoterequestchannelinfo['body']; // use the content
		}

		$remoterequestchannelinfojson = json_decode( $bodyremoterequestchannelinfo, true );
		//echo '<pre>';
		//print_r($remoterequestchannelinfojson);
		?>
		<div class="card channel-info">
        <div class="youtube-top-bar">
                <div class="powered-by">
                    <img src="<?php echo BUDDYPRESS_YOUTUBE_FEED_PLUGIN_URL . 'assets/images/pwrdByYT-black.png'; ?>"/>
                </div>
							</div>
						</div>
						<div class="card channel-info">
				        <div class="youtube-top-bar">
                <div class="channel_widget_card">
					<?php /*if ( bp_loggedin_user_id() === bp_displayed_user_id() ) { ?>
                        <a href="<?php echo esc_url( $bp->displayed_user->domain . 'youtube/youtube-settings/' ) ?>"
                                class="fa fa-cog settings"></a>
					<?php } */ ?>
					<div class="left"><i class="fab fa-youtube"></i></div>
					<div class="right">
                    <a target="_blank"
                            href="<?php echo esc_url( 'https://www.youtube.com/channel/' . $channel_name ); ?>"><span
                                class="channel_title"><?php echo $remoterequestchannelinfojson['items'][0]['snippet']['title']; ?></span></a>
                    <span class="channel_subscribers"><?php echo number_format($channel_details['subscriberCount']) . ' Subscribers'; ?></span>
            </div>
					</div>
        </div>
			</div>
			<script>
			jQuery(document).ready(function(){
				jQuery('.card.channel-info').prependTo('.profile-right-sidebar').css('display', 'flex');
			});
			</script>
				<?php

				if ( $json['pageInfo']['totalResults'] >= 1 ) {

					?>
								<script type="text/javascript">
										$.ajax({
												type: "post",
												url: '<?php echo admin_url( 'admin-ajax.php' ); ?>',
												data: {
														action: "store_video_db_ajax",
														inputvalue: '<?php echo $channel_name; ?>'
												},
												success: function ( response ) {

												}
										});
								</script>
										<div class="row video_container">

								<?php
								for ( $start = 0; $start <= $json['pageInfo']['totalResults']; $start ++ ) {

									if ( ! isset( $json['items'][ $start ]['id']['videoId'] ) ) {
										continue;
									}

									if ( isset( $json['items'][ $start ] ) ) {

										$item_data = $json['items'][ $start ]['snippet'];

										$video_meta = $this->get_video_meta( $json['items'][ $start ]['id']['videoId'] );

										$youtube_url = 'https://www.youtube.com/watch?v=' . $json['items'][ $start ]['id']['videoId'];

										$video_details = $this->get_statics( $json['items'][ $start ]['id']['videoId'], 'video' );

										$duration = new DateInterval( $video_details['items'][0]['contentDetails']['duration'] );

										?>
																		<div class="<?php if($start === 0){echo 'small-12 ';}?>columns">
																				<div class="main_video">
																					<a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
																						<div class="video_thumbnail" style="background-image: url(<?php echo esc_url( $item_data['thumbnails']['medium']['url'] ); ?>);">

																										<span class="video_duration"><?php echo $duration->format( '%I:%S' ); ?></span>
																										<div class="lefty">
																											<div class="video_title">

																															<h3><?php echo $item_data['title']; ?></h3>

																											</div>
																											<div class="video_meta">
																													<span class="video_views"><?php echo $video_meta['items'][0]['statistics']['viewCount']; ?> views</span> |
																													<span class="video_views"><?php echo $this->change_time( $item_data['publishedAt'] ); ?></span>
																											</div>
																										</div>




																					</div>
																					</a>
																				</div>
																		</div>
										<?php
									}

								}

								?>
												</div>
							<?php
							if ( $json['nextPageToken'] ) {
								?>
														<div class="load-more-button-container">
																<button type="button" name="nextPage" id="load-more-videos"
																				data-channel_name="<?php echo $channel_name; ?>"
																				data-nextPagetoken="<?php echo $json['nextPageToken']; ?>">
																				Load More
																</button>
														</div>
								<?php
							}
							?>

						<?php

					//$this->store_video_db_without_ajax($channel_name);
				} else { ?>
								<li>No Videos found.</li>
					<?php
				}
	}

	public function get_statics( $id, $type = 'channel' ) {

		if ( $type == 'channel' ) {

			$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/channels?part=statistics&id=' . $id . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );
			if ( is_array( $remoterequest ) ) {
				$header = $remoterequest['headers']; // array of http header lines
				$body   = $remoterequest['body']; // use the content
			}
			$json = json_decode( $body, true );

			if ( ! empty( $json['items'][0] ) ) {
				return $json['items'][0]['statistics'];
			}

		} else if ( $type == 'video' ) {

			$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/videos?part=snippet,contentDetails,statistics,status&id=' . $id . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );
			if ( is_array( $remoterequest ) ) {
				$header = $remoterequest['headers']; // array of http header lines
				$body   = $remoterequest['body']; // use the content
			}
			$json = json_decode( $body, true );

			if ( ! empty( $json ) ) {
				return $json;
			}

		}

		return false;

	}

	public function change_time( $timestring ) {

		if ( ! empty( $timestring ) ) {

			$time = date( 'm/d/y', strtotime( $timestring ) );

			return $time;
		}

		return false;
	}

	public function my_listing_data() {

		global $user_ID;

		if ( isset( $_POST['inputvalue'] ) ) {

			$channel_name = $_POST['inputvalue'];
			update_user_meta( bp_loggedin_user_id(), 'youtube_channel_name', $channel_name );
			$this->load_videos( $channel_name );

		} else {
			wp_die();
		}


		wp_die();
	}

	public function load_more_video() {

		if ( isset( $_POST['nextToken'] ) ) {

			$channel_name = $_POST['channel_name'];

			$remoterequest = wp_remote_get( 'https://www.googleapis.com/youtube/v3/search?order=date&maxResults=4&part=snippet&part=statistics&channelId=' . $channel_name . '&pageToken=' . $_POST['nextToken'] . '&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc' );

			if ( is_array( $remoterequest ) ) {
				$header = $remoterequest['headers']; // array of http header lines
				$body   = $remoterequest['body']; // use the content
			}

			$video_data = json_decode( $body, true );


			//echo '<pre>';
			//print_r($video_data);

			if ( count( $video_data['items'] ) > 0 ) {


				ob_start();


				foreach ( $video_data['items'] as $video ) {


					if ( ! isset( $video['id']['videoId'] ) ) {
						continue;
					}

					$item_data = $video['snippet'];

					$video_meta    = $this->get_video_meta( $video['id']['videoId'] );
					$video_details = $this->get_statics( $video['id']['videoId'], 'video' );
					$youtube_url   = 'https://www.youtube.com/watch?v=' . $video['id']['videoId'];

					$video_details = $this->get_statics( $video['id']['videoId'], 'video' );

					$duration = new DateInterval( $video_details['items'][0]['contentDetails']['duration'] );

					if(strpos($_SERVER['REQUEST_URI'], "/dashboard") !== false){
						?>
						<script>console.log('3573');</script>
						<div class="small-12 medium-6 large-3 columns">
								<div class="main_video">
									<a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
										<div class="video_thumbnail" style="background-image: url(<?php echo esc_url( $item_data['thumbnails']['medium']['url'] ); ?>);">

														<span class="video_duration"><?php echo $duration->format( '%I:%S' ); ?></span>
														<div class="lefty">
															<div class="video_title">

																			<h3><?php echo $item_data['title']; ?></h3>

															</div>
															<div class="video_meta">
																	<span class="video_views"><?php echo $video_meta['items'][0]['statistics']['viewCount']; ?> views</span> |
																	<span class="video_views"><?php echo $this->change_time( $item_data['publishedAt'] ); ?></span>
															</div>
														</div>




									</div>
									</a>
								</div>
						</div>
						<?php
					} else {
					?>

                    <div class="single_recent_video">
                        <div class="main_video">
                            <div class="video_thumbnail">
                                <a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
                                    <img src="<?php echo esc_url( $item_data['thumbnails']['medium']['url'] ); ?>"
                                            class="vid_image"/>
                                    <span class="video_duration"><?php echo $duration->format( '%I:%S' ); ?></span>
                                </a>
                            </div>
                            <div class="video_title">
                                <a href="<?php echo esc_url( $youtube_url ); ?>" data-lity>
                                    <h3><?php echo esc_html( $item_data['title'] ); ?></h3>
                                </a>
                            </div>
                            <div class="video_meta">
                                <span class="video_views"><?php echo $video_meta['items'][0]['statistics']['viewCount']; ?> views</span> |
                                <span class="video_views"><?php echo $this->change_time( $item_data['publishedAt'] ); ?></span>
                            </div>
                        </div>
                    </div>
					<?php
				}
				}

				// Get the html.
				$html_content = ob_get_clean();

				echo wp_json_encode( array(
					'html' => $html_content,
					'next' => isset( $video_data['nextPageToken'] ) ? $video_data['nextPageToken'] : '',
				) );

			} else {

				echo wp_json_encode( array(
					'html' => '',
					'next' => 0,
				) );
			}

		}
		wp_die();
	}


	public function get_video_meta( $video_id ) {

		if ( empty( $video_id ) ) {
			return false;
		}
		$video_meta_request = wp_remote_get( "https://www.googleapis.com/youtube/v3/videos?part=statistics&id=" . $video_id . "&key=AIzaSyAuz2nlLozYUJy6da-pg91oZoYY_0noXZc" );

		$return_data = wp_remote_retrieve_body( $video_meta_request );

		return json_decode( $return_data, true );
	}

	public function store_video_db_without_ajax($channel_name) {
		global $bp, $wpdb;
		if ( is_user_logged_in() ) {
			$videos = $this->getVideos( $channel_name );
			//echo '<pre>';
			//print_r( $videos );
			//echo '</pre>';

			$query = "DELETE FROM " . $wpdb->prefix . "bp_youtube_feed WHERE user_id=" . bp_loggedin_user_id();
			$wpdb->query( $query );


			foreach ( $videos as $videosingle ) {

				$user_id            = bp_loggedin_user_id();
				$channel_id         = $videosingle['snippet']['channelId'];
				$channel_name       = $videosingle['snippet']['channelTitle'];
				$video_id           = $videosingle['id']['videoId'];
				$video_title        = $videosingle['snippet']['title'];
				$video_description  = $videosingle['snippet']['description'];
				$video_publish_date = $this->change_time( $videosingle['snippet']['publishedAt'] );

				$video_details_store = $this->get_statics( $video_id, 'video' );

				//							echo '<pre>';
				//							print_r( $video_details_store );
				//							echo '</pre>';

				$video_tags = implode( ", ", $video_details_store['items'][0]['snippet']['tags'] );

				preg_match_all('/(\d+)/',$video_details_store['items'][0]['contentDetails']['duration'],$parts);
				//$duration            = new DateInterval( $video_details_store['items'][0]['contentDetails']['duration'] );
				$video_duration      = $parts[0][0].":".$parts[0][1].":".$parts[0][2];
				$video_dimension     = $video_details_store['items'][0]['contentDetails']['dimension'];
				$video_definition    = $video_details_store['items'][0]['contentDetails']['definition'];
				$video_projection    = $video_details_store['items'][0]['contentDetails']['projection'];
				$video_viewCount     = $video_details_store['items'][0]['statistics']['viewCount'];
				$video_likeCount     = $video_details_store['items'][0]['statistics']['likeCount'];
				$video_dislikeCount  = $video_details_store['items'][0]['statistics']['dislikeCount'];
				$video_favoriteCount = $video_details_store['items'][0]['statistics']['favoriteCount'];
				$video_commentCount  = $video_details_store['items'][0]['statistics']['commentCount'];
				$video_thumb_default = $videosingle['snippet']['thumbnails']['default']['url'];
				$video_thumb_medium  = $videosingle['snippet']['thumbnails']['medium']['url'];
				$video_thumb_high    = $videosingle['snippet']['thumbnails']['high']['url'];

				if (isset($video_id) && !empty($video_id)) {

					$wpdb->insert( 'sv_bp_youtube_feed', array(
						'user_id'             => $user_id,
						'channel_id'          => "$channel_id",
						'channel_name'        => "$channel_name",
						'video_id'            => "$video_id",
						'video_title'         => "$video_title",
						'video_description'   => "$video_description",
						'video_publish_date'  => "$video_publish_date",
						'video_tags'          => "$video_tags",
						'video_duration'      => "$video_duration",
						'video_dimension'     => "$video_dimension",
						'video_definition'    => "$video_definition",
						'video_projection'    => "$video_projection",
						'video_viewCount'     => "$video_viewCount",
						'video_likeCount'     => "$video_likeCount",
						'video_dislikeCount'  => "$video_dislikeCount",
						'video_favoriteCount' => "$video_favoriteCount",
						'video_commentCount'  => "$video_commentCount",
						'video_thumb_default' => "$video_thumb_default",
						'video_thumb_medium'  => "$video_thumb_medium",
						'video_thumb_high'    => "$video_thumb_high"
					), array(
						'%d',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s',
						'%s'
					) );
				}
			}
		}
    }

		public function prof_vid() {
			add_action('wp_loaded', 'load_wall_video');
		}

	public function store_video_db_ajax() {
		global $bp, $wpdb;

		$channel_name = $_POST['inputvalue'];

		//echo $channel_name;

		if ( is_user_logged_in() ) {
			$videos = $this->getVideos( $channel_name );
			//echo '<pre>';
			//print_r( $videos );
			//echo '</pre>';

			$query = "DELETE FROM " . $wpdb->prefix . "bp_youtube_feed WHERE user_id=" . bp_loggedin_user_id();
			$wpdb->query( $query );


			foreach ( $videos as $videosingle ) {

				$user_id            = bp_loggedin_user_id();
				$channel_id         = $videosingle['snippet']['channelId'];
				$channel_name       = $videosingle['snippet']['channelTitle'];
				$video_id           = $videosingle['id']['videoId'];
				$video_title        = $videosingle['snippet']['title'];
				$video_description  = $videosingle['snippet']['description'];
				$video_publish_date = $this->change_time( $videosingle['snippet']['publishedAt'] );

				$video_details_store = $this->get_statics( $video_id, 'video' );

				//							echo '<pre>';
				//							print_r( $video_details_store );
				//							echo '</pre>';

				$video_tags = implode( ", ", $video_details_store['items'][0]['snippet']['tags'] );

				preg_match_all('/(\d+)/',$video_details_store['items'][0]['contentDetails']['duration'],$parts);
				//$duration            = new DateInterval( $video_details_store['items'][0]['contentDetails']['duration'] );
				$video_duration      = $parts[0][0].":".$parts[0][1].":".$parts[0][2];
				$video_dimension     = $video_details_store['items'][0]['contentDetails']['dimension'];
				$video_definition    = $video_details_store['items'][0]['contentDetails']['definition'];
				$video_projection    = $video_details_store['items'][0]['contentDetails']['projection'];
				$video_viewCount     = $video_details_store['items'][0]['statistics']['viewCount'];
				$video_likeCount     = $video_details_store['items'][0]['statistics']['likeCount'];
				$video_dislikeCount  = $video_details_store['items'][0]['statistics']['dislikeCount'];
				$video_favoriteCount = $video_details_store['items'][0]['statistics']['favoriteCount'];
				$video_commentCount  = $video_details_store['items'][0]['statistics']['commentCount'];
				$video_thumb_default = $videosingle['snippet']['thumbnails']['default']['url'];
				$video_thumb_medium  = $videosingle['snippet']['thumbnails']['medium']['url'];
				$video_thumb_high    = $videosingle['snippet']['thumbnails']['high']['url'];

				if (isset($video_id) && !empty($video_id)) {

					$wpdb->insert( 'sv_bp_youtube_feed', array(
							'user_id'             => $user_id,
							'channel_id'          => "$channel_id",
							'channel_name'        => "$channel_name",
							'video_id'            => "$video_id",
							'video_title'         => "$video_title",
							'video_description'   => "$video_description",
							'video_publish_date'  => "$video_publish_date",
							'video_tags'          => "$video_tags",
							'video_duration'      => "$video_duration",
							'video_dimension'     => "$video_dimension",
							'video_definition'    => "$video_definition",
							'video_projection'    => "$video_projection",
							'video_viewCount'     => "$video_viewCount",
							'video_likeCount'     => "$video_likeCount",
							'video_dislikeCount'  => "$video_dislikeCount",
							'video_favoriteCount' => "$video_favoriteCount",
							'video_commentCount'  => "$video_commentCount",
							'video_thumb_default' => "$video_thumb_default",
							'video_thumb_medium'  => "$video_thumb_medium",
							'video_thumb_high'    => "$video_thumb_high"
						), array(
							'%d',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s',
							'%s'
						) );
				}
			}
		}

		wp_die();
	}

}
