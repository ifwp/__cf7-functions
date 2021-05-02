<?php

if(!class_exists('__cf7_redirect')){
    final class __cf7_redirect {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // private static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		public static function l10n(){
			$l10n = [
				'message' => __('Loading&hellip;'),
				'messages' => [],
				'redirects' => [],
			];
			$posts = get_posts([
                'post_type' => 'wpcf7_contact_form',
                'posts_per_page' => -1,
            ]);
            if($posts){
                foreach($posts as $post){
                    $contact_form = wpcf7_contact_form($post->ID);
					if($contact_form){
						$message = $contact_form->pref('__redirect_message');
						if($message !== null){
							$l10n['messages'][$post->ID] = $message;
						}
						if($contact_form->is_true('__redirect')){
							$l10n['redirects'][$post->ID] = '';
						} else {
							$redirect = $contact_form->pref('__redirect');
							if($redirect !== null){
								if(wpcf7_is_url($redirect)){
									$l10n['redirects'][$post->ID] = $redirect;
								} else {
									$l10n['redirects'][$post->ID] = '';
								}
							}
						}
					}
                }
            }
			return $l10n;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_scripts(){
            wp_enqueue_script('__cf7-redirect', plugin_dir_url(__CF7_FILE) . 'assets/redirect.js', ['contact-form-7'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/redirect.js'), true);
			wp_localize_script( '__cf7-redirect', '__cf7_redirect', self::l10n());
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
