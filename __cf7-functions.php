<?php
/*
Author: IFWP
Author URI: https://github.com/ifwp
Description: A collection of useful functions for Contact Form 7.
Domain Path:
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Network: true
Plugin Name: __cf7-functions
Plugin URI: https://github.com/ifwp/__cf7-functions
Requires at least: 5.0
Requires PHP: 5.6
Text Domain: __cf7-functions
Version: 1.4.28-alpha.1
*/

if(defined('ABSPATH')){
    add_action('plugins_loaded', function(){
        if(!function_exists('is_plugin_active')){
            require_once(ABSPATH . 'wp-admin/includes/plugin.php');
        }
        if(is_plugin_active('__functions/__functions.php')){
            require_once(plugin_dir_path(__FILE__) . 'functions.php');
            __build_update_checker('https://github.com/ifwp/__cf7-functions', __FILE__, '__cf7-functions');
        } else {
            add_action('admin_notices', function(){
                echo '<div class="notice notice-error"><p>The __functions plugin must be active in order to use __cf7-functions. Please <a href="' . admin_url('plugins.php') . '">activate it</a> before continuing.</p></div>';
            });
        }
    });
}
