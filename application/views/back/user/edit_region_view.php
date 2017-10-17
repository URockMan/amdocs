<div class="row"> 
                	<div class="col-9"><h3>add region</h3></div><div class="col-3 tar"><a href="<?php echo base_url('user/region_list/'); ?>" class="btn_small">Back</a></div>
                </div>
				<?php echo form_open('user/edit_region/'.$region['id']); ?>
                	<div class="col-6">
                    	<div class="form-group">
                        	<label>name</label>
                            <input type="text" class="st_box" id="region_name" name="region_name" value="<?php echo set_value('region_name',$region['name']); ?>">
                           <?php  echo form_error('region_name', '<span class="in_danger">', '</span>'); ?>   
                    	</div>
                       	<div class="form-group  tar btn_grup">
                        	<span class="btn_small"><input type="reset" value="CLEAR"></span>
                            <span class="btn_small"><input type="submit" value="SUBMIT" name="submit"></span>
                    </div>  
                    </div>
                                       
              <?php echo form_close(); ?>
