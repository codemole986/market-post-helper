var intrinio_last_search = '';
var intrinio_last_replace = '';

(function() {

    function intrinio_preload_content() {
        
        jQuery('#intrinio_full_loading').addClass('loading');

        jQuery.ajax({
            url: ajaxurl + "?action=intrinio_preload",
            data: {
                'title': jQuery('#title').val(),
                'content': tinyMCE.get('content').getContent()
            },
            method: 'POST',
            statusCode: {
                500: function() {
                  intrinio_preload_content();
                }
            }
        }).done(function( data ) {
            
            jQuery('#intrinio_full_loading').removeClass('loading');

            if(data.title) {
                jQuery('#title').val(data.title);
            }

            if(data.content) {
                tinyMCE.get('content').setContent(data.content);   
            }
            
        });
    }

    tinymce.PluginManager.add( 'intrinio_preload', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        console.log(url);
        editor.addButton('intrinio_preload', {
            title: 'Intrinio Preload',
            cmd: 'intrinio_preload',
            image: url + '/../img/icon-preload.png?v=0.1',
        });


        editor.addCommand('intrinio_preload', function() {
            intrinio_preload_content();
        });
    });
})();