var intrinio_last_ticker = '';
(function() {

    function init_intrinio_shortcode_view (v) {
        var view_control = v;
        if (v.control) view_control = v.control;

        if (view_control.settings.value == '0') {
            jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).show();
        } else {
            jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).hide();
        }

        if (view_control.settings.value == '4') {
            var topv = jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).css('top');
            jQuery('#intrinio_shortcode_input_finviz_item').parents('.mce-container').eq(0).css('top', topv).show();
        } else {
            jQuery('#intrinio_shortcode_input_finviz_item').parents('.mce-container').eq(0).hide();
        }

        if (view_control.settings.value == '5') {
            var topv = jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).css('top');
            jQuery('#intrinio_shortcode_input_quandl_item').parents('.mce-container').eq(0).css('top', topv).show();
        } else {
            jQuery('#intrinio_shortcode_input_quandl_item').parents('.mce-container').eq(0).hide();
        }

        if (view_control.settings.value == '8') {
            var topv = jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).css('top');
            jQuery('#intrinio_shortcode_input_marketwatch_item').parents('.mce-container').eq(0).css('top', topv).show();
        } else {
            jQuery('#intrinio_shortcode_input_marketwatch_item').parents('.mce-container').eq(0).hide();
        }

        if (view_control.settings.value == '9') {
            var topv = jQuery('#intrinio_shortcode_input_item').parents('.mce-container').eq(0).css('top');
            jQuery('#intrinio_shortcode_input_barchart_item').parents('.mce-container').eq(0).css('top', topv).show();
        } else {
            jQuery('#intrinio_shortcode_input_barchart_item').parents('.mce-container').eq(0).hide();
        }
       
    }

    function init_intrinio_shortcode_window(view) {
        jQuery("#intrinio_shortcode_input_ticker").autocomplete({
            source: ajaxurl + "?action=intrinio_load_comapines",
            minLength: 2,
            select: function(event, ui) {},
            open: function(event, ui) {
                jQuery(".ui-autocomplete").css("z-index", 100000000);
            }
        }).on('keydown', function(e){
            if ((jQuery(".ui-autocomplete").length > 0) && (e.keyCode == 13)) {
                e.stopPropagation();
                return false;    
            }
        });

        jQuery('#intrinio_shortcode_input_finviz_item').parents('.mce-container').eq(0).hide();
        jQuery('#intrinio_shortcode_input_quandl_item').parents('.mce-container').eq(0).hide();
        jQuery('#intrinio_shortcode_input_marketwatch_item').parents('.mce-container').eq(0).hide();
        jQuery('#intrinio_shortcode_input_barchart_item').parents('.mce-container').eq(0).hide();
    }

    function replace_barchart(type, ticker, editor, source) {
        jQuery.ajax({
            url: ajaxurl + "?action=intrinio_barchart&value=" + type + "&ticker=" + ticker,
            statusCode: {
                500: function() {
                  replace_barchart(type, ticker, editor, source);
                }
            }
        }).done(function( data ) {
            if (data == '') {
                replace_barchart(type, ticker, editor, source);
            } else {

                /*
                editor.execCommand('mceReplaceContent', source, data);
                return;
                */

                var content = editor.getContent();
                content = content.split(source).join(data);
                editor.setContent(content);
            }
        });
    }

    tinymce.PluginManager.add( 'intrinio_shortcode', function( editor, url ) {
        // Add Button to Visual Editor Toolbar
        editor.addButton('intrinio_shortcode', {
            title: 'Insert Intrinio Shortcode',
            cmd: 'intrinio_shortcode',
            image: url + '/../img/icon-shortcode.png?v=0.1',
        });

        // Add Command when Button Clicked
        editor.addCommand('intrinio_shortcode', function(ui, v) {

            var view = editor.windowManager.open( {
                title: 'Insert a Shortcode',
                height : 150,
                width: 620,
                body: [{
                    type: 'listbox',
                    name: 'type',
                    label: 'Type',
                    value: '0',
                    values: [
                        {'text': 'Intrino Value', 'value': '0'},
                        {'text': 'Finviz Value', 'value': '4'},                        
                        {'text': 'Quandl Value', 'value': '5'}, 
                        {'text': 'Chart', 'value': '1'},
                        {'text': 'Business Summary', 'value': '2'},
                        {'text': 'Barchart Technical Opinion', 'value': '3'},
                        {'text': 'RSI Value', 'value': '6'}, 
                        {'text': 'EMA Value', 'value': '7'},
                        {'text': 'Market Watch', 'value': '8'},
                        {'text': 'Barchart', 'value': '9'}
                    ],
                    id : 'intrinio_shortcode_input_type',
                    minWidth: 500,
                    onselect: init_intrinio_shortcode_view
                },
                {
                    type: 'textbox',
                    name: 'ticker',
                    label : 'Ticker',
                    id : 'intrinio_shortcode_input_ticker',
                    placeholder: 'Type the Ticker here.',
                    value: intrinio_last_ticker,
                    minWidth: 500
                },
                {
                    type: 'listbox',
                    name: 'item',
                    label: 'Item',
                    values: intrinio_item_list,
                    id : 'intrinio_shortcode_input_item',
                    tooltip: 'Select the type of panel you want.',
                    minWidth: 500
                },
                {
                    type: 'listbox',
                    name: 'finviz_item',
                    label: 'Item',
                    values: intrinio_finviz_item_list,
                    id : 'intrinio_shortcode_input_finviz_item',
                    minWidth: 500
                },
                {
                    type: 'listbox',
                    name: 'quandl_item',
                    label: 'Item',
                    values: intrinio_quandl_item_list,
                    id : 'intrinio_shortcode_input_quandl_item',
                    minWidth: 500
                },
                {
                    type: 'listbox',
                    name: 'marketwatch_item',
                    label: 'Item',
                    values: intrinio_marketwatch_items,
                    id : 'intrinio_shortcode_input_marketwatch_item',
                    minWidth: 500
                },
                {
                    type: 'listbox',
                    name: 'barchart_item',
                    label: 'Item',
                    values: intrinio_barchart_items,
                    id : 'intrinio_shortcode_input_barchart_item',
                    minWidth: 500
                }
                ],
                onsubmit: function( e ) {
                    if (((e.data.type == '0') && ((e.data.ticker == '') || (e.data.item == ''))) || ((e.data.type == '1') && (e.data.ticker == ''))) return false;
                    intrinio_last_ticker = e.data.ticker;
                    if (e.data.type == '0') {
                        if ((e.data.ticker == '') || (e.data.item == '')) return false;
                        editor.insertContent( '[intr_code ticker=' + e.data.ticker + ' item=' + e.data.item + ']');    
                    } else if (e.data.type == '1') {
                        if (e.data.ticker == '') return false;
                        editor.insertContent( '[intr_chart ticker=' + e.data.ticker + ']');
                    } else if (e.data.type == '2') {
                        if (e.data.ticker == '') return false;
                        var shortcode = '[intr_summary ticker=' + e.data.ticker + ']';
                        editor.insertContent( shortcode );
                        replace_barchart('summary', e.data.ticker, editor, shortcode);
                    } else if (e.data.type == '3') {
                        if (e.data.ticker == '') return false;
                        var shortcode = '[intr_bto ticker=' + e.data.ticker + ']';
                        editor.insertContent( shortcode );
                        replace_barchart('bto', e.data.ticker, editor, shortcode);
                    } else if (e.data.type == '4') {
                        if ((e.data.ticker == '') || (e.data.finviz_item == '')) return false;
                        var shortcode = '[finviz_code ticker=' + e.data.ticker + ' item=' + e.data.finviz_item + ']';
                        editor.insertContent( shortcode );
                    } else if (e.data.type == '5') {
                        if ((e.data.ticker == '') || (e.data.quandl_item == '')) return false;
                        var shortcode = '[quandl_code ticker=' + e.data.ticker + ' item=' + e.data.quandl_item + ']';
                        editor.insertContent( shortcode );
                    } else if (e.data.type == '6') {
                        if (e.data.ticker == '') return false;
                        editor.insertContent( '[rsi_code ticker=' + e.data.ticker + ']');
                    } else if (e.data.type == '7') {
                        if (e.data.ticker == '') return false;
                        editor.insertContent( '[ema_code ticker=' + e.data.ticker + ']');
                    } else if (e.data.type == '8') {
                        if ((e.data.ticker == '') || (e.data.marketwatch_item == '')) return false;
                        editor.insertContent( '[market_watch ticker=' + e.data.ticker + ' item=' + e.data.marketwatch_item  + ']');
                    } else if (e.data.type == '9') {
                        if ((e.data.ticker == '') || (e.data.barchart_item == '')) return false;
                        editor.insertContent( '[barchart ticker=' + e.data.ticker + ' item=' + e.data.barchart_item  + ']');
                    }
                }
            });
            init_intrinio_shortcode_window(view);
        });
    });

})();