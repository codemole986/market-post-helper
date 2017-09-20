

					    		<form method="post" class="frm_bulk_settings">
								<input type="hidden" name="action_type" value="bulk">					
								<input type="hidden" name="action_code" value="save" class="bulk_action_code">
								<input type="hidden" name="action_id" value="<?php echo $group_id; ?>" class="bulk_action_id">

					    		<div class="row row-sm">
					    			<div class="col-sm-8">
					    				<div class="input-group">
					    					<label class="input-group-addon">Label</label>
					    					<input type="text" class="form-control" name="bulk_group_label" value="<?php echo $group_label; ?>">
					    				</div>
					    			</div>
					    			<div class="col-sm-4">
					    				<input type="submit" value="<?php echo $group_id?"Save":"Create";?>" class="btn btn-primary"> 
					    				<?php if ($group_id) { ?>
						    			<a href="#" title="Delete" class="btn btn-danger btn-del-bulk-group" ref="<?php echo $key; ?>"><i class="glyphicon glyphicon-trash"></i></a>
							    		<?php } ?>
					    			</div>
					    		</div>

					    		<div class="row row-sm">
					    			<div class="col-sm-6">
					    				<label>Titles</label><hr style="margin: 0 0 10px 0;">
					    				<div>
						    				<?php foreach ($titles as $tlt) {
						    					if ($tlt == '') continue; 
						    					$isChecked = false;
						    					if (in_array($tlt, $group_titles))	$isChecked = true;
						    				?>
						    				<label class="block-bulk-title display-block"><input type="checkbox" name="bulk_titles[]" value="<?php echo $tlt; ?>" <?php echo $isChecked?"checked":"";?>> <?php echo $tlt; ?></label>
						    				<?php } ?>
					    				</div>
					    			</div>
					    			<div class="col-sm-6">
					    				<label>Templates</label><hr style="margin: 0 0 10px 0;">
					    				<div>
						    				<?php 
											foreach ($templates as $value) {
												$tlt = basename($value[1]); 
												$isChecked = false;
												if (in_array($tlt, $group_tpls))	$isChecked = true; 
												?>
												<label class="block-bulk-tpl display-block"><input type="checkbox" name="bulk_tpls[]" value="<?php echo $tlt; ?>" <?php echo $isChecked?"checked":"";?>> <?php echo $tlt; ?></label>	
											<?php } ?>
					    				</div>
					    			</div>
					    		</div>
					    		
					    		</form>
