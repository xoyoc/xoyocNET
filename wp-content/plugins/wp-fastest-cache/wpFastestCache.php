<?php
/*
Plugin Name: WP Fastest Cache
Plugin URI: http://wordpress.org/plugins/wp-fastest-cache/
Description: The simplest and fastest WP Cache system
Version: 0.8.5.8
Author: Emre Vona
Author URI: http://tr.linkedin.com/in/emrevona
Text Domain: wp-fastest-cache
Domain Path: /languages/

Copyright (C)2013 Emre Vona

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/ 
	if (!defined('WPFC_WP_CONTENT_BASENAME')) {
		if (!defined('WPFC_WP_PLUGIN_DIR')) {
			if(preg_match("/(\/trunk\/|\/wp-fastest-cache\/)$/", plugin_dir_path( __FILE__ ))){
				define("WPFC_WP_PLUGIN_DIR", preg_replace("/(\/trunk\/|\/wp-fastest-cache\/)$/", "", plugin_dir_path( __FILE__ )));
			}else if(preg_match("/\\\wp-fastest-cache\/$/", plugin_dir_path( __FILE__ ))){
				//D:\hosting\LINEapp\public_html\wp-content\plugins\wp-fastest-cache/
				define("WPFC_WP_PLUGIN_DIR", preg_replace("/\\\wp-fastest-cache\/$/", "", plugin_dir_path( __FILE__ )));
			}
		}
		define("WPFC_WP_CONTENT_DIR", dirname(WPFC_WP_PLUGIN_DIR));
		define("WPFC_WP_CONTENT_BASENAME", basename(WPFC_WP_CONTENT_DIR));
	}

	if (!defined('WPFC_MAIN_PATH')) {
		define("WPFC_MAIN_PATH", plugin_dir_path( __FILE__ ));
	}

	class WpFastestCache{
		private $systemMessage = "";
		private $options = array();
		public $images_in_css = array("path" => "", "images" => array());

		public function __construct(){

			$optimize_image_ajax_requests = array("wpfc_revert_image_ajax_request", 
												  "wpfc_statics_ajax_request",
												  "wpfc_optimize_image_ajax_request",
												  "wpfc_update_image_list_ajax_request"
												  );


			add_action( 'wp_ajax_wpfc_save_timeout_pages', array($this, 'wpfc_save_timeout_pages_callback'));
			add_action( 'wp_ajax_wpfc_save_exclude_pages', array($this, 'wpfc_save_exclude_pages_callback'));
			add_action( 'wp_ajax_wpfc_cdn_options_ajax_request', array($this, 'wpfc_cdn_options_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_remove_cdn_integration_ajax_request', array($this, 'wpfc_remove_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_save_cdn_integration_ajax_request', array($this, 'wpfc_save_cdn_integration_ajax_request_callback'));
			add_action( 'wp_ajax_wpfc_cdn_template_ajax_request', array($this, 'wpfc_cdn_template_ajax_request_callback'));




			add_action( 'wp_ajax_wpfc_check_url_ajax_request', array($this, 'wpfc_check_url_ajax_request_callback'));

			add_action( 'wp_ajax_wpfc_cache_statics_get', array($this, 'wpfc_cache_statics_get_callback'));

			add_action( 'wp_ajax_wpfc_update_premium', array($this, 'wpfc_update_premium_callback'));


			add_action( 'rate_post', array($this, 'wp_postratings_clear_fastest_cache'), 10, 2);

			if(is_dir($this->getWpContentDir()."/cache/tmpWpfc")){
				$this->rm_folder_recursively($this->getWpContentDir()."/cache/tmpWpfc");
			}

			if(isset($_POST) && isset($_POST["action"]) && $_POST["action"] == "vc_get_vc_grid_data"){
				if(isset($_POST["vc_post_id"]) && $_POST["vc_post_id"]){
					if(isset($_POST["_vcnonce"]) && $_POST["_vcnonce"]){
						$this->setCustomInterval();

						$schedules_rules = array();
						$exist_cronjob = false;
						$wpfc_timeout_number = 0;

						$crons = _get_cron_array();

						foreach ((array)$crons as $cron_key => $cron_value) {
							foreach ( (array) $cron_value as $hook => $events ) {
								if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
									if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
										$wpfc_timeout_number++;

										foreach ( (array) $events as $event_key => $event ) {
											$schedules = wp_get_schedules();

											if(isset($event["args"]) && isset($event["args"][0])){
												if($event["args"][0] == '{"prefix":"all","content":"all"}'){
													if($schedules[$event["schedule"]]["interval"] <= 86400){
														$exist_cronjob = true;
													}
												}
											}
										}
									}
								}
							}
						}

						if(!$exist_cronjob){
							$args = array("prefix" => "all", "content" => "all");
							wp_schedule_event(time(), "onceaday", "wp_fastest_cache_".$wpfc_timeout_number, array(json_encode($args)));
						}
					}
				}
			}else if(isset($_POST) && isset($_POST["action"]) && $_POST["action"] == "wpfc_wppolls_ajax_request"){
				//for WP-Polls 
				require_once "inc/wp-polls.php";
				$wp_polls = new WpPollsForWpFc();
				$wp_polls->hook();
			}else if(isset($_GET) && isset($_GET["action"]) && in_array($_GET["action"], $optimize_image_ajax_requests)){
				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("image.php");
					$img = new WpFastestCacheImageOptimisation();
					$img->hook();
				}
			}else{
				$this->setCustomInterval();

				$this->options = $this->getOptions();

				add_action('transition_post_status',  array($this, 'on_all_status_transitions'), 10, 3 );

				$this->commentHooks();

				$this->checkCronTime();

				register_deactivation_hook( __FILE__, array('WpFastestCache', 'deactivate') );

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("mobile-cache.php");
				}

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("powerful-html.php");
				}

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/statics.php")){
						include_once $this->get_premium_path("statics.php");
					}
				}

				if(is_admin()){
					//for wp-panel
					
					if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
						include_once $this->get_premium_path("image.php");
					}

					if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
						include_once $this->get_premium_path("logs.php");
					}

					add_action( 'wp_ajax_wpfc_cdn_template_ajax_request', array($this, 'wpfc_cdn_template_ajax_request_callback'));


					add_action( 'plugins_loaded', array($this, 'wpfc_load_plugin_textdomain'));

					$this->admin();
				}else{
					if(preg_match("/wpfc-minified\/([^\/]+)\/([^\/]+)/", $this->current_url(), $path)){
						if($sources = @scandir(WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$path[1], 1)){
							if(isset($sources[0])){
								// $exist_url = str_replace($path[2], $sources[0], $this->current_url());
								// header('Location: ' . $exist_url, true, 301);
								// exit;

								if(preg_match("/\.css/", $this->current_url())){
									header('Content-type: text/css');
								}else if(preg_match("/\.js/", $this->current_url())){
									header('Content-type: text/js');
								}

								echo file_get_contents(WPFC_WP_CONTENT_DIR."/cache/wpfc-minified/".$path[1]."/".$sources[0]);
								exit;
							}
						}
					}else{
						//for cache
						$this->cache();
					}
				}
			}
		}

		public function wpfc_update_premium_callback(){
			if(current_user_can('manage_options')){
				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					if(!file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/update.php")){
						$res = array("success" => false, "error_message" => "update.php is not exist");
					}else{
						include_once $this->get_premium_path("update.php");
						
						if(!class_exists("WpFastestCacheUpdate")){
							$res = array("success" => false, "error_message" => "WpFastestCacheUpdate is not exist");
						}else{
							$wpfc_premium = new WpFastestCacheUpdate();
							$content = $wpfc_premium->download_premium();

							if($content["success"]){
								$wpfc_zip_data = $content["content"];

								$wpfc_zip_dest_path = WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium.zip";

								if(@file_put_contents($wpfc_zip_dest_path, $wpfc_zip_data)){

									include_once ABSPATH."wp-admin/includes/file.php";
									include_once ABSPATH."wp-admin/includes/plugin.php";

									if(function_exists("unzip_file")){
										$this->rm_folder_recursively(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium");
										
										if(!function_exists('gzopen')){
											$res = array("success" => false, "error_message" => "Missing zlib extension"); 
										}else{
											WP_Filesystem();
											$unzipfile = unzip_file($wpfc_zip_dest_path, WPFC_WP_PLUGIN_DIR."/");

											if ($unzipfile) {
												$result = activate_plugin( 'wp-fastest-cache-premium/wpFastestCachePremium.php' );

												if ( is_wp_error( $result ) ) {
													$res = array("success" => false, "error_message" => "Error occured while the plugin was activated"); 
												}else{
													$res = array("success" => true);
													$this->deleteCache(true);
												}
											} else {
												$res = array("success" => false, "error_message" => 'Error occured while the file was unzipped');      
											}
										}
										
									}else{
										$res = array("success" => false, "error_message" => "unzip_file() is not found");
									}
								}else{
									$res = array("success" => false, "error_message" => "/wp-content/plugins/ is not writable");
								}
							}else{
								$res = array("success" => false, "error_message" => $content["error_message"]);
							}
						}
					}
				}else{
					$res = array("success" => false, "error_message" => "Premium is not active");

				}

				echo json_encode($res);
				exit;

			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_cache_statics_get_callback(){
			if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
				if(file_exists(WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/statics.php")){
					include_once $this->get_premium_path("statics.php");
					
					$cache_statics = new WpFastestCacheStatics();
					$res = $cache_statics->get();
					echo json_encode($res);
					exit;
				}
			}
		}

		public function wpfc_check_url_ajax_request_callback(){
			if(current_user_can('manage_options')){
				$_GET["url"] = strip_tags($_GET["url"]);
				$_GET["url"] = str_replace(array("'", '"'), "", $_GET["url"]);
				
				if(!preg_match("/^http/", $_GET["url"])){
					$_GET["url"] = "http://".$_GET["url"];
				}
				
				$response = wp_remote_get($_GET["url"], array('timeout' => 10 ) );

				$header = wp_remote_retrieve_headers($response);

				if ( !$response || is_wp_error( $response ) ) {
					$res = array("success" => false, "error_message" => $response->get_error_message());
					
					if($response->get_error_code() == "http_request_failed"){
						if($response->get_error_message() == "Failure when receiving data from the peer"){
							$res = array("success" => true);
						}
					}
				}else{
					$response_code = wp_remote_retrieve_response_code( $response );
					if($response_code == 200){
						$res = array("success" => true);
					}else{
						if(method_exists($response, "get_error_message")){
							$res = array("success" => false, "error_message" => $response->get_error_message());
						}else{
							$res = array("success" => false, "error_message" => wp_remote_retrieve_response_message($response));
						}

						if(isset($header["server"]) && preg_match("/squid/i", $header["server"])){
							$res = array("success" => true);
						}
					}
				}
				echo json_encode($res);
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_cdn_template_ajax_request_callback(){
			if(current_user_can('manage_options')){
				ob_start();
				include_once(WPFC_MAIN_PATH."templates/cdn/".$_POST["id"].".php");
				$content = ob_get_contents();
				ob_end_clean();

				$res = array("success" => false, "content" => "");

				if($data = @file_get_contents(WPFC_MAIN_PATH."templates/cdn/".$_POST["id"].".php")){
					$res["success"] = true;
					$res["content"] = $content;
				}

				echo json_encode($res);
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_save_cdn_integration_ajax_request_callback(){
			if(current_user_can('manage_options')){
				$values = json_encode($_POST["values"]);
				if(get_option("WpFastestCacheCDN")){
					update_option("WpFastestCacheCDN", $values);
				}else{
					add_option("WpFastestCacheCDN", $values, null, "yes");
				}
				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_remove_cdn_integration_ajax_request_callback(){
			if(current_user_can('manage_options')){
				delete_option("WpFastestCacheCDN");
				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_cdn_options_ajax_request_callback(){
			if(current_user_can('manage_options')){
				$cdn_values = get_option("WpFastestCacheCDN");
				if($cdn_values){
					echo $cdn_values;
				}else{
					echo json_encode(array("success" => false)); 
				}
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_save_exclude_pages_callback(){
			if(current_user_can('manage_options')){
				if(isset($_POST["rules"])){
					$data = json_encode($_POST["rules"]);

					if(get_option("WpFastestCacheExclude")){
						update_option("WpFastestCacheExclude", $data);
					}else{
						add_option("WpFastestCacheExclude", $data, null, "yes");
					}
				}else{
					delete_option("WpFastestCacheExclude");
				}

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wpfc_save_timeout_pages_callback(){
			if(current_user_can('manage_options')){
				$this->setCustomInterval();
			
		    	$crons = _get_cron_array();

		    	foreach ($crons as $cron_key => $cron_value) {
		    		foreach ( (array) $cron_value as $hook => $events ) {
		    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
		    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
		    					foreach ( (array) $events as $event_key => $event ) {
			    					if($id[1]){
			    						wp_clear_scheduled_hook("wp_fastest_cache".$id[1], $event["args"]);
			    					}else{
			    						wp_clear_scheduled_hook("wp_fastest_cache", $event["args"]);
			    					}
		    					}
		    				}
		    			}
		    		}
		    	}

				if(isset($_POST["rules"]) && count($_POST["rules"]) > 0){
					$i = 0;

					foreach ($_POST["rules"] as $key => $value) {
						$args = array("prefix" => $value["prefix"], "content" => $value["content"]);

						wp_schedule_event(time(), $value["schedule"], "wp_fastest_cache_".$i, array(json_encode($args)));
						$i = $i + 1;
					}
				}

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}

		public function wp_postratings_clear_fastest_cache($rate_userid, $post_id){
			// to remove cache if vote is from homepage or category page or tag
			if(isset($_SERVER["HTTP_REFERER"]) && $_SERVER["HTTP_REFERER"]){
				$url =  parse_url($_SERVER["HTTP_REFERER"]);

				$url["path"] = isset($url["path"]) ? $url["path"] : "/index.html";

				if(isset($url["path"])){
					if($url["path"] == "/"){
						$this->rm_folder_recursively($this->getWpContentDir()."/cache/all/index.html");
					}else{
						$this->rm_folder_recursively($this->getWpContentDir()."/cache/all".$url["path"]);
					}
				}
			}

			if($post_id){
				$this->singleDeleteCache(false, $post_id);
			}
		}

		private function admin(){
			add_action('wp_loaded', array($this, "load_admin_toolbar"));
			

			if(isset($_GET["page"]) && $_GET["page"] == "wpfastestcacheoptions"){
				include_once('inc/admin.php');
				$wpfc = new WpFastestCacheAdmin();
				$wpfc->addMenuPage();
			}else{
				add_action('admin_menu', array($this, 'register_my_custom_menu_page'));
			}

			add_action('admin_enqueue_scripts', array($this, 'load_toolbar_js'));
		}

		public function load_toolbar_js(){
			wp_enqueue_script("wpfc-toolbar", plugins_url("wp-fastest-cache/js/toolbar.js"), array(), time(), true);
		}

		public function load_admin_toolbar(){
			$show = false;

			// Admin
			$show = (current_user_can( 'manage_options' ) || current_user_can('edit_others_pages')) ? true : false;

			// Author
			if(defined('WPFC_TOOLBAR_FOR_AUTHOR') && WPFC_TOOLBAR_FOR_AUTHOR){
				if(current_user_can( 'delete_published_posts' ) || current_user_can('edit_published_posts')) {
					$show = true;
				}
			}
			
			if($show){
				include_once plugin_dir_path(__FILE__)."inc/admin-toolbar.php";

				add_action('wp_ajax_wpfc_delete_cache', array($this, "deleteCacheToolbar"));
				add_action('wp_ajax_wpfc_delete_cache_and_minified', array($this, "deleteCssAndJsCacheToolbar"));
				
				$toolbar = new WpFastestCacheAdminToolbar();
				$toolbar->add();
			}
		}

		public function register_my_custom_menu_page(){
			if(function_exists('add_menu_page')){ 
				add_menu_page("WP Fastest Cache Settings", "WP Fastest Cache", 'manage_options', "wpfastestcacheoptions", array($this, 'optionsPage'), plugins_url("wp-fastest-cache/images/icon-32x32.png"), "99.".time() );
				wp_enqueue_style("wp-fastest-cache", plugins_url("wp-fastest-cache/css/style.css"), array(), time(), "all");
			}
			
			wp_enqueue_style("wp-fastest-cache-toolbar", plugins_url("wp-fastest-cache/css/toolbar.css"), array(), time(), "all");
			
			if(isset($_GET["page"]) && $_GET["page"] == "wpfastestcacheoptions"){
				wp_enqueue_style("wp-fastest-cache-buycredit", plugins_url("wp-fastest-cache/css/buycredit.css"), array(), time(), "all");
				wp_enqueue_style("wp-fastest-cache-flaticon", plugins_url("wp-fastest-cache/css/flaticon.css"), array(), time(), "all");
				wp_enqueue_style("wp-fastest-cache-dialog", plugins_url("wp-fastest-cache/css/dialog.css"), array(), time(), "all");
			}
		}

		public function deleteCacheToolbar(){
			$this->deleteCache();
		}

		public function deleteCssAndJsCacheToolbar(){
			$this->deleteCache(true);
		}

		private function cache(){
			include_once('inc/cache.php');
			$wpfc = new WpFastestCacheCreateCache();
			$wpfc->createCache();
		}

		public function deactivate(){
			$wpfc = new WpFastestCache();
			$path = ABSPATH;
			
			if($wpfc->is_subdirectory_install()){
				$path = $wpfc->getABSPATH();
			}

			if(is_file($path.".htaccess") && is_writable($path.".htaccess")){
				$htaccess = file_get_contents($path.".htaccess");
				$htaccess = preg_replace("/#\s?BEGIN\s?WpFastestCache.*?#\s?END\s?WpFastestCache/s", "", $htaccess);
				$htaccess = preg_replace("/#\s?BEGIN\s?GzipWpFastestCache.*?#\s?END\s?GzipWpFastestCache/s", "", $htaccess);
				$htaccess = preg_replace("/#\s?BEGIN\s?LBCWpFastestCache.*?#\s?END\s?LBCWpFastestCache/s", "", $htaccess);
				file_put_contents($path.".htaccess", $htaccess);
			}

			wp_clear_scheduled_hook("wp_fastest_cache");
			wp_clear_scheduled_hook($wpfc->slug()."_regular");
			wp_clear_scheduled_hook("wp_fastest_cache_Preload");

			delete_option("WpFastestCache");
			delete_option("WpFcDeleteCacheLogs");
			$wpfc->deleteCache();
		}

		protected function slug(){
			return "wp_fastest_cache";
		}

		protected function getWpContentDir(){
			return WPFC_WP_CONTENT_DIR;
		}

		protected function getOptions(){
			if($data = get_option("WpFastestCache")){
				return json_decode($data);
			}
		}

		protected function getSystemMessage(){
			return $this->systemMessage;
		}

		protected function get_excluded_useragent(){
			return "facebookexternalhit";
		}

		// protected function detectNewPost(){
		// 	if(isset($this->options->wpFastestCacheNewPost) && isset($this->options->wpFastestCacheStatus)){
		// 		add_filter ('save_post', array($this, 'deleteCache'));
		// 	}
		// }

		public function on_all_status_transitions($new_status, $old_status, $post) {
			if ( ! wp_is_post_revision($post->ID) ){
				if(isset($this->options->wpFastestCacheNewPost) && isset($this->options->wpFastestCacheStatus)){
					if($new_status == "publish" && $old_status != "publish"){
						$this->deleteCache();
					}else if($new_status == "trash" && $old_status == "publish"){
						$this->deleteCache();
					}else if(($new_status == "draft" || $new_status == "pending") && $old_status == "publish"){
						$this->deleteCache();
					}
				}

				if($new_status == "publish" && $old_status == "publish"){
					if(defined('WPFC_DELETE_ALL_CACHE_AFTER_UPDATE') && WPFC_DELETE_ALL_CACHE_AFTER_UPDATE){
						$this->deleteCache();
					}else{
						$this->singleDeleteCache(false, $post->ID);
					}
				}
			}
		}

		protected function commentHooks(){
			//it works when the status of a comment changes
			add_filter ('wp_set_comment_status', array($this, 'singleDeleteCache'));

			//it works when a comment is saved in the database
			add_filter ('comment_post', array($this, 'detectNewComment'));
		}

		public function detectNewComment($comment_id){
			if(current_user_can( 'manage_options') || !get_option('comment_moderation')){
				$this->singleDeleteCache($comment_id);
			}
		}

		public function singleDeleteCache($comment_id = false, $post_id = false){
			if($comment_id){
				$comment = get_comment($comment_id);
				
				if($comment && $comment->comment_post_ID){
					$post_id = $comment->comment_post_ID;
				}
			}

			if($post_id){
				$permalink = get_permalink($post_id);

				if(preg_match("/https?:\/\/[^\/]+\/(.+)/", $permalink, $out)){
					$path = $this->getWpContentDir()."/cache/all/".$out[1];
					$mobile_path = $this->getWpContentDir()."/cache/wpfc-mobile-cache/".$out[1];

					if(is_dir($path)){
						if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
							include_once $this->get_premium_path("logs.php");
							$log = new WpFastestCacheLogs("delete");
							$log->action();
						}

						$this->rm_folder_recursively($path);
					}

					if(is_dir($mobile_path)){
						$this->rm_folder_recursively($mobile_path);
					}
				}
				
				// Sometimes there is no path of post/page static pages 
				if(get_option('page_on_front') == $post_id){
					@unlink($this->getWpContentDir()."/cache/all/index.html");
					@unlink($this->getWpContentDir()."/cache/wpfc-mobile-cache/index.html");
				}

				if(is_sticky($post_id)){
					@unlink($this->getWpContentDir()."/cache/all/index.html");
					@unlink($this->getWpContentDir()."/cache/wpfc-mobile-cache/index.html");
				}

				// to check the post appears on homepage
				if(!get_option('page_on_front')){
					$numberposts = get_option("posts_per_page") - count(get_option('sticky_posts'));

					if($numberposts > 0){
			    		$recent_posts = wp_get_recent_posts(array(
										'numberposts' => $numberposts,
									    'orderby' => 'post_date',
									    'order' => 'DESC',
									    'post_type' => 'post',
									    'post_status' => 'publish',
									    'suppress_filters' => true
									    ), ARRAY_A);

						foreach ((array)$recent_posts as $key => $value) {
							if($post_id == $value["ID"]){
								@unlink($this->getWpContentDir()."/cache/all/index.html");
								@unlink($this->getWpContentDir()."/cache/wpfc-mobile-cache/index.html");
							}
						}
					}
				}

				// to check the post appears on cat
				// toDO

				// to check the post appears on tag
				// toDO
			}
		}

		public function deleteCache($minified = false){
			$this->set_preload();

			$created_tmpWpfc = false;
			$cache_deleted = false;
			$minifed_deleted = false;

			$cache_path = $this->getWpContentDir()."/cache/all";
			$minified_cache_path = $this->getWpContentDir()."/cache/wpfc-minified";

			if(class_exists("WpFcMobileCache")){
				$wpfc_mobile = new WpFcMobileCache();
				$wpfc_mobile->delete_cache($this->getWpContentDir());
			}
			
			if(!is_dir($this->getWpContentDir()."/cache/tmpWpfc")){
				if(@mkdir($this->getWpContentDir()."/cache/tmpWpfc", 0755, true)){
					$created_tmpWpfc = true;
				}else{
					$created_tmpWpfc = false;
					//$this->systemMessage = array("Permission of <strong>/wp-content/cache</strong> must be <strong>755</strong>", "error");
				}
			}else{
				$created_tmpWpfc = true;
			}

			if(is_dir($cache_path)){
				if(@rename($cache_path, $this->getWpContentDir()."/cache/tmpWpfc/".time())){
					delete_option("WpFastestCacheHTML");
					delete_option("WpFastestCacheHTMLSIZE");
					delete_option("WpFastestCacheMOBILE");
					delete_option("WpFastestCacheMOBILESIZE");

					$cache_deleted = true;
				}
			}else{
				$cache_deleted = true;
			}

			if($minified){
				if(is_dir($minified_cache_path)){
					if(@rename($minified_cache_path, $this->getWpContentDir()."/cache/tmpWpfc/m".time())){
						delete_option("WpFastestCacheCSS");
						delete_option("WpFastestCacheCSSSIZE");
						delete_option("WpFastestCacheJS");
						delete_option("WpFastestCacheJSSIZE");

						$minifed_deleted = true;
					}
				}else{
					$minifed_deleted = true;
				}
			}else{
				$minifed_deleted = true;
			}

			if($created_tmpWpfc && $cache_deleted && $minifed_deleted){
				$this->systemMessage = array("All cache files have been deleted","success");

				if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
					include_once $this->get_premium_path("logs.php");

					$log = new WpFastestCacheLogs("delete");
					$log->action();
				}
			}else{
				$this->systemMessage = array("Permissions Problem: <a href='http://www.wpfastestcache.com/warnings/delete-cache-problem-related-to-permission/' target='_blank'>Read More</a>", "error", array("light_box" => "delete_cache_permission_error"));
			}

			// for ajax request
			if(isset($_GET["action"]) && in_array($_GET["action"], array("wpfc_delete_cache", "wpfc_delete_cache_and_minified"))){
				die(json_encode($this->systemMessage));
			}
		}

		public function checkCronTime(){
			$crons = _get_cron_array();

	    	foreach ((array)$crons as $cron_key => $cron_value) {
	    		foreach ( (array) $cron_value as $hook => $events ) {
	    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
	    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
		    				foreach ( (array) $events as $event_key => $event ) {
		    					add_action("wp_fastest_cache".$id[1],  array($this, 'setSchedule'));
		    				}
		    			}
		    		}
		    	}
		    }

		    add_action($this->slug()."_Preload",  array($this, 'create_preload_cache'));
		}

		public function set_preload(){
			$preload_arr = array();

			if(!empty($_POST)){
				foreach ($_POST as $key => $value) {
					preg_match("/wpFastestCachePreload_(.+)/", $key, $type);

					if(!empty($type)){
						$preload_arr[$type[1]] = 0; 
					}
				}
			}

			if($data = get_option("WpFastestCachePreLoad")){
				$preload_std = json_decode($data);

				if(!empty($preload_arr)){
					foreach ($preload_arr as $key => &$value) {
						if(!empty($preload_std->$key)){
							$value = $preload_std->$key;
						}
					}

					$preload_std = $preload_arr;
				}else{
					foreach ($preload_std as $key => &$value) {
						$value = 0;
					}
				}

				update_option("WpFastestCachePreLoad", json_encode($preload_std));

				if(!wp_next_scheduled($this->slug()."_Preload")){
					wp_schedule_event(time() + 5, 'everyminute', $this->slug()."_Preload");
				}
			}else{
				if(!empty($preload_arr)){
					add_option("WpFastestCachePreLoad", json_encode($preload_arr), null, "yes");

					if(!wp_next_scheduled($this->slug()."_Preload")){
						wp_schedule_event(time() + 5, 'everyminute', $this->slug()."_Preload");
					}
				}else{
					//toDO
				}
			}
		}

		public function create_preload_cache(){
			if($data = get_option("WpFastestCachePreLoad")){
				$pre_load = json_decode($data);
				
				$urls_limit = isset($this->options->wpFastestCachePreload_number) ? $this->options->wpFastestCachePreload_number : 4; // must be even
				$urls = array();

				// HOME
				if($pre_load->homepage > -1){
					if(isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme){
						array_push($urls, array("url" => get_option("home"), "user-agent" => "mobile"));
					}

					array_push($urls, array("url" => get_option("home"), "user-agent" => "desktop"));
					
					$pre_load->homepage = -1;
				}

				// POST
				if($pre_load->post > -1){
		    		$recent_posts = wp_get_recent_posts(array(
											'numberposts' => 2,
										    'offset' => $pre_load->post,
										    'orderby' => 'ID',
										    'order' => 'DESC',
										    'post_type' => 'post',
										    'post_status' => 'publish',
										    'suppress_filters' => true
										    ), ARRAY_A);

		    		if(count($recent_posts) > 0){
		    			foreach ($recent_posts as $key => $post) {
		    				if(count($urls) < $urls_limit){
			    				if(isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme){
			    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "mobile"));
			    				}

		    					array_push($urls, array("url" => get_permalink($post["ID"]), "user-agent" => "desktop"));

			    				$pre_load->post = $pre_load->post + 1;
		    				}
		    			}
		    		}else{
		    			$pre_load->post = -1;
		    		}
				}

				// PAGE
				if($pre_load->page > -1){
					$pages = get_pages(array(
							'sort_order' => 'DESC',
							'sort_column' => 'ID',
							'parent' => -1,
							'hierarchical' => 0,
							'number' => 2,
							'offset' => $pre_load->page,
							'post_type' => 'page',
							'post_status' => 'publish'
					));

					if(count($pages) > 0){
						foreach ($pages as $key => $page) {
							$page_url = get_option("home")."/".get_page_uri($page->ID);

							if(count($urls) < $urls_limit){
			    				if(isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme){
			    					array_push($urls, array("url" => $page_url, "user-agent" => "mobile"));
			    				}

		    					array_push($urls, array("url" => $page_url, "user-agent" => "desktop"));

			    				$pre_load->page = $pre_load->page + 1;
		    				}
						}
					}else{
						$pre_load->page = -1;
					}
				}

				// CATEGORY
				if($pre_load->category > -1){
					$categories = get_terms("category", array(
														    'orderby'           => 'id', 
														    'order'             => 'DESC',
														    'hide_empty'        => false, 
														    'number'            => 2, 
														    'fields'            => 'all', 
														    'pad_counts'        => false, 
														    'offset'            => $pre_load->category
														));
					
					if(count($categories) > 0){
						foreach ($categories as $key => $category) {
							if(count($urls) < $urls_limit){
								if(isset($this->options->wpFastestCacheMobileTheme) && $this->options->wpFastestCacheMobileTheme){
									array_push($urls, array("url" => get_term_link($category->slug, "category"), "user-agent" => "mobile"));
								}

								array_push($urls, array("url" => get_term_link($category->slug, "category"), "user-agent" => "desktop"));

								$pre_load->category = $pre_load->category + 1;
							}
						}
						if(count($categories) == 1){
							$pre_load->category = -1;
						}
					}else{
						$pre_load->category = -1;
					}
				}

				if(count($urls) > 0){
					foreach ($urls as $key => $arr) {
						$user_agent = "";

						if($arr["user-agent"] == "desktop"){
							$user_agent = "WP Fastest Cache Preload Bot";
						}else if($arr["user-agent"] == "mobile"){
							$user_agent = "WP Fastest Cache Preload iPhone Mobile Bot";
						}

						$this->wpfc_remote_get($arr["url"], $user_agent);
					}

		    		update_option("WpFastestCachePreLoad", json_encode($pre_load));
				}else{
					wp_clear_scheduled_hook("wp_fastest_cache_Preload");
				}
			}

			die();
		}

		public function wpfc_remote_get($url, $user_agent){
			$response = wp_remote_get($url, array('timeout' => 10, 'headers' => array("cache-control" => array("no-store, no-cache, must-revalidate", "post-check=0, pre-check=0"),'user-agent' => $user_agent)));

			if (!$response || is_wp_error($response)){
				//error
			}else{
				if(wp_remote_retrieve_response_code($response) != 200){
					//error
				}
			}
		}

		public function setSchedule($args = ""){
			if($args){
				$rule = json_decode($args);

				if($rule->prefix == "all"){
					$this->deleteCache();
				}else if($rule->prefix == "homepage"){
					@unlink($this->getWpContentDir()."/cache/all/index.html");
					@unlink($this->getWpContentDir()."/cache/wpfc-mobile-cache/index.html");

					if(isset($this->options->wpFastestCachePreload_homepage) && $this->options->wpFastestCachePreload_homepage){
						$this->wpfc_remote_get(get_option("home"), "WP Fastest Cache Preload Bot - After Cache Timeout");
						$this->wpfc_remote_get(get_option("home"), "WP Fastest Cache Preload iPhone Mobile Bot - After Cache Timeout");
					}
				}else if($rule->prefix == "startwith"){


				}else if($rule->prefix == "exact"){
					if(!is_dir($this->getWpContentDir()."/cache/tmpWpfc")){
						if(@mkdir($this->getWpContentDir()."/cache/tmpWpfc", 0755, true)){}
					}

					@rename($this->getWpContentDir()."/cache/all/".$rule->content, $this->getWpContentDir()."/cache/tmpWpfc/".time());
					@rename($this->getWpContentDir()."/cache/wpfc-mobile-cache/".$rule->content, $this->getWpContentDir()."/cache/tmpWpfc/mobile_".time());

				}

				if($rule->prefix != "all"){
					if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
						include_once $this->get_premium_path("logs.php");
						$log = new WpFastestCacheLogs("delete");
						$log->action($rule);
					}
				}
			}else{
				//for old cron job
				$this->deleteCache();
			}
		}

		public function getABSPATH(){
			$path = ABSPATH;
			$siteUrl = site_url();
			$homeUrl = home_url();
			$diff = str_replace($homeUrl, "", $siteUrl);
			$diff = trim($diff,"/");

		    $pos = strrpos($path, $diff);

		    if($pos !== false){
		    	$path = substr_replace($path, "", $pos, strlen($diff));
		    	$path = trim($path,"/");
		    	$path = "/".$path."/";
		    }
		    return $path;
		}

		public function rm_folder_recursively($dir, $i = 1) {
			$files = @scandir($dir);
		    foreach((array)$files as $file) {
		    	if($i > 50){
		    		return true;
		    	}else{
		    		$i++;
		    	}
		        if ('.' === $file || '..' === $file) continue;
		        if (is_dir("$dir/$file")) $this->rm_folder_recursively("$dir/$file", $i);
		        else @unlink("$dir/$file");
		    }
		    
		    @rmdir($dir);
		    return true;
		}

		protected function is_subdirectory_install(){
			if(strlen(site_url()) > strlen(home_url())){
				return true;
			}
			return false;
		}

		protected function getMobileUserAgents(){

			return implode("|", $this->get_mobile_browsers())."|".implode("|", $this->get_operating_systems());

			//return "iphone|midp|sony|symbos|nokia|samsung|mobile|epoc|ericsson|panasonic|philips|sanyo|sharp|sie-|portalmmm|blazer|avantgo|danger|palm|series60|palmsource|pocketpc|android|blackberry|playbook|ipad|ipod|iemobile|palmos|webos|googlebot-mobile|bb10|xoom|p160u|nexus|SCH-I800|opera\smini|SM-G900R4|LG-|HTC|GT-I9505|WAP-Browser|Nokia309|Casper_VIA";
		}

		public function get_premium_path($name){
			return WPFC_WP_PLUGIN_DIR."/wp-fastest-cache-premium/pro/library/".$name;
		}

		public function getProLibraryPath($file){
			$currentPath = plugin_dir_path( __FILE__ );
			$pluginMainPath = str_replace("inc/", "", $currentPath);

			return $pluginMainPath."pro/".$file;
		}

		public function cron_add_minute( $schedules ) {
			$schedules['everyminute'] = array(
			    'interval' => 60*1,
			    'display' => __( 'Once Every 1 Minute' ),
			    'wpfc' => false
		    );

			$schedules['everyfiveminute'] = array(
			    'interval' => 60*5,
			    'display' => __( 'Once Every 5 Minutes' ),
			    'wpfc' => false
		    );

		   	$schedules['everyfifteenminute'] = array(
			    'interval' => 60*15,
			    'display' => __( 'Once Every 15 Minutes' ),
			    'wpfc' => true
		    );

		    $schedules['twiceanhour'] = array(
			    'interval' => 60*30,
			    'display' => __( 'Twice an Hour' ),
			    'wpfc' => true
		    );

		    $schedules['onceanhour'] = array(
			    'interval' => 60*60,
			    'display' => __( 'Once an Hour' ),
			    'wpfc' => true
		    );

		    $schedules['everysixhours'] = array(
			    'interval' => 60*60*6,
			    'display' => __( 'Once Every 6 Hours' ),
			    'wpfc' => true
		    );

		    $schedules['onceaday'] = array(
			    'interval' => 60*60*24,
			    'display' => __( 'Once a Day' ),
			    'wpfc' => true
		    );

		    $schedules['weekly'] = array(
			    'interval' => 60*60*24*7,
			    'display' => __( 'Once a Week' ),
			    'wpfc' => true
		    );

		    $schedules['montly'] = array(
			    'interval' => 60*60*24*30,
			    'display' => __( 'Once a Month' ),
			    'wpfc' => true
		    );

		    $schedules['yearly'] = array(
			    'interval' => 60*60*24*30*12,
			    'display' => __( 'Once a Year' ),
			    'wpfc' => true
		    );

		    return $schedules;
		}

		public function setCustomInterval(){
			add_filter( 'cron_schedules', array($this, 'cron_add_minute'));
		}

		public function isPluginActive( $plugin ) {
			return in_array( $plugin, (array) get_option( 'active_plugins', array() ) ) || $this->isPluginActiveForNetwork( $plugin );
		}
		
		public function isPluginActiveForNetwork( $plugin ) {
			if ( !is_multisite() )
				return false;

			$plugins = get_site_option( 'active_sitewide_plugins');
			if ( isset($plugins[$plugin]) )
				return true;

			return false;
		}

		public function current_url(){
			if(defined('WP_CLI')){
				$_SERVER["SERVER_NAME"] = isset($_SERVER["SERVER_NAME"]) ? $_SERVER["SERVER_NAME"] : "";
				$_SERVER["SERVER_PORT"] = isset($_SERVER["SERVER_PORT"]) ? $_SERVER["SERVER_PORT"] : 80;
			}
			
		    $pageURL = 'http';
		 
		    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
		        $pageURL .= 's';
		    }
		 
		    $pageURL .= '://';
		 
		    if($_SERVER['SERVER_PORT'] != '80'){
		        $pageURL .= $_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];
		    }else{
		        $pageURL .= $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
		    }
		 
		    return $pageURL;
		}

		public function wpfc_load_plugin_textdomain(){
			load_plugin_textdomain('wp-fastest-cache', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
		}

		public function cdn_replace_urls($matches){
			$this->cdn->file_types = str_replace(",", "|", $this->cdn->file_types);

			if(preg_match("/\.(".$this->cdn->file_types.")/i", $matches[0])){
				if(preg_match("/".preg_quote($this->cdn->originurl, "/")."/", $matches[2])){
					$matches[0] = preg_replace("/(http(s?)\:)?\/\/(www\.)?".preg_quote($this->cdn->originurl, "/")."/i", $this->cdn->cdnurl, $matches[0]);
				}else if(preg_match("/^(\/?)(wp-includes|wp-includes)/", $matches[2])){
					$matches[2] = preg_replace("/^\//", "", $matches[2]);
					$matches[0] = str_replace($matches[2], $this->cdn->cdnurl."/".$matches[2], $matches[0]);
				}
			}

			return $matches[0];
		}

		public function read_file($url){
			if(!preg_match("/\.php/", $url)){
				$url = preg_replace("/\?.*/", "", $url);
				$path = preg_replace("/.+\/wp-content\/(.+)/", WPFC_WP_CONTENT_DIR."/"."$1", $url);

				if(file_exists($path)){
					$myfile = fopen($path, "r") or die("Unable to open file!");
					$data = fread($myfile, filesize($path));
					fclose($myfile);

					return $data;
				}
			}

			return false;
		}

		public function get_operating_systems(){
			$operating_systems  = array(
									'Android',
									'blackberry|\bBB10\b|rim\stablet\sos',
									'PalmOS|avantgo|blazer|elaine|hiptop|palm|plucker|xiino',
									'Symbian|SymbOS|Series60|Series40|SYB-[0-9]+|\bS60\b',
									'Windows\sCE.*(PPC|Smartphone|Mobile|[0-9]{3}x[0-9]{3})|Window\sMobile|Windows\sPhone\s[0-9.]+|WCE;',
									'Windows\sPhone\s10.0|Windows\sPhone\s8.1|Windows\sPhone\s8.0|Windows\sPhone\sOS|XBLWP7|ZuneWP7|Windows\sNT\s6\.[23]\;\sARM\;',
									'\biPhone.*Mobile|\biPod|\biPad',
									'MeeGo',
									'Maemo',
									'J2ME\/|\bMIDP\b|\bCLDC\b', // '|Java/' produces bug #135
									'webOS|hpwOS',
									'\bBada\b',
									'BREW'
							    );
			return $operating_systems;
		}

		public function get_mobile_browsers(){
			$mobile_browsers  = array(
								'Vivaldi',
								'\bCrMo\b|CriOS|Android.*Chrome\/[.0-9]*\s(Mobile)?',
								'\bDolfin\b',
								'Opera.*Mini|Opera.*Mobi|Android.*Opera|Mobile.*OPR\/[0-9.]+|Coast\/[0-9.]+',
								'Skyfire',
								'Safari\/[.0-9]*\sEdge|Mobile\sSafari\/[.0-9]*\sEdge',
								'IEMobile|MSIEMobile', // |Trident/[.0-9]+
								'fennec|firefox.*maemo|(Mobile|Tablet).*Firefox|Firefox.*Mobile',
								'bolt',
								'teashark',
								'Blazer',
								'Version.*Mobile.*Safari|Safari.*Mobile|MobileSafari',
								'Tizen',
								'UC.*Browser|UCWEB',
								'baiduboxapp',
								'baidubrowser',
								'DiigoBrowser',
								'Puffin',
								'\bMercury\b',
								'Obigo',
								'NF-Browser',
								'NokiaBrowser|OviBrowser|OneBrowser|TwonkyBeamBrowser|SEMC.*Browser|FlyFlow|Minimo|NetFront|Novarra-Vision|MQQBrowser|MicroMessenger',
								'Android.*PaleMoon|Mobile.*PaleMoon'
							    );
			return $mobile_browsers;
		}


	}

	$GLOBALS["wp_fastest_cache"] = new WpFastestCache();
?>