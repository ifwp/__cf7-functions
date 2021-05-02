<?php

if(!class_exists('__cf7_editor')){
    final class __cf7_editor {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function admin_enqueue_scripts(){
	   		wp_enqueue_script('__ace', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ace.min.js', ['wpcf7-admin'], '1.4.12', true);
			wp_enqueue_script('__ace-language-tools', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12/ext-language_tools.min.js', ['__ace'], '1.4.12', true);
			wp_enqueue_script('__cf7-editor', plugin_dir_url(__CF7_FILE) . 'assets/editor.js', ['__ace-language-tools'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/editor.js'), true);
			 wp_enqueue_style('__cf7-editor', plugin_dir_url(__CF7_FILE) . 'assets/editor.css', ['contact-form-7-admin'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/editor.css'));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
