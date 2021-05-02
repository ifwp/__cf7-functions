
// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

var __cf7_ace = {};

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(typeof __cf7_ace_edit !== 'function'){
    function __cf7_ace_edit(id = ''){
        if(jQuery('#' + id).length){
        	jQuery('#' + id).hide();
        	jQuery('<div class="__cf7-editor-container" id="' + id + '-editor-container"><div id="' + id + '-editor"></div></div>').insertBefore('#' + id);
            __cf7_ace[id] = ace.edit(id + '-editor');
            __cf7_ace[id].$blockScrolling = Infinity;
            __cf7_ace[id].setOptions({
            	enableBasicAutocompletion: true,
            	enableLiveAutocompletion: true,
            	fontSize: 14,
                maxLines: Infinity,
                minLines: 5,
                wrap: true,
            });
            __cf7_ace[id].getSession().on('change', function(){
                jQuery('#' + id).val(__cf7_ace[id].getSession().getValue()).trigger('change');
            });
            __cf7_ace[id].getSession().setMode('ace/mode/html');
            __cf7_ace[id].getSession().setValue(jQuery('#' + id).val());
            __cf7_ace[id].setTheme('ace/theme/monokai');
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(typeof __cf7_ace_destroy !== 'function'){
    function __cf7_ace_destroy(id = ''){
        if(typeof __cf7_ace[id] !== 'undefined'){
            __cf7_ace[id].destroy();
            jQuery('#' + id + '-editor-container').remove();
        	jQuery('#' + id).show();
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(typeof __cf7_ace_mail !== 'function'){
    function __cf7_ace_mail(){
        if(jQuery('#wpcf7-mail-use-html').prop('checked')){
            __cf7_ace_edit('wpcf7-mail-body');
        } else {
            __cf7_ace_destroy('wpcf7-mail-body');
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

if(typeof __cf7_ace_mail_2 !== 'function'){
    function __cf7_ace_mail_2(){
        if(jQuery('#wpcf7-mail-2-use-html').prop('checked')){
            __cf7_ace_edit('wpcf7-mail-2-body');
        } else {
            __cf7_ace_destroy('wpcf7-mail-2-body');
        }
    }
}

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

jQuery(function($){
	if(typeof ace != 'undefined'){
        ace.config.set('basePath', 'https://cdnjs.cloudflare.com/ajax/libs/ace/1.4.12');
       	ace.require('ace/ext/language_tools');
        __cf7_ace_edit('wpcf7-form');
        __cf7_ace_mail();
        __cf7_ace_mail_2();
        $('#wpcf7-mail-use-html').on('change', __cf7_ace_mail);
        $('#wpcf7-mail-2-use-html').on('change', __cf7_ace_mail_2);
	}
});

// ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
