
					<form method="post" enctype="multipart/form-data">

						<input type="submit" value="Submit" class="btn btn-primary pull-right"> 
						<h4>Templates</h4>

						<hr>
					
					    <input type="hidden" name="action_type" value="template">
					    <div class="row row-sm">
						    <div class="col-sm-4 update-template">
						    	<?php 
						    	if (isset($errors) && count($errors) > 0) {
									echo implode('<br/>', $errors);
								}
								?>
						    	<input type="file" name="template_file">
						    </div>

						    <div class="col-sm-8 template-list">
						    	<?php if (count($files) > 0) { ?>
						    	<p>Please check the temaplets you want to delete and then click the submit button.</p>
							    <table class="form-table" style="width: 100%;">

							        <tr valign="top">
							        	<th scope="row" style="width: 50px;"></th>
								        <th scope="row">File</th>
								        <th scope="row">File</th>
							        </tr>

							        <?php foreach ($files as $f) { ?>
							        <tr valign="top">
							        	<td scope="row"><input type="checkbox" name="delete_files[]" value="<?php echo basename($f[1]) ?>"></td>
								        <td scope="row"><?php echo $f[0]; ?></td>
								        <td scope="row"><pre class="preview"><?php echo nl2br(Intrinio_Helper::make_string_safe(file_get_contents($f[1]))); ?></pre></td>
							        </tr>
							        <?php } ?>
							    </table>
							    <?php } else { ?>
							    <p>There is no template to uploaded so far. </p>
							    <?php } ?>
						    </div>
						 </div>

					</form>