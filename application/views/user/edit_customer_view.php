

<div class="row"> 
    <div class="col-9"><h3>edit customer</h3></div><div class="col-3 tar"><a href="<?php echo base_url('user/customer_list/'); ?>" class="btn_small">Back</a></div>
</div>
<?php echo form_open('user/edit_customer/' . $vendor['id']); ?>
<div class="form-group">
    <div class="col-4">
        <label>name</label>
        <input type="text" class="st_box" id="customer_name" name="customer_name" value="<?php echo set_value('customer_name', $vendor['name']); ?>">
        <?php echo form_error('customer_name', '<span class="in_danger">', '</span>'); ?>     

    </div>
    <div class="col-4">
        <label>credit days</label>
        <input type="text" class="st_box" id="credit_day" name="credit_day" value="<?php echo set_value('credit_day', $vendor['credit_day']); ?>">
        <?php echo form_error('credit_day', '<span class="in_danger">', '</span>'); ?>                                 
    </div>
    <div class="col-4">
        <label>terms</label>                            
        <input type="text" class="st_box" id="terms" name="terms" value="<?php echo set_value('terms', $vendor['terms']); ?>">
        <?php echo form_error('terms', '<span class="in_danger">', '</span>'); ?>                                 
    </div>                                                
</div>
<div class="form-group">
    <div class="col-12">
        <label>address</label>                            
        <textarea class="t_area" name="address"><?php echo set_value('address', $vendor['address']); ?></textarea>
        <?php echo form_error('address', '<span class="in_danger">', '</span>'); ?>                                   
    </div> 
</div> 
<div class="form-group">
    <div class="col-12 btn_grup tar">    
        <span class="btn_small"><input type="reset" value="CLEAR"></span>                            
        <span class="btn_small"><input type="submit" value="SUBMIT" name="submit"></span>
    </div>
</div>                    
<?php echo form_close(); ?>