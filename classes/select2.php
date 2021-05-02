<?php

if(!class_exists('__cf7_select2')){
    final class __cf7_select2 {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_scripts(){
            wp_enqueue_script('__select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js', ['contact-form-7'], '4.0.13', true);
			wp_enqueue_script('__cf7-select2', plugin_dir_url(__CF7_FILE) . 'assets/select2.js', ['__select2'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/select2.js'), true);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_styles(){
            wp_enqueue_style('__select2', 'https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css', ['contact-form-7'], '4.0.13');
			wp_enqueue_style('__select2-bootstrap', plugin_dir_url(__CF7_FILE) . 'assets/select2-bootstrap.css', ['__select2'], '1.0.0');
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
