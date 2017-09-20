var intrinio_last_search = '';
var intrinio_last_replace = '';

(function() {


    tinymce.PluginManager.add( 'intrinio_replacecode', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        console.log(url);
        editor.addButton('intrinio_replacecode', {
            title: 'Intrinio Replacer',
            cmd: 'intrinio_replacecode',
            image: url + '/../img/icon-replace.png?v=0.1',
        });


        editor.addCommand('intrinio_replacecode', function() {
            
            editor.windowManager.open( {
                title: 'Replace Tikcer',
                body: [{
                    type: 'listbox',
                    name: 'replace_what',
                    label: 'Replace What?',
                    values: [
                        {'text': 'Ticker', 'value': '0'},
                        {'text': 'Item', 'value': '1'},
                        {'text': 'Anything', 'value': '2'},
                    ],
                    id : 'intrinio_input_replace_what',
                    minWidth: 500
                },
                {
                    type: 'textbox',
                    name: 'search',
                    label: 'Find',
                    value: intrinio_last_search,
                    id : 'intrinio_input_search',
                    minWidth: 500
                },
                {
                    type: 'textbox',
                    name: 'replace',
                    label: 'Replace',
                    value: intrinio_last_replace,
                    id : 'intrinio_input_replace',
                    minWidth: 500
                }],
                onsubmit: function( e ) {
                    if ((e.data.search == '') || (e.data.replace == '')) return false;

                    intrinio_last_search = e.data.search;
                    intrinio_last_replace = e.data.replace;

                    var content = editor.getContent();
                    var title = jQuery('#title').val();
                    if (e.data.replace_what == '0') {
                        content = content.split(' ticker=' + e.data.search.trim() + ' ').join(' ticker=' + e.data.replace.trim() + ' ');
                        content = content.split(' ticker=' + e.data.search.trim() + ']').join(' ticker=' + e.data.replace.trim() + ']');

                        title = title.split(' ticker=' + e.data.search.trim() + ' ').join(' ticker=' + e.data.replace.trim() + ' ');
                        title = title.split(' ticker=' + e.data.search.trim() + ' ').join(' ticker=' + e.data.replace.trim() + ' ');

                    } else if (e.data.replace_what == '1') {
                        content = content.split(' item=' + e.data.search.trim() + ' ').join(' item=' + e.data.replace.trim() + ' ');
                        content = content.split(' item=' + e.data.search.trim() + ']').join(' item=' + e.data.replace.trim() + ']');

                        title = title.split(' item=' + e.data.search.trim() + ' ').join(' item=' + e.data.replace.trim() + ' ');
                        title = title.split(' item=' + e.data.search.trim() + ']').join(' item=' + e.data.replace.trim() + ']');

                    } else {
                        content = content.split(e.data.search).join(e.data.replace);
                        title = title.split(e.data.search).join(e.data.replace);
                    }
                    
                    editor.setContent(content);
                    jQuery('#title').val(title);
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