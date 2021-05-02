<?php

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_has_tag')){
    function __cf7_has_tag($contact_form, $tag){
        static $tags = [];
        $id = $contact_form->id();
        if(!isset($tags[$id])){
            $tags[$id] = wp_list_pluck($contact_form->scan_form_tags(), 'type', 'name');
        }
        return isset($tags[$id][$tag]);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__get_posted_data')){
    function __get_posted_data($name = ''){
        return isset($_POST[$name]) ? $_POST[$name] : '';
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_bootstrap')){
    function __cf7_support_bootstrap(){
        if(!class_exists('__cf7_bootstrap')){
            require_once(plugin_dir_path(__FILE__) . 'classes/bootstrap.php');
        }
		__off('wpcf7_init', 'wpcf7_add_form_tag_acceptance');
		__off('wpcf7_init', 'wpcf7_add_form_tag_checkbox');
		__off('wpcf7_init', 'wpcf7_add_form_tag_date');
		__off('wpcf7_init', 'wpcf7_add_form_tag_file');
		__off('wpcf7_init', 'wpcf7_add_form_tag_number');
		__off('wpcf7_init', 'wpcf7_add_form_tag_select');
		__off('wpcf7_init', 'wpcf7_add_form_tag_text');
		__off('wpcf7_init', 'wpcf7_add_form_tag_textarea');
		__one('wpcf7_autop_or_not', '__return_false');
		__one('wpcf7_enqueue_scripts', ['__cf7_bootstrap', 'wpcf7_enqueue_scripts']);
		__one('wpcf7_enqueue_styles', ['__cf7_bootstrap', 'wpcf7_enqueue_styles']);
        __one('wpcf7_init', ['__cf7_bootstrap', 'wpcf7_init']);
		__one('wpcf7_validate_password', ['__cf7_bootstrap', 'wpcf7_password_validation_filter'], 10, 2);
		__one('wpcf7_validate_password*', ['__cf7_bootstrap', 'wpcf7_password_validation_filter'], 10, 2);
		__one('wpcf7_validate_radio*', 'wpcf7_checkbox_validation_filter', 10, 2);
		__require_simplehtmldom();
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_close_date')){
    function __cf7_support_close_date(){
        __one('do_shortcode_tag', function($output, $tag, $attr, $m){
            if('contact-form-7' !== $tag){
                return $output;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $output;
            }
            $close_date = $contact_form->pref('__close_date');
            if($close_date === null){
                return $output;
            }
            $close_time = strtotime($close_date);
            if(current_time('timestamp') > $close_time){
                $close_date_message = $contact_form->pref('__close_date_message');
                if($close_date_message === null){
                    $close_date_message = sprintf(__('Schedule for: %s'), '<strong>' . sprintf(__('%1$s at %2$s'), date_i18n(get_option('date_format'), $close_time), date_i18n(get_option('time_format'), $close_time)) . '</strong>');
                }
                return apply_filters('__cf7_close_date_message', $close_date_message, $contact_form);
            }
            return $output;
        }, 10, 4);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_close_limit')){
    function __cf7_support_close_limit(){
        __one('do_shortcode_tag', function($output, $tag, $attr, $m){
            if('contact-form-7' !== $tag){
                return $output;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $output;
            }
            $close_limit = $contact_form->pref('__close_limit');
            if($close_limit === null){
                return $output;
            }
            $args = apply_filters('__close_limit_args', [
                'meta_query' => [
                    [
                        'key' => '__id',
                        'value' => $contact_form->id(),
                    ],
                ],
                'post_type' => '__submission',
                'posts_per_page' => -1,
            ], $contact_form);
            $posts = apply_filters('__close_limit_posts', get_posts($args), $contact_form);
            if(count($posts) >= (int) $close_limit){
                $close_limit_message = $contact_form->pref('__close_limit_message');
                if($close_limit_message === null){
                    $close_limit_message = __('Comments are closed.');
                }
                return apply_filters('__cf7_close_limit_message', $close_limit_message, $contact_form);
            }
            return $output;
        }, 10, 4);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_editor')){
    function __cf7_support_editor(){
        if(!class_exists('__cf7_editor')){
            require_once(plugin_dir_path(__FILE__) . 'classes/editor.php');
        }
        __one('admin_enqueue_scripts', ['__cf7_editor', 'admin_enqueue_scripts']);
		__one('wpcf7_autop_or_not', '__return_false');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_login')){
    function __cf7_support_login(){
        if(!class_exists('__cf7_login')){
            require_once(plugin_dir_path(__FILE__) . 'classes/login.php');
        }
        __one('do_shortcode_tag', ['__cf7_login', 'do_shortcode_tag'], 10, 4);
        __one('wpcf7_mail_sent', ['__cf7_login', 'wpcf7_mail_sent']);
        __one('wpcf7_validate_email*', ['__cf7_login', 'wpcf7_validate_email'], 10, 2);
        __one('wpcf7_validate_password*', ['__cf7_login', 'wpcf7_validate_password'], 10, 2);
        __one('wpcf7_validate_text*', ['__cf7_login', 'wpcf7_validate_text'], 10, 2);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_open_date')){
    function __cf7_support_open_date(){
        __one('do_shortcode_tag', function($output, $tag, $attr, $m){
            if('contact-form-7' !== $tag){
                return $output;
            }
            $contact_form = wpcf7_get_current_contact_form();
            if($contact_form === null){
                return $output;
            }
            $open_date = $contact_form->pref('__open_date');
            if($open_date === null){
                return $output;
            }
            $open_time = strtotime($open_date);
            if(current_time('timestamp') < $open_time){
                $open_date_message = $contact_form->pref('__open_date_message');
                if($open_date_message === null){
                    $open_date_message = sprintf(__('Schedule for: %s'), '<strong>' . sprintf(__('%1$s at %2$s'), date_i18n(get_option('date_format'), $open_time), date_i18n(get_option('time_format'), $open_time)) . '</strong>');
                }
                return apply_filters('__cf7_open_date_message', $open_date_message, $contact_form);
            }
            return $output;
        }, 10, 4);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_redirect')){
    function __cf7_support_redirect(){
        if(!class_exists('__cf7_redirect')){
            require_once(plugin_dir_path(__FILE__) . 'classes/redirect.php');
        }
        __one('wpcf7_enqueue_scripts', ['__cf7_redirect', 'wpcf7_enqueue_scripts']);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_select2')){
    function __cf7_support_select2(){
        if(!class_exists('__cf7_select2')){
            require_once(plugin_dir_path(__FILE__) . 'classes/select2.php');
        }
        __one('wpcf7_enqueue_scripts', ['__cf7_select2', 'wpcf7_enqueue_scripts']);
        __one('wpcf7_enqueue_styles', ['__cf7_select2', 'wpcf7_enqueue_styles']);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_signup')){
    function __cf7_support_signup(){
        if(!class_exists('__cf7_signup')){
            require_once(plugin_dir_path(__FILE__) . 'classes/signup.php');
        }
        __one('do_shortcode_tag', ['__cf7_signup', 'do_shortcode_tag'], 10, 4);
        __one('wpcf7_mail_sent', ['__cf7_signup', 'wpcf7_mail_sent']);
        __one('wpcf7_validate_email*', ['__cf7_signup', 'wpcf7_validate_email'], 10, 2);
        __one('wpcf7_validate_password*', ['__cf7_signup', 'wpcf7_validate_password'], 10, 2);
        __one('wpcf7_validate_text*', ['__cf7_signup', 'wpcf7_validate_text'], 10, 2);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_storage')){
    function __cf7_support_storage(){
        if(!class_exists('__cf7_storage')){
            require_once(plugin_dir_path(__FILE__) . 'classes/storage.php');
        }
        __one('do_shortcode_tag', ['__cf7_storage', 'do_shortcode_tag'], 10, 4);
        __one('init', ['__cf7_storage', 'init']);
        __one('shortcode_atts_wpcf7', ['__cf7_storage', 'shortcode_atts_wpcf7'], 10, 3);
		__one('wpcf7_enqueue_scripts', ['__cf7_storage', 'wpcf7_enqueue_scripts']);
        __one('wpcf7_form_hidden_fields', ['__cf7_storage', 'wpcf7_form_hidden_fields']);
        __one('wpcf7_mail_sent', ['__cf7_storage', 'wpcf7_mail_sent']);
		__one('wpcf7_verify_nonce', 'is_user_logged_in');
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(!function_exists('__cf7_support_submit')){
    function __cf7_support_submit(){
        if(!class_exists('__cf7_submit')){
            require_once(plugin_dir_path(__FILE__) . 'classes/submit.php');
        }
        __one('wpcf7_enqueue_scripts', ['__cf7_submit', 'wpcf7_enqueue_scripts']);
        __one('wpcf7_enqueue_styles', ['__cf7_submit', 'wpcf7_enqueue_styles']);
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
