			<div class="ntrinio-shortcode-setting-page issp">
				<h3>Market Post Helper Plugin Settings</h3>
				<div class="wrap">
					<div id="intrinio_setting_tabs">

					  	<ul class="nav nav-tabs" style=" margin-bottom: 20px; ">
					  		<li class="active"><a href="#tabs-base" data-toggle="tab">Basic Settings</a></li>
						    <li><a href="#tabs-post-helper" data-toggle="tab">Post Helper</a></li>
						    <li><a href="#tabs-tpls" data-toggle="tab">Templages</a></li>
						    <li><a href="#tabs-bulk" data-toggle="tab">Bulk Publish Options</a></li>
						</ul>


			  			<div class="tab-content ">
					<form method="post">
					  <input type="hidden" name="action_type" value="update">
					  <div id="tabs-base" class="tab-pane active">
					    <?php include (dirname(__FILE__) . '/view_settings_base.tpl'); ?>
					  </div>
					  <div id="tabs-post-helper" class="tab-pane">
					    <?php include (dirname(__FILE__) . '/view_settings_post_helper.tpl'); ?>
					  </div>
					</form>
					  
					  <div id="tabs-bulk" class="tab-pane">
					    <?php include (dirname(__FILE__) . '/view_settings_bulk_options.tpl'); ?>
					  </div>

					  <div id="tabs-tpls" class="tab-pane">
					    <?php include (dirname(__FILE__) . '/view_settings_tpls.tpl'); ?>
					  </div>
					  <!--
					  <div id="tabs-finviz">
					    <?php include (dirname(__FILE__) . '/view_settings_stockta.tpl'); ?>
					  </div>
					  -->
					</div>
				</div>
			</div>