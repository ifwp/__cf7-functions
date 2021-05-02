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
			$message = '';
            $post_id = $contact_form->shortcode_attr('__post_id');
            if($post_id !== null){
				if($post_id){
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
					} else {
						if(get_post_status($post_id) === 'trash'){
							$edit_post_message = __('You can&#8217;t edit this item because it is in the Trash. Please restore it and try again.');
							return apply_filters('__cf7_edit_post_message', $edit_post_message, $post_id);
						} else {
							if(isset($_GET['__referer']) and $_GET['__referer'] === get_post_meta($post_id, '__uniqid', true)){
								$message = $contact_form->pref('__update_message');
								if($message === null){
									$message = get_post_meta($post_id, '__response', true);
								}
							}
						}
					}
				}
            }
			$post_type = $contact_form->shortcode_attr('__post_type');
            if($post_type !== null){
				if($post_type){
					if(!post_type_exists($post_type)){
						return apply_filters('__cf7_edit_post_type_message', __('Invalid post type.'), $post_type);
					}
				}
			}
            /*$user_id = $contact_form->shortcode_attr('__user_id');
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
            }*/
			if($message){
				// pendiente: apply_filters, alert-dismissible
				$message = '<div class="__update_message">' . $message . '</div>';
				$output = $message . $output;
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
            if(isset($atts['__post_id'])){
                $post = null;
                if(is_numeric($atts['__post_id'])){
                    $post = get_post($atts['__post_id']);
                } elseif($atts['__post_id'] == 'current'){
                    $post = get_post();
                }
                if($post){
                    $post_id = $post->ID;
                }
            }
            $out['__post_id'] = $post_id;
            if($post_id){
                $scanned_form_tags = $contact_form->scan_form_tags();
                foreach($scanned_form_tags as $scanned_form_tag){
                    $name = $scanned_form_tag->name;
                    if($name != '__post_id'){
                        if(metadata_exists('post', $post_id, $name)){
                            $out[$name] = get_post_meta($post_id, $name, true);
							if(is_array($out[$name])){
								$out[$name] = $out[$name][0];
							}
                        }
                    }
                }
            }
			$post_type = '';
            if(isset($atts['__post_type'])){
                if(post_type_exists($atts['__post_type'])){
                    $post_type = $atts['__post_type'];
                }
            }
            $out['__post_type'] = $post_type;
            /*$user_id = 0;
            if(isset($atts['__user_id'])){
                $user = null;
                if(is_numeric($atts['__user_id'])){
                    $user = get_userdata($atts['__user_id']);
                } elseif($atts['__user_id'] == 'current'){
                    $user = wp_get_current_user();
                }
                if($user){
                    $user_id = $user->ID;
                }
            }
            $out['__user_id'] = $user_id;
            if($user_id){
                $scanned_form_tags = $contact_form->scan_form_tags();
                foreach($scanned_form_tags as $scanned_form_tag){
                    $name = $scanned_form_tag->name;
                    if($name != '__user_id'){
                        if(metadata_exists('user', $user_id, $name)){
                            $out[$name] = get_user_meta($user_id, $name, true);
                        }
                    }
                }
            }*/
            return $out;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_scripts(){
			wp_enqueue_script('__cf7-storage', plugin_dir_url(__CF7_FILE) . 'assets/storage.js', ['contact-form-7'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/storage.js'), true);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_form_hidden_fields($hidden_fields){
            $contact_form = wpcf7_get_current_contact_form();
            $hidden_fields['__referer'] = isset($_GET['__referer']) ? wpcf7_sanitize_query_var($_GET['__referer']) : '';
            $hidden_fields['__uniqid'] = uniqid();
            if($contact_form !== null){
                $post_id = $contact_form->shortcode_attr('__post_id');
				if($post_id){
					$uniqid = get_post_meta($post_id, '__uniqid', true);
					if($uniqid){
						$hidden_fields['__uniqid'] = $uniqid;
					}
					$hidden_fields['__post_id'] = $post_id;
                	$hidden_fields['__edit_post_nonce'] = wp_create_nonce('__edit_post-' . $post_id);
				}
				$post_type = $contact_form->shortcode_attr('__post_type');
				if($post_type){
					$hidden_fields['__post_type'] = $post_type;
				}
                /*$user_id = $contact_form->shortcode_attr('__user_id');
				if($user_id){
					$hidden_fields['__user_id'] = $user_id;
                	$hidden_fields['__edit_user_nonce'] = wp_create_nonce('__edit_user-' . $user_id);
				}*/
            }
            return $hidden_fields;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_mail_sent($contact_form){
			if($contact_form->is_true('__login')){
                return;
            }
			if($contact_form->is_true('__signup')){
                return;
            }
            $submission = WPCF7_Submission::get_instance();
            if($submission !== null){
                /*$redirect = $contact_form->pref('__redirect');
                if($redirect !== null){
                    $redirect_message = $contact_form->pref('__redirect_message');
                    if($redirect_message === null){
                        $redirect_message = __('Please wait...');
                    }
                    $submission->set_response($redirect_message);
                }*/
				$posted_data = $submission->get_posted_data();
				if($posted_data){
					$meta_data = [
						'__container_post_id' => $submission->get_meta('container_post_id'),
						'__current_user_id' => $submission->get_meta('current_user_id'),
						'__id' => $contact_form->id(),
						'__locale' => $contact_form->locale(),
						'__name' => $contact_form->name(),
						'__referer' => __get_posted_data('__referer'),
						'__remote_ip' => $submission->get_meta('remote_ip'),
						'__remote_port' => $submission->get_meta('remote_port'),
						'__response' => $submission->get_response(),
						'__status' => $submission->get_status(),
						'__timestamp' => $submission->get_meta('timestamp'),
						'__title' => $contact_form->title(),
						'__uniqid' => __get_posted_data('__uniqid'),
						'__unit_tag' => $submission->get_meta('unit_tag'),
						'__url' => $submission->get_meta('url'),
						'__user_agent' => $submission->get_meta('user_agent'),
					];
					$post_id = __get_posted_data('__post_id');
					$update = false;
					if($post_id){
						$nonce = __get_posted_data('__edit_post_nonce');
						if(wp_verify_nonce($nonce, '__edit_post-' . $post_id)){
							/*if(get_post_status($post_id) !== 'trash'){
								$update = true;
							} else {
								$post_id = 0;
								$submission->set_response(__('Error while saving.'));
							}*/
							$update = true;
						} else {
							$post_id = 0;
							$submission->set_response(__('Error while saving.'));
						}
					} else {
						$post_type = __get_posted_data('__post_type');
						if(!$post_type){
							$post_type = '__submission';
						}
						$post_id = wp_insert_post([
							'post_status' => 'private',
							'post_title' => sprintf('[contact-form-7 id="%1$d" title="%2$s"]', $contact_form->id(), $contact_form->title()),
							'post_type' => $post_type,
						]);
					}
					if($post_id){
						foreach($meta_data as $key => $value){
							update_post_meta($post_id, $key, $value);
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
					/*$user_id = __get_posted_data('__user_id');
					if($user_id){
						$nonce = __get_posted_data('__edit_user_nonce');
						if(!wp_verify_nonce($nonce, '__edit_user-' . $post_id)){
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
					}*/
				}
            }
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
