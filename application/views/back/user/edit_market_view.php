
<div class="row"> 
    <div class="col-9"><h3>add market</h3></div><div class="col-3 tar"><a href="<?php echo base_url('user/market_list/'); ?>" class="btn_small">Back</a></div>
</div>
<?php echo form_open('user/edit_market/'.$market['id']); ?>
<div class="form-group">
    <div class="col-6">
        <label>region name</label>
        <select class="select_box" name="region_id">
            <option value="">Select Region</option>
            <?php foreach ($region as $val) { ?>
            
                <option value="<?php echo $val['id']; ?>" <?php echo ($market['region_id']==$val['id'])?"Selected":"";?>><?php echo $val['name']; ?></option>
            <?php } ?>
        </select>
        <?php echo form_error('region_id', '<span class="in_danger">', '</span>'); ?>   

    </div>
    <div class="col-6">
        <label>market name</label>
        <input type="text" class="st_box" id="market_name" name="market_name" value="<?php echo set_value('market_name',$market['name']); ?>" >
        <?php echo form_error('market_name', '<span class="in_danger">', '</span>'); ?>                              
    </div>

</div>
<div class="form-group">
    <div class="col-12 btn_grup tar">
        <span class="btn_small"><input type="reset" value="CLEAR"></span>
        <span class="btn_small"><input type="submit" value="SUBMIT" name="submit"></span>
    </div>
</div>                    
<?php echo form_close(); ?>