			<div class="ntrinio-shortcode-setting-page issp">
				<h3>Market Post Helper Bulk Publish</h3>
				<div class="wrap">
					<form method="post" id="form_bulk_publish">
					  <input type="hidden" name="action_type" value="update" id="bulk_action_type">

					  <div class="row row-sm">

					  	<?php if ($msg_bulk_option != '') { ?>
						<div class="alert alert-success" role="alert"><?php echo nl2br($msg_bulk_option); ?></div>
						<?php } ?>

					  	<?php $isRemotePublishEnabled = has_action('mph_remote_publish_options'); ?>
				    	<div class="<?php echo $isRemotePublishEnabled?'col-sm-8':'col-sm-12'; ?>">
				    		<div class="row row-sm"><div class="col-sm-12">
					    		<h4>Ticker List</h4>
					    		<textarea class="form-control" name="ticker_list" rows="12"><?php if(isset($data) && isset($data['ticker_list'])) echo $data['ticker_list']; ?></textarea>
				    		</div></div>

				    		<div class="row row-sm">
				    			<div class="col-sm-6">
				    				<label>Bulk Publish Group</label>
					    			<?php
						    			$groups = get_option('mph_bulk_groups');
										if (!$groups)	$groups = array();
									?>
									<select name="bulk-publish-group">
										<option value="">All Titles and Templates</option>
										<?php foreach ($groups as $key => $grp) { ?>
										<option value="<?php echo $key; ?>"><?php echo $grp['label']; ?></option>
										<?php } ?>
									</select>

				    			</div>

				    			<div class="col-sm-6 text-center">
				    				<?php if ( isset($preloadResult) && (count($preloadResult) > 0) ) { ?>
					    			<button type="button" class="btn btn-success btn-preload" onclick="intrinio_bulk_upload_preload_content()">Publish Preloaded</button>
					    			<?php } ?>
					    			<button type="button" class="btn btn-primary btn-preload" onclick="intrinio_bulk_preload_content()">Bulk Preload</button>
					    			&nbsp;&nbsp;&nbsp;
					    			<button type="submit" class="btn btn-primary">Bulk Publish</button>
					    		</div>

				    		</div>

				    		<?php if ( isset($preloadResult) && (count($preloadResult) > 0) ) { ?>
				    		<?php foreach($preloadResult AS $ind => $pr) { ?>
				    		<input type="text" name="preloadTitle[<?php echo $ind; ?>]" value="<?php echo $pr['t']; ?>">
				    		<?php echo wp_editor($pr['c'], 'preloadCont' . $ind); ?>
				    		<?php } ?>
				    		<?php } ?>

				    		<!--
				    		<div class="row row-sm"><div class="col-sm-12">
				    			<h4>Post will be generated randomly with following titles and templates.</h4>
				    		</div></div>

				    		<div class="row row-sm">
					    		<div class="col-sm-6">
					    			<label>Titles</label>
					    			<pre><?php foreach($titles AS $ttl) { 
					    				echo $ttl . "\r\n";
					    			} ?></pre>
					    		</div>
					    		<div class="col-sm-6">
					    			<label>Templates</label>
					    			<pre><?php foreach($tpls AS $tpl) { 
					    				echo $tpl[0] . "\r\n";
					    			} ?></pre>
					    		</div>
					    	</div>
					    	-->

				    	</div>

				    	<?php if ( $isRemotePublishEnabled ) { ?>
				    	<div class="col-sm-4">
				    		<h4>Remote Publish Options</h4>
				    		<div id="mph_center_publish_option">
				    			<?php do_action('mph_remote_publish_options'); ?>
				    		</div>
				    	</div>
				    	<?php } ?>

					   </div>
					  
					</form>
					  
				</div>
			</div>