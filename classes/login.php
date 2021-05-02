<?php

if(!class_exists('__cf7_login')){
    final class __cf7_login {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function do_shortcode_tag($output, $tag, $attr, $m){
            if('contact-form-7' !== $tag){
                return $output;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $output;
            }
            if(!$contact_form->is_true('__login')){
                return $output;
            }
            if($contact_form->is_true('__signup')){
                return $output; // signup first
            }
            $tags = wp_list_pluck($contact_form->scan_form_tags(), 'type', 'name');
            if(isset($tags['user_email']) and isset($tags['user_login'])){
                return current_user_can('manage_options') ? str_replace('.', ':', __('Invalid user parameter(s).')) . ' ' . __('Duplicated username or email address.') : __('Something went wrong.');
            }
            $missing = [];
            if(!isset($tags['user_email']) and !isset($tags['user_login'])){
                $missing[] = 'user_login';
            }
            if(!isset($tags['user_password'])){
                $missing[] = 'user_password';
            }
            if($missing){
                return current_user_can('manage_options') ? sprintf(__('Missing parameter(s): %s'), implode(', ', $missing)) . '.' : __('Something went wrong.');
            }
            $invalid = [];
            if(isset($tags['user_email']) and $tags['user_email'] != 'email*'){
                $invalid[] = 'user_email';
            }
            if(isset($tags['user_login']) and $tags['user_login'] != 'text*'){
                $invalid[] = 'user_login';
            }
            if(isset($tags['user_password']) and $tags['user_password'] != 'password*'){
                $invalid[] = 'user_password';
            }
            if($invalid){
                return current_user_can('manage_options') ? sprintf(__('Invalid parameter(s): %s'), implode(', ', $invalid)) . '.' : __('Something went wrong.');
            }
            if(is_user_logged_in()){
                return __('You are logged in already. No need to register again!');
            }
            return $output;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_mail_sent($contact_form){
            if(!$contact_form->is_true('__login')){
                return;
            }
            if($contact_form->is_true('__signup')){
                return; // signup first
            }
            if(is_user_logged_in()){
                return;
            }
            // Asumo que ya pasó por la validación de requeridos.
            $user_email = __get_posted_data('user_email');
            $user_login = __get_posted_data('user_login');
            $user_password = __get_posted_data('user_password');
            if($user_login){
                $user = get_user_by('login', $user_login);
                if(!$user and wpcf7_is_email($user_login)){
                    $user = get_user_by('email', $user_login);
                }
            } else {
                $user = get_user_by('email', $user_email);
            }
            if(!$user){
                return;
            }
            if(!wp_check_password($user_password, $user->data->user_pass, $user->ID)){
                return;
            }
            wp_signon([
                'remember' => __get_posted_data('remember'),
                'user_login' => $user->user_login,
                'user_password' => $user_password,
            ]);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_email($result, $tag){
            if($tag->name !== 'user_email'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__login')){
                return $result;
            }
            if($contact_form->is_true('__signup')){
                return $result; // signup first
            }
            // Asumo que ya pasó por la validación de requeridos.
            $user_email = __get_posted_data('user_email');
            if(!email_exists($user_email)){
                $message = __('Unknown email address. Check again or try your username.');
                $message = explode('.', $message);
                $message = $message[0] . '.';
                $result->invalidate($tag, wp_strip_all_tags($message));
                return $result;
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_password($result, $tag){
            if($tag->name !== 'user_password'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__login')){
                return $result;
            }
            if($contact_form->is_true('__signup')){
                return $result; // signup first
            }
            // Asumo que ya pasó por la validación de requeridos.
            $user_email = __get_posted_data('user_email');
            $user_login = __get_posted_data('user_login');
            $user_password = __get_posted_data('user_password');
            if($user_email === '' and $user_login === ''){
                return $result; // double check
            }
            if($user_login){
                $message = sprintf(__('<strong>Error</strong>: The password you entered for the username %s is incorrect.'), '<strong>' . $user_login . '</strong>');
                $user = get_user_by('login', $user_login);
                if(!$user and wpcf7_is_email($user_login)){
                    $message = sprintf(__('<strong>Error</strong>: The password you entered for the email address %s is incorrect.'), '<strong>' . $user_email . '</strong>');
                    $user = get_user_by('email', $user_login);
                }
            } else {
                $message = sprintf(__('<strong>Error</strong>: The password you entered for the email address %s is incorrect.'), '<strong>' . $user_email . '</strong>');
                $user = get_user_by('email', $user_email);
            }
            if(!$user){
                return $result; // double check
            }
            if(!wp_check_password($user_password, $user->data->user_pass, $user->ID)){
                $result->invalidate($tag, wp_strip_all_tags($message));
                return $result;
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_text($result, $tag){
            if($tag->name !== 'user_login'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__login')){
                return $result;
            }
            if($contact_form->is_true('__signup')){
                return $result; // signup first
            }
            // Asumo que ya pasó por la validación de requeridos.
            $user_login = __get_posted_data('user_login');
            $message = __('Unknown username. Check again or try your email address.');
            $user = get_user_by('login', $user_login);
            if(!$user and wpcf7_is_email($user_login)){
                $message = __('Unknown email address. Check again or try your username.');
                $user = get_user_by('email', $user_login);
            }
            if(!$user){
                $result->invalidate($tag, wp_strip_all_tags($message));
                return $result;
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
