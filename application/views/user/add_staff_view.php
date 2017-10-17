
                <div class="row"> 
                	<div class="col-9"><h3>add user</h3></div><div class="col-3 tar"><a href="<?php  echo base_url("user/staff_list/"); ?>" class="btn_small">Back</a></div>
                </div>
			<?php echo form_open('user/add_staff'); ?>
                    <div class="form-group">
                        <div class="col-4">
                            <label>name</label>
                            <input type="text" class="st_box" id="user_name" name="user_name" value="<?php echo set_value('user_name'); ?>">
                             <?php  echo form_error('user_name', '<span class="in_danger">', '</span>'); ?>
                    
                        </div>
                        <div class="col-4">
                            <label>e-mail</label>
                            <input type="email" class="st_box" id="user_email"  name="user_email" value="<?php echo set_value('user_email'); ?>">
                           <?php  echo form_error('user_email', '<span class="in_danger">', '</span>'); ?>                             
                        </div>
                        <div class="col-4">
                            <label>password</label>
                            <input type="password" class="st_box" id="user_password" name="user_password" value="<?php echo set_value('user_password'); ?>">
                            <?php  echo form_error('user_password', '<span class="in_danger">', '</span>'); ?>                              
                        </div>                                                
                    </div>
                    <div class="form-group">
                        <div class="col-12 btn_grup tar">
                        	<span class="btn_small"><input type="reset" value="CLEAR"></span>
                            <span class="btn_small"><input type="submit" value="SUBMIT" name="submit"></span>
                        </div>
                    </div>                    
                 <?php echo form_close(); ?>


           
