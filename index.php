<?php
/**
* Plugin Name: wpChat
* Plugin URI: http://dev.wp-cours.com/
* Description: plugin for website anouncing maintenace
* Version: 1.0 or whatever version of the plugin (pretty self explanatory)
* Author: Adam Parent (InitialCrow)
* Author URI: http://adam-parent.com
* License: MIT
*/
require plugin_dir_path(__FILE__ ).'/vendor/autoload.php';
use App\Controller\ChatController;
use App\Controller\Admin\AdminController;
use App\EzRouter;

define('BASE_URI', '');
session_start();


global $current_user;


function wpChat_scripts() {
    wp_enqueue_style( 'wpChatBuble', plugins_url('wpChat/public/css/buble.css' ) );
    wp_enqueue_style( 'wpChatLogin', plugins_url('wpChat/public/css/login.css' ) );
    wp_enqueue_style( 'wpChat', plugins_url('wpChat/public/css/chat.css' ) );

    wp_enqueue_script( 'main', plugins_url('wpChat/public/js/main.js' ),array( 'jquery' ), false, true );
    wp_enqueue_script( 'init', plugins_url('wpChat/public/js/init.js' ),array(), false, true );
}
function wpChat_AdminScripts(){
	wp_enqueue_style( 'wpChatAdmin', plugins_url('wpChat/public/css/admin/dashboard.css' ) );
	wp_enqueue_script( 'main', plugins_url('wpChat/public/js/admin.js' ),array( 'jquery' ), false, true );
	wp_enqueue_script( 'init', plugins_url('wpChat/public/js/init.js' ),array(), false, true );
}


function wpChat_route(){
	$router = new EzRouter();
		if(is_admin()) {
		  // do stuff for the admin user...

			AdminController::init();
		
			$router->route(BASE_URI.'/wp-admin/index.php/server/start',[AdminController::class,'start']);
			$router->route(BASE_URI.'/wp-admin/index.php/server/stop',[AdminController::class,'stop']);
			$router->route(BASE_URI.'/wp-admin/index.php/server/restart',[AdminController::class,'restart']);
			$router->route(BASE_URI.'/wp-admin/index.php/clearChat',[AdminController::class,'clearHistory']);


		}
		$router->route(BASE_URI.'/',[ChatController::class, 'init']);
		$router->route(BASE_URI.'/index.php/login',[ChatController::class,'login']);
		$router->route(BASE_URI.'/index.php/wc_unlog',[ChatController::class,'disconect']);
	$router->end(false);
}
add_action('init', 'wpChat_route' );
add_action('wp_enqueue_scripts', 'wpChat_scripts' );
add_action('admin_enqueue_scripts','wpChat_AdminScripts');




