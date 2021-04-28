<?php

if(!class_exists('__cf7_signup')){
    final class __cf7_signup {

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
            if(!$contact_form->is_true('__signup')){
                return $output;
            }
            $tags = wp_list_pluck($contact_form->scan_form_tags(), 'type', 'name');
            $missing = [];
            if(!isset($tags['__user_email'])){
                $missing[] = '__user_email';
            }
            if(!isset($tags['__user_password'])){
                $missing[] = '__user_password';
            }
            if($missing){
                return current_user_can('manage_options') ? sprintf(__('Missing parameter(s): %s'), implode(', ', $missing)) . '.' : __('Something went wrong.');
            }
            $invalid = [];
            if(isset($tags['__user_email']) and $tags['__user_email'] !== 'email*'){
                $invalid[] = '__user_email';
            }
            if(isset($tags['__user_login']) and $tags['__user_login'] !== 'text*'){
                $invalid[] = '__user_login';
            }
            if($tags['__user_password'] !== 'password*'){
                $invalid[] = '__user_password';
            }
            if($tags['__user_password_confirm'] !== 'password*'){
                $invalid[] = '__user_password_confirm';
            }
            if($invalid){
                return current_user_can('manage_options') ? sprintf(__('Invalid parameter(s): %s'), implode(', ', $invalid)) . '.' : __('Something went wrong.');
            }
            if(is_user_logged_in() and !current_user_can('create_users')){
                return __('Sorry, you are not allowed to create new users.') . ' ' . __('You need a higher level of permission.');
            }
            return $output;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_mail_sent($contact_form){
            if(!$contact_form->is_true('__signup')){
                return;
            }
            if(is_user_logged_in() and !current_user_can('create_users')){
    			return;
    		}
            // Asumo que ya pasó por la validación de requeridos
            $user_email = __get_posted_data('__user_email');
            $user_login = __get_posted_data('__user_login');
            $user_password = __get_posted_data('__user_password');
            $user_password_confirm = __get_posted_data('__user_password_confirm');
            /*if($user_email === '' and $user_login === ''){
                return;
            }
            if($user_password === ''){
                return;
            }*/
            if(email_exists($user_email)){
                return;
            }
            /*if($user_email !== ''){
                if(email_exists($user_email)){
                    return;
                }
            }*/
            if($user_login !== ''){
                if(wpcf7_is_email($user_login)){
                    if(email_exists($user_login)){
                        return;
                    }
                } else {
                    if(username_exists($user_login)){
                        return;
                    }
                }
                if(!validate_username($user_login)){
                    return;
                }
                $illegal_user_logins = (array) apply_filters('illegal_user_logins', ['admin']);
                if(in_array(strtolower($user_login), array_map('strtolower', $illegal_user_logins), true)){
                    return;
                }
            } else {
                $user_login = $user_email;
            }
            if(strpos(wp_unslash($user_password), '\\') !== false){
                return;
            }
            if($user_password_confirm !== '' and $user_password_confirm !== $user_password){
                return;
            }
            $role = __get_posted_data('__role');
            if($role === '' or !is_user_logged_in()){
                $role = $contact_form->pref('__role');
                if($role === null){
                    $role = get_option('default_role');
                }
            }
            $userdata = [
                'role' => $role,
                'user_email' => wp_slash($user_email),
                'user_login' => wp_slash($user_login),
                'user_pass' => $user_password,
            ];
            $user_id = wp_insert_user($userdata);
            if(is_wp_error($user_id)){
                return;
            }
            if(!is_user_logged_in()){
                if($contact_form->is_true('__login')){
                    wp_signon([
                        'remember' => __get_posted_data('__remember'),
                        'user_login' => $user_login,
                        'user_password' => $user_password,
                    ]);
                }
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_email($result, $tag){
            if($tag->name !== '__user_email'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__signup')){
                return $result;
            }
            $user_email = __get_posted_data('__user_email');
            if($user_email === ''){
                return $result; // required first
            }
            if(email_exists($user_email)){
                $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: This email is already registered. Please choose another one.')));
                return $result;
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_password($result, $tag){
            if($tag->name !== '__user_password' and $tag->name !== '__user_password_confirm'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__signup')){
                return $result;
            }
            $user_password = __get_posted_data('__user_password');
            $user_password_confirm = __get_posted_data('__user_password_confirm');
            switch($tag->name){
                case '__user_password':
                    if($user_password === ''){
                        return $result; // required first
                    }
                    if(strpos(wp_unslash($user_password), '\\') !== false){
                        $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: Passwords may not contain the character "\\".')));
                        return $result;
                    }
                    break;
                case '__user_password_confirm':
                    if($user_password_confirm === ''){
                        return $result; // required first
                    }
                    if($user_password_confirm !== $user_password){
                        $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: Passwords don&#8217;t match. Please enter the same password in both password fields.')));
                        return $result;
                    }
                    break;
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_validate_text($result, $tag){
            if($tag->name !== '__user_login'){
                return $result;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $result;
            }
            if(!$contact_form->is_true('__signup')){
                return $result;
            }
            $user_login = __get_posted_data('__user_login');
            if($user_login === ''){
                return $result; // required first
            }
            if(!validate_username($user_login)){
                $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: This username is invalid because it uses illegal characters. Please enter a valid username.')));
                return $result;
            }
            $illegal_user_logins = (array) apply_filters('illegal_user_logins', ['admin']);
            if(in_array(strtolower($user_login), array_map('strtolower', $illegal_user_logins), true)){
                $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: Sorry, that username is not allowed.')));
                return $result;
            }
            if(username_exists($user_login)){
                $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: This username is already registered. Please choose another one.')));
                return $result;
            }
            if(wpcf7_is_email($user_login)){
                if(email_exists($user_login)){
                    $result->invalidate($tag, wp_strip_all_tags(__('<strong>Error</strong>: This email is already registered. Please choose another one.')));
                    return $result;
                }
            }
            return $result;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
