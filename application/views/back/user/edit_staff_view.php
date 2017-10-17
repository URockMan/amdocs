
    <div class="row"> 
        <div class="col-9"><h3>edit staff</h3></div><div class="col-3 tar"><a href="<?php echo base_url("user/staff_list/"); ?>" class="btn_small">Back</a></div>
    </div>
    <?php echo form_open('user/edit_staff/'.$user_details['user_id']); ?>
    <div class="form-group">
        <div class="col-4">
            <label>name</label>
            <input type="text" class="st_box" id="user_name" name="user_name" value="<?php echo set_value('user_name',$user_details['user_name']); ?>">
            <?php echo form_error('user_name', '<span class="in_danger">', '</span>'); ?>

        </div>
        <div class="col-4">
            <label>e-mail</label>
            <input type="email" class="st_box" id="user_email"  name="user_email" value="<?php echo set_value('user_email',$user_details['email']); ?>">
            <?php echo form_error('user_email', '<span class="in_danger">', '</span>'); ?>                             
        </div>
        <div class="col-4">
            <label>password</label>
            <input type="text" class="st_box" id="user_password" name="user_password" value="<?php echo set_value('user_password',$user_details['password']); ?>">
            <?php echo form_error('user_password', '<span class="in_danger">', '</span>'); ?>                              
        </div>                                                
    </div>
    <div class="form-group">
        <div class="col-12 btn_grup tar">
            
            <span class="btn_small"><input type="submit" value="SUBMIT" name="submit"></span>
        </div>
    </div>                    
    <?php echo form_close(); ?>

