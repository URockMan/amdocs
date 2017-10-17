<div class="ad_content">
    <div class="acol_l">
        <?php $this->load->view('report/report_menu'); ?>                
    </div>
    <div class="acol_r">
        <?php if ($this->session->flashdata('error_message')) { ?>
            <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
        <?php } ?>
        <?php if ($this->session->flashdata('success_message')) { ?>
            <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
        <?php } ?>
        <?php $this->load->view('report/' . $sub_view_file); ?>
    </div>
</div>
