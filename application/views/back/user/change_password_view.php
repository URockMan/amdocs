<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="wraper">
    <?php echo form_open('user/change_password', 'id="demo_form" class="form no_mar"'); ?>    
    <div class="form-box">
        <h1 class="heading-1"><?php echo ($page_heading) ? $page_heading : '' ?></h1>
        <?php if ($this->session->flashdata('error_message')) { ?>
            <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
        <?php } ?>
        <?php if ($this->session->flashdata('success_message')) { ?>
            <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
        <?php } ?>
        <div class="form-box-block">
            <label>old password</label>
            <input class="st_box" placeholder="" name="old_pass" type="password">
            <?php echo form_error('old_pass', '<span class="in_danger">', '</span>'); ?>
        </div>
        <div class="form-box-block">
            <label>new password</label>
            <input class="st_box" placeholder="" name="new_pass" type="password">
            <?php echo form_error('new_pass', '<span class="in_danger">', '</span>'); ?>
        </div>                        
        <div class="form-box-block">
            <label>repeat password</label>
            <input class="st_box" placeholder="" name="repeat_pass" type="password">
            <?php echo form_error('repeat_pass', '<span class="in_danger">', '</span>'); ?>                
        </div>                         

        <div class="form-box-block form-btn-block row">
            <div class="col-12 tar">
                <span class="btn_small"><input value="SUBMIT" type="submit" name="change"></span>
            </div>
        </div>
    </div>
    <?php echo form_close(); ?>


</div>