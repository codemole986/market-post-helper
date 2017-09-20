(function(){
	
	function resizeIframe(obj) {
	    obj.style.height = obj.contentWindow.document.body.scrollHeight + 'px';
	}

	function load_intrinio_news(ticker) {

		jQuery('#intrinio_load_news').addClass('loading');

		jQuery.ajax({
            url: ajaxurl + "?action=intrinio_barchart&value=news&ticker=" + ticker,
            statusCode: {
                500: function() {
                  load_intrinio_news(ticker);
                }
            }
        }).done(function( data ) {
            if (data == '') {
                load_intrinio_news(ticker);
            } else {
                // let 's fill the box
                var content = '';
                for (var i=0; i<data.data.length; i++) {
                	var obj = data.data[i];

                	content = content + '<h2>' + obj['title'] + '</h2>';
                	if ((typeof obj['image'] == 'object') && obj['image']['url']) {
                		content = content + '<img class="alignleft" src="' + obj['image']['url'] + '" alt="" width="' + obj['image']['width'] + '" height="' + obj['image']['height'] + '" />';
                		content = content + obj['image']['caption']

                	}
                	content = content + '<hr />';
                	content = content + obj['content'];
                }

                tinyMCE.get('intrinio_show_news').setContent(content);

                jQuery('#intrinio_load_news').removeClass('loading');
            }
        });
	}

	function import_finviz_items() {
		jQuery('#intrinio_finviz_items_wrapper').addClass('loading');

		jQuery.ajax({
            url: ajaxurl + "?action=intrinio_import_finviz_items",
            statusCode: {
                500: function() {
                  import_finviz_items();
                }
            }
        }).done(function( data ) {
            if (data == '') {
                import_finviz_items();
            } else {
                // let 's fill the box
                jQuery('#intrinio_finviz_items').html('');
                for (var i=0; i<data.length; i++) {
                	var $span = jQuery('<span>');
                	$span.html(data[i][0]).addClass('finviz-item');
                	$span.appendTo(jQuery('#intrinio_finviz_items'));
                }
                jQuery('#intrinio_finviz_items_wrapper').removeClass('loading');
            }
        });
	}

	function import_quandl_items() {
		jQuery('#intrinio_quandl_items_wrapper').addClass('loading');

		jQuery.ajax({
            url: ajaxurl + "?action=intrinio_import_quandl_items&key=" + jQuery('#intrinio_quandl_apikey').val(),
            statusCode: {
                500: function() {
                  import_quandl_items();
                }
            }
        }).done(function( data ) {
            document.location.href = document.location.href;
        });
	}


	jQuery(document).ready(function(){

		if (jQuery('#intrinio_load_news_ticker').length > 0) {

			jQuery("#intrinio_load_news_ticker").autocomplete({
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
		}

		if (jQuery('#btn_import_finviz_items').length > 0) {

			jQuery("#btn_import_finviz_items").on('click', function(){
				import_finviz_items();
			});
		}

		if (jQuery('#btn_import_quandl_items').length > 0) {

			jQuery("#btn_import_quandl_items").on('click', function(e){
				e.preventDefault();
				import_quandl_items();
				return false;
			});
		}

		if (jQuery('#intrinio_load_news_button').length > 0) {
			jQuery('#intrinio_load_news_button').on('click', function(e){
				e.preventDefault();
				e.stopPropagation();

				var ticker = jQuery("#intrinio_load_news_ticker").val();
				if (ticker != '') {
					load_intrinio_news(ticker);
				}

				return false;
			});
		}

		if (jQuery('#intrinio_show_news').length > 0) {
			
		}

		if (jQuery('#intrinio_setting_tabs').length > 0) {
			jQuery('#intrinio_setting_tabs').tabs();
		}

		if (jQuery('#intrinio_title_list_row').length > 0) {
			jQuery('#post-body-content').prepend(jQuery('#intrinio_title_list_row'));
			jQuery('#intrinio_title_list').on('change', function(){
				jQuery('#title').val(jQuery(this).val()).focus();
			})
		}

		jQuery('.frm_bulk_settings').on('click', '.btn-del-bulk-group', function() {
			if (!confirm("You are about to delete the group. \nAre you sure to delete?")) return;

			var $link = jQuery(this);
			var $frm = $link.closest('.frm_bulk_settings');

			$frm.find('.bulk_action_code').val('delete');
			$frm.find('.bulk_action_id').val($link.attr('ref'));
			$frm.submit();
		});

	});

})();


function intrinio_bulk_preload_content() {
    var $form = jQuery('#form_bulk_publish');
    $form.find('#bulk_action_type').val('preload');
    $form.submit();
}

function intrinio_bulk_upload_preload_content() {
    var $form = jQuery('#form_bulk_publish');
    $form.find('#bulk_action_type').val('preload_update');
    $form.submit();
}