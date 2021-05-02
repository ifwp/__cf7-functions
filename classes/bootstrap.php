<?php

if(!class_exists('__cf7_bootstrap')){
    final class __cf7_bootstrap {

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // private static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function checkbox($html = '', $tag = null){
            $html = str_get_html($html);
            $type = 'checkbox';
            if(in_array($tag->basetype, ['checkbox', 'radio'])){
                $type = $tag->basetype;
            }
			foreach($html->find('.wpcf7-list-item') as $li){
				$li->addClass('custom-control custom-' . $type);
				if(self::inline($tag)){
                    $li->addClass('custom-control-inline');
                }
				$input = $li->find('input', 0);
				$input->addClass('custom-control-input');
				$input->id = $tag->name . '_' . str_replace('-', '_', sanitize_title($input->value));
				$label = $li->find('.wpcf7-list-item-label', 0);
				$label->addClass('custom-control-label');
				$label->for = $input->id;
				$label->tag = 'label';
				$li->innertext = $input->outertext . $label->outertext;
			}
            return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function file($html = '', $tag = null){
            $html = str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
            $wrapper->addClass('custom-file');
            $input = $wrapper->find('input', 0);
            $input->addClass('custom-file-input');
			$input->id = $tag->name;
            $input->outertext = $input->outertext . '<label class="custom-file-label" for="' . $input->id . '" data-browse="' . self::file_text($tag) . '">' . self::file_label($tag) . '</label>';
        	return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static function file_label($tag = null, $fallback = ''){
			if($tag->has_option('__file_label')){
                return $tag->get_option('__file_label', '', true);
            }
            if(!$fallback){
                $fallback = __('Select Files');
            }
            return $fallback;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static function file_text($tag = null, $fallback = ''){
			if($tag->has_option('__file_text')){
                return $tag->get_option('__file_text', '', true);
            }
            if(!$fallback){
                $fallback = __('Select');
            }
            return $fallback;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        private static function inline($tag = null, $fallback = false){
            if($tag->has_option('__inline')){
                $inline = $tag->get_option('__inline', '', true);
				return (in_array($inline, ['off', 'false']) ? false : boolval($inline));
            }
            return $fallback;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function range($html = '', $tag = null){
            $html = str_get_html($html);
            $range = $wrapper->find('range', 0);
            $range->addClass('form-control-range');
            return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function select($html = '', $tag = null){
            $html = str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
			$select = $wrapper->find('select', 0);
			$select->addClass('custom-select');
			$size = self::size($tag);
			if($size){
				$select->addClass('custom-select-' . $size);
			}
            return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function size($tag = null, $fallback = ''){
            if($tag->has_option('__size')){
                $size = $tag->get_option('__size', '', true);
                if(in_array($size, ['sm', 'md', 'lg'])){
                    return ($size === 'md' ? '' : $size);
                }
            }
            return $fallback;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function text($html = '', $tag = null){
            $html = str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
			$input = $wrapper->find('input', 0);
			$input->addClass('form-control');
			$size = self::size($tag);
			if($size){
				$input->addClass('form-control-' . $size);
			}
            return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

		private static function textarea($html = '', $tag = null){
            $html = str_get_html($html);
            $wrapper = $html->find('.wpcf7-form-control-wrap', 0);
			$textarea = $wrapper->find('textarea', 0);
			$textarea->addClass('form-control');
			$size = self::size($tag);
			if($size){
				$textarea->addClass('form-control-' . $size);
			}
            return $html;
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        //
        // public static
        //
        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_init(){
           wpcf7_add_form_tag('acceptance', function($tag){
                $html = wpcf7_acceptance_form_tag_handler($tag);
                return self::checkbox($html, $tag);
            }, [
        		'name-attr' => true,
			]);
			wpcf7_add_form_tag(['checkbox', 'checkbox*', 'radio', 'radio*'], function($tag){
                $html = wpcf7_checkbox_form_tag_handler($tag);
                return self::checkbox($html, $tag);
            }, [
				'multiple-controls-container' => true,
        		'name-attr' => true,
                'selectable-values' => true,
        	]);
			wpcf7_add_form_tag(['date', 'date*'], function($tag){
                $html = wpcf7_date_form_tag_handler($tag);
                return self::text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['file', 'file*'], function($tag){
                $html = wpcf7_file_form_tag_handler($tag);
                return self::file($html, $tag);
            }, [
				'file-uploading' => true,
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['number', 'number*'], function($tag){
                $html = wpcf7_number_form_tag_handler($tag);
				return self::text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['range', 'range*'], function($tag){
				$html = wpcf7_number_form_tag_handler($tag);
                return self::range($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
			wpcf7_add_form_tag(['select', 'select*'], function($tag){
                $html = wpcf7_select_form_tag_handler($tag);
                return self::select($html, $tag);
            }, [
        		'name-attr' => true,
                'selectable-values' => true,
        	]);
			wpcf7_add_form_tag(['email', 'email*', 'password', 'password*', 'tel', 'tel*', 'text', 'text*', 'url', 'url*'], function($tag){
                $html = wpcf7_text_form_tag_handler($tag);
                return self::text($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
            wpcf7_add_form_tag(['textarea', 'textarea*'], function($tag){
                $html = wpcf7_textarea_form_tag_handler($tag);
                return self::textarea($html, $tag);
            }, [
        		'name-attr' => true,
        	]);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_scripts(){
			wp_add_inline_script('bs-custom-file-input', 'jQuery(function(){ bsCustomFileInput.init(); });');
            wp_enqueue_script('bs-custom-file-input', 'https://cdn.jsdelivr.net/npm/bs-custom-file-input@1.3.4/dist/bs-custom-file-input.min.js', ['contact-form-7'], '1.3.4', true);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_enqueue_styles(){
            wp_enqueue_style('__cf7-bootstrap', plugin_dir_url(__CF7_FILE) . 'assets/bootstrap.css', ['contact-form-7'], filemtime(plugin_dir_path(__CF7_FILE) . 'assets/bootstrap.css'));
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

        public static function wpcf7_password_validation_filter($result, $tag){
            $name = $tag->name;
			$value = isset($_POST[$name]) ? trim(wp_unslash(strtr((string) $_POST[$name], "\n", " "))) : '';
			if('password' == $tag->basetype){
				if($tag->is_required() and '' === $value){
					$result->invalidate($tag, wpcf7_get_message('invalid_required'));
				}
			}
			return wpcf7_text_validation_filter($result, $tag);
        }

        // ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

    }
}
