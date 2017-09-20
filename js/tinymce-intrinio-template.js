var intrinio_last_template = '';
(function() {


    tinymce.PluginManager.add( 'intrinio_template', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        editor.addButton('intrinio_template', {
            title: 'Insert Intrinio Template',
            cmd: 'intrinio_template',
            image: url + '/../img/icon-template.png?v=0.1',
        });


        editor.addCommand('intrinio_template', function() {
            
            editor.windowManager.open( {
                title: 'Insert a Shortcode',
                body: [{
                    type: 'listbox',
                    name: 'template',
                    label: 'Template',
                    value: intrinio_last_template,
                    values: intrinio_templates,
                    id : 'intrinio_input_template',
                    minWidth: 500
                }],
                onsubmit: function( e ) {
                    if ((e.data.template == '')) return false;
                    intrinio_last_template = e.data.template;

                    /*
                    var content = editor.getContent();
                    content = content + intrinio_templates_store[e.data.template][1];
                    editor.setContent(content);
                    */

                    editor.insertContent(intrinio_templates_store[e.data.template][1]);
                }
            });

            /*
            // Check we have selected some text selected
            var text = editor.selection.getContent({
                'format': 'html'
            });
            if ( text.length === 0 ) {
                alert( 'Please select some text.' );
                return;
            }

            // Ask the user to enter a CSS class
            var result = prompt('Enter the CSS class');
            if ( !result ) {
                // User cancelled - exit
                return;
            }
            if (result.length === 0) {
                // User didn't enter anything - exit
                return;
            }

            // Insert selected text back into editor, wrapping it in an anchor tag
            editor.execCommand('mceReplaceContent', false, '<span class="' + result + '">' + text + '</span>');
            // tinymce.activeEditor.execCommand('mceInsertContent', false, "some text");
            */
        });
    });
})();