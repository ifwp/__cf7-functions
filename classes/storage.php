<?php

if(!class_exists('__cf7_storage')){
    final class __cf7_storage {

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
            $post_id = $contact_form->shortcode_attr('__post_id');
            if($post_id !== null){
                if(!current_user_can('edit_post', $post_id)){
                    $edit_post_message = $contact_form->shortcode_attr('__edit_post_message');
                    if($edit_post_message === null){
                        $edit_post_message = $contact_form->pref('__edit_post_message');
                        if($edit_post_message === null){
                            if(get_post_type($post_id) === 'post'){
                                $edit_post_message = __('Sorry, you are not allowed to edit this post.');
                            } else {
                                $edit_post_message = __('Sorry, you are not allowed to edit this item.');
                            }
                            $edit_post_message .=  ' ' . __('You need a higher level of permission.');
                        }
                    }
                    return apply_filters('__cf7_edit_post_message', $edit_post_message, $post_id);
                }
            }
            $user_id = $contact_form->shortcode_attr('__user_id');
            if($user_id !== null){
                if(!current_user_can('edit_user', $user_id)){
                    $edit_user_message = $contact_form->shortcode_attr('__edit_user_message');
                    if($edit_user_message === null){
                        $edit_user_message = $contact_form->pref('__edit_user_message');
                        if($edit_user_message === null){
                            $edit_user_message = __('Sorry, you are not allowed to edit this user.') . ' ' . __('You need a higher level of permission.');
                        }
                    }
                    return apply_filters('__cf7_edit_user_message', $edit_user_message, $user_id);
                }
            }
            return $output;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function init(){
            register_post_type('__submission', [
                'labels' => __post_type_labels('Submission', 'Submissions', false),
                'show_in_admin_bar' => false,
                'show_in_menu' => 'wpcf7',
                'show_ui' => true,
                'supports' => ['custom-fields', 'title'],
            ]);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function shortcode_atts_wpcf7($out, $pairs, $atts){
            $id = (int) $atts['id'];
            $title = trim($atts['title']);
            if(!$contact_form = wpcf7_contact_form($id)){
                $contact_form = wpcf7_get_contact_form_by_title($title);
            }
            if(!$contact_form){
                return $out;
            }
            $post_id = 0;
            if(isset($atts['post_id'])){
                $post = null;
                if(is_numeric($atts['post_id'])){
                    $post = get_post($atts['post_id']);
                } elseif($atts['post_id'] == 'current'){
                    $post = get_post();
                }
                if($post){
                    $post_id = $post->ID;
                }
            }
            $out['post_id'] = $post_id;
            if($post_id){
                $scanned_form_tags = $contact_form->scan_form_tags();
                foreach($scanned_form_tags as $scanned_form_tag){
                    $name = $scanned_form_tag->name;
                    if($name != 'post_id'){
                        if(metadata_exists('post', $post_id, $name)){
                            $out[$name] = get_post_meta($post_id, $name, true);
                        }
                    }
                }
            }
            $user_id = 0;
            if(isset($atts['user_id'])){
                $user = null;
                if(is_numeric($atts['user_id'])){
                    $user = get_userdata($atts['user_id']);
                } elseif($atts['user_id'] == 'current'){
                    $user = wp_get_current_user();
                }
                if($user){
                    $user_id = $user->ID;
                }
            }
            $out['user_id'] = $user_id;
            if($user_id){
                $scanned_form_tags = $contact_form->scan_form_tags();
                foreach($scanned_form_tags as $scanned_form_tag){
                    $name = $scanned_form_tag->name;
                    if($name != 'user_id'){
                        if(metadata_exists('user', $user_id, $name)){
                            $out[$name] = get_user_meta($user_id, $name, true);
                        }
                    }
                }
            }
            return $out;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_form_hidden_fields($hidden_fields){
            $contact_form = wpcf7_get_current_contact_form();
            $hidden_fields['__nonce'] = '';
            $hidden_fields['__referer'] = isset($_GET['__referer']) ? wpcf7_sanitize_query_var($_GET['__referer']) : '';
            $hidden_fields['__uniqid'] = uniqid();
            if($contact_form !== null){
                $post_id = (int) $contact_form->shortcode_attr('__post_id');
                $hidden_fields['__edit_post_nonce'] = wp_create_nonce('__edit_post-' . $post_id);
                $user_id = (int) $contact_form->shortcode_attr('__user_id');
                $hidden_fields['__edit_user_nonce'] = wp_create_nonce('__edit_user-' . $user_id);
            }
            return $hidden_fields;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_mail_sent($contact_form){
            $submission = WPCF7_Submission::get_instance();
            if($submission !== null){
                $redirect = $contact_form->pref('__redirect');
                if($redirect !== null){
                    $redirect_message = $contact_form->pref('__redirect_message');
                    if($redirect_message === null){
                        $redirect_message = __('Please wait...');
                    }
                    $submission->set_response($redirect_message);
                }
                $meta_data = [
                    '__container_post_id' => $submission->get_meta('container_post_id'),
                    '__current_user_id' => $submission->get_meta('current_user_id'),
                    '__id' => $contact_form->id(),
                    '__locale' => $contact_form->locale(),
                    '__name' => $contact_form->name(),
                    '__referer' => isset($_POST['__referer']) ? $_POST['__referer'] : '',
                    '__remote_ip' => $submission->get_meta('remote_ip'),
                    '__remote_port' => $submission->get_meta('remote_port'),
                    '__status' => $submission->get_status(),
                    '__timestamp' => $submission->get_meta('timestamp'),
                    '__title' => $contact_form->title(),
                    '__uniqid' => isset($_POST['__uniqid']) ? $_POST['__uniqid'] : '',
                    '__unit_tag' => $submission->get_meta('unit_tag'),
                    '__url' => $submission->get_meta('url'),
                    '__user_agent' => $submission->get_meta('user_agent'),
                ];
                $posted_data = $submission->get_posted_data();
                $post_id = (int) $contact_form->shortcode_attr('__post_id');
                $update = false;
                if($post_id){
                    $nonce = isset($_POST['__edit_post_nonce']) ? $_POST['__edit_post_nonce'] : '';
                    if(wp_verify_nonce($nonce, '__edit_post-' . $post_id)){
                        $update = true;
                    } else {
                        $post_id = 0;
                        $submission->set_response(__('Error while saving.'));
                    }
                } else {
                    $post_id = wp_insert_post([
                        'post_status' => 'private',
                        'post_title' => sprintf('[contact-form-7 id="%1$d" title="%2$s"]', $contact_form->id(), $contact_form->title()),
                        'post_type' => '__submission',
                    ]);
                }
                if($post_id){
                    foreach($meta_data as $key => $value){
                        add_post_meta($post_id, $key, $value);
                    }
                    if($posted_data){
                        foreach($posted_data as $key => $value){
                            update_post_meta($post_id, $key, $value);
                        }
                    }
                    if($update){
                        __do('__cf7_insert_post', $post_id);
                    } else {
                        __do('__cf7_update_post', $post_id);
                    }
                }
                $user_id = (int) $contact_form->shortcode_attr('__user_id');
                if($user_id){
                    $nonce = isset($_POST['__edit_user_nonce']) ? $_POST['__edit_user_nonce'] : '';
                    if(!wp_verify_nonce($nonce, '__edit_user-' . $user_id)){
                        $user_id = 0;
                    }
                }
                if($user_id){
                    foreach($meta_data as $key => $value){
                        add_user_meta($user_id, $key, $value);
                    }
                    if($posted_data){
                        foreach($posted_data as $key => $value){
                            update_user_meta($user_id, $key, $value);
                        }
                    }
                    __do('__cf7_update_user', $user_id);
                }
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
