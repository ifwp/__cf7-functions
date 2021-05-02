<?php

if(!class_exists('__cf7_submit')){
    final class __cf7_submit {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_scripts(){
            wp_enqueue_script('__cf7-submit', plugin_dir_url(__CF7_FILE) . 'assets/submit.js', ['contact-form-7'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/submit.js'), true);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_styles(){
            wp_enqueue_style('__cf7-submit', plugin_dir_url(__CF7_FILE) . 'assets/submit.css', ['contact-form-7'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/submit.css'));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
