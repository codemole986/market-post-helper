<div class="row">
	<div class="col-xs-12">
		<?php 
		$settings = array( 
			'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), // note that spaces in this list seem to cause an issue
		);

		$disclaimer = get_post_meta( get_the_ID(), 'intrinio_post_disclaimer', true);
		if (!$disclaimer) {
			$disclaimer = get_option('intrinio_disclaimer');
		}

		wp_editor($disclaimer , 'intrinio_post_disclaimer', $settings ); 
		?>
		<!-- <textarea class="form-control" id="intrinio_show_news"></textarea> -->
	</div>
</div>

<div id="intrinio_full_loading"></div>