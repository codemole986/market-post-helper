
					<form method="post" id="frm_bulk_settings">
						<input type="hidden" name="action_type" value="bulk">					
						<input type="hidden" name="action_code" value="new" id="bulk_action_code">
						<input type="hidden" name="action_id" value="" id="bulk_action_id">

					
					    <?php settings_fields('intrinio-shortcode-group'); ?>
					    <?php do_settings_sections('intrinio-shortcode-group'); ?>

					    <?php
							$titles = get_option('intrinio_title_list');
							$titles = explode("\n", str_replace("\r\n", "\n", $titles));

							$templates = Intrinio_Shortcode::get_templates();

							$groups = get_option('mph_bulk_groups');
							if (!$groups)	$groups = array();
						?>

					    <div class="row row-sm">
					    	<div class="col-sm-6">
					    		<h5>Create Group</h5>
					    		<hr>

					    		<?php
						    		$group_id = '';
						    		$group_label = '';
						    		$group_titles = array();
						    		$group_tpls = array();
					    		?>

					    		<?php include (dirname(__FILE__) . '/view_settings_bulk_options_form.tpl'); ?>

					    	</div>
					    	<div class="col-sm-6 mph-bulk-list-groups">
					    		<h5>Group(s)</h5>
					    		<hr>
					    		<ul class="nav nav-tabs row row-xs">
					    			<?php  $isFirst = true; foreach ($groups as $key => $grp) { ?>
					    			<li class="<?php echo $isFirst?'active':''; ?> col-xs-3"><a href="#bulk_grp_<?php echo $key; ?>" data-toggle="tab"><?php echo $grp['label']; ?></a></li>	
					    			<?php $isFirst = false; } ?>
					    		</ul>

					    		<div class="tab-content ">
					    			<?php $isFirst = true; foreach ($groups as $key => $grp) { ?>
					    			<div id="bulk_grp_<?php echo $key; ?>" class="tab-pane <?php echo $isFirst?'active':''; ?>">

						    			<?php
								    		$group_id = $key;
								    		$group_label = $grp['label'];
								    		$group_titles = $grp['titles'];
								    		$group_tpls = $grp['tpls'];
							    		?>
							    		<?php include (dirname(__FILE__) . '/view_settings_bulk_options_form.tpl'); ?>

					    			</div>
					    			<?php $isFirst = false; } ?>
					    		</div>
					    	</div>
					    	
					    </div>

					</form>