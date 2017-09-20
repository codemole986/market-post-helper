
					
					

					    <div class="row">
					    	<div class="col-sm-5">
					    		<!-- Quandl Settings -->
					    		<button class="btn btn-primary pull-right" id="btn_import_quandl_items">Import Items from Quandl</button>
					    		<h4>Quandl API Info</h4>
								<hr>
								<div class="row row-sm">
							    	<div class="col-sm-4"><label>API Key</label></div>
							    	<div class="col-sm-8">
							    		<input type="text" class="form-control" id="intrinio_quandl_apikey" name="intrinio_quandl_apikey" value="<?php echo esc_attr( get_option('intrinio_quandl_apikey') ); ?>" />
							    	</div>
							    </div>

							    <div class="row">
									<div class="col-sm-12" id="intrinio_quandl_items_wrapper">
										<div class="overlay"></div>
										<div id="intrinio_quandl_items">
											<?php 
												// settings_fields('intrinio-shortcode-group'); 
										    	// do_settings_sections('intrinio-shortcode-group'); 
												$intrinio_quandl_items = get_option('intrinio_quandl_items');
										    	if ($intrinio_quandl_items) {
										    		foreach ($intrinio_quandl_items as $key => $items) {
											    	?>
											    		<div class="quandl-section"><?php echo Intrinio_Shortcode::$quandl_zacks[$key][0]; ?></div>
											    	<?php
											    		foreach ($items as $item) {
										    			?>
												    		<span class="quandl-item"><?php echo $item; ?></span>
												    	<?php
											    		}
											    	}	
										    	}
										    ?>

										</div>
									</div>
								</div>

					    	</div>
					    	<div class="col-sm-7">
					    		<!-- Intrino Settings -->
					    		<input type="submit" value="Submit" class="btn btn-primary pull-right"> 
					    		<h4>Intrinio API Info</h4>
								<hr>
							    <div class="row row-sm">
							    	<div class="col-sm-4"><label>User Name</label></div>
							    	<div class="col-sm-8">
							    		<input type="text" class="form-control" name="intrinio_api_username" value="<?php echo esc_attr( get_option('intrinio_api_username') ); ?>" />
							    	</div>
							    </div>

							    <div class="row row-sm">
							    	<div class="col-sm-4"><label>Password</label></div>
							    	<div class="col-sm-8">
							    		<input type="text" class="form-control" name="intrinio_api_password" value="<?php echo esc_attr( get_option('intrinio_api_password') ); ?>" />
							    	</div>
							    </div>

							    <div class="row row-sm">
							    	<div class="col-sm-12">
							    		<label>Item List</label>
							    		<textarea class="form-control" name="intrinio_item_list" rows="20"><?php echo esc_attr( get_option('intrinio_item_list') ); ?></textarea>
							    	</div>
							    </div>


							    <button class="btn btn-primary pull-right" id="btn_import_finviz_items">Import Items from Finviz</button>
								<h4>Finviz Items</h4>
								<hr>
								<div class="row">
									<div class="col-sm-12" id="intrinio_finviz_items_wrapper">
										<div class="overlay"></div>
										<div id="intrinio_finviz_items">
											<?php 
												// settings_fields('intrinio-shortcode-group'); 
										    	// do_settings_sections('intrinio-shortcode-group'); 

										    	$items = get_option('intrinio_finviz_items');

										    	foreach ($items as $item) {
										    	?>
										    		<span class="finviz-item"><?php echo $item[0]; ?></span>
										    	<?php
										    	}
										    ?>

										</div>
									</div>
								</div>

					    	</div>
					    </div>
						

						

					