<div class="overlay"></div>
<div class="row" style="margin-top: 10px; margin-bottom: 10px;">
	<div class="col-xs-8">
		<input type="text" class="form-control" id="intrinio_load_news_ticker" placeholder="Type the tikcer">
	</div>
	<div class="col-xs-4">
		<button class="button" id="intrinio_load_news_button">Load News</button>
	</div>
</div>
<div class="row">
	<div class="col-xs-12">
		<?php 
		$settings = array( 
			'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), // note that spaces in this list seem to cause an issue
			'intrinio_shortcode' => false,
			'intrinio_template' => false,
			'intrinio_replacecode' => false,
		);

		wp_editor( '', 'intrinio_show_news', $settings ); 
		?>
		<!-- <textarea class="form-control" id="intrinio_show_news"></textarea> -->
	</div>
</div>

<div class="row" id="intrinio_title_list_row">
	<div class="col-sm-12">
		<select id="intrinio_title_list" class="form-control">
			<option>Select the Title</option>
			<?php
			$titles = get_option('intrinio_title_list');
			$titles = explode("\n", str_replace("\r\n", "\n", $titles));

			foreach ($titles as $t) {
				?>
				<option value="<?php echo htmlspecialchars($t); ?>"><?php echo htmlspecialchars($t); ?></option>
				<?php
			}
			?>
		</select>
	</div>
</div>