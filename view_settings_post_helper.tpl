
					
					
						<input type="submit" value="Submit" class="btn btn-primary pull-right"> 
						<h4>Post Helper Settings</h4>
						<hr>
					
					    <?php settings_fields('intrinio-shortcode-group'); ?>
					    <?php do_settings_sections('intrinio-shortcode-group'); ?>
					    <div class="row row-sm">
					    	<div class="col-sm-4"><label>Featured Photo Path</label></div>
					    	<div class="col-sm-8">
					    		<input type="text" class="form-control" name="intrinio_featured_path" value="<?php echo esc_attr( get_option('intrinio_featured_path') ); ?>" />
					    	</div>
					    </div>

					    <div class="row row-sm">
					    	<div class="col-sm-6">
					    		<label>Title List</label>
					    		<textarea class="form-control" name="intrinio_title_list" rows="12"><?php echo esc_attr( get_option('intrinio_title_list') ); ?></textarea>
					    	</div>
					    	<div class="col-sm-6">
					    		<label>Disclaimer</label>
					    		<?php 
					    			$settings = array( 
										'quicktags' => array( 'buttons' => 'strong,em,del,ul,ol,li,close' ), // note that spaces in this list seem to cause an issue
									);
					    			wp_editor(get_option('intrinio_disclaimer') , 'intrinio_disclaimer', $settings ); 
					    		?>
					    	</div>
					    </div>

					    <h4>Stock TA Scrapper</h4>
					    <hr>

					    <label>MYOS Relative Strength Index (RSI) Analysis</label>
					    <div class="row">
					    	<div class="col-sm-2"><label>Blue</label></div>
					    	<div class="col-sm-4">
					    		<input type="text" class="form-control" name="intrinio_stockta_rsi_blue_col" value="<?php echo esc_attr( get_option('intrinio_stockta_rsi_blue_col') ); ?>" />
					    		<textarea class="form-control" name="intrinio_stockta_rsi_blue_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_rsi_blue_text') ); ?></textarea> 
					    	</div>
					    
					    	<div class="col-sm-2"><label>Red</label></div>
					    	<div class="col-sm-4">
					    		<input type="text" class="form-control" name="intrinio_stockta_rsi_red_col" value="<?php echo esc_attr( get_option('intrinio_stockta_rsi_red_col') ); ?>" />
					    		<textarea class="form-control" name="intrinio_stockta_rsi_red_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_rsi_red_text') ); ?></textarea> 
					    	</div>
					    </div>

					    <label>ESES Expotential Moving Average (EMA) Analysis</label>
					    <div class="row">
					    	<div class="col-sm-3"><label>Blue</label></div>
					    	<div class="col-sm-3"><input type="text" class="form-control" name="intrinio_stockta_ema_blue_col" value="<?php echo esc_attr( get_option('intrinio_stockta_ema_blue_col') ); ?>" /></div>
					    	<div class="col-sm-3"><label>Red</label></div>
					    	<div class="col-sm-3"><input type="text" class="form-control" name="intrinio_stockta_ema_red_col" value="<?php echo esc_attr( get_option('intrinio_stockta_ema_red_col') ); ?>" /></div>
					    </div>
					    <div class="row">
					    	<div class="col-sm-6">
					    		<label>Blue-Blue</label>
					    		<textarea class="form-control" name="intrinio_stockta_ema_blue_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_ema_blue_text') ); ?></textarea>
					    	</div>
					    	<div class="col-sm-6">
					    		<label>Red-Red</label>
					    		<textarea class="form-control" name="intrinio_stockta_ema_red_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_ema_red_text') ); ?></textarea>
					    	</div>
					    	<div class="col-sm-6">
					    		<label>Blue-Red</label>
					    		<textarea class="form-control" name="intrinio_stockta_ema_blue_red_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_ema_blue_red_text') ); ?></textarea>
					    	</div>
					    	<div class="col-sm-6">
					    		<label>Red-Blue</label>
					    		<textarea class="form-control" name="intrinio_stockta_ema_red_blue_text" rows="3"><?php echo esc_attr( get_option('intrinio_stockta_ema_red_blue_text') ); ?></textarea>
					    	</div>
					    </div>