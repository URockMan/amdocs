<?php
$report_search = $this->session->userdata('tracker_search');
$report_status = array_flip($status);
?>

<div class="wraper">
    <div class="row">
        <div class="col-9"><h1 class="heading-1"><?php echo (isset($page_heading) ? $page_heading : 'tracker'); ?></h1></div>
        <div class="col-3 tar"><span class="fr"><a href="<?php echo base_url('tracker/trackerList'); ?>" class="btn_small">< back</a></span></div>

    </div>
    <?php if ($this->session->flashdata('error_message')) { ?>
        <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success_message')) { ?>
        <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
    <?php } ?>


    <?php echo form_open_multipart('tracker/editTracker/' . $tracker_detail['tracker_id'], 'class="form label_right inline_alert"'); ?>   
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">region</label>
                <div class="col-8">
                    <select class="select_box" id="region" name="region">                
                        <?php
                        foreach ($regions as $temp) {
                            if (isset($tracker_detail['region']) && $temp['id'] == $tracker_detail['region']) {
                                echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                            } else {
                                echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php echo form_error('region', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">market</label>
                <div class="col-8">
                    <select class="select_box" id="market" name="market">
                        <option value="">select market</option>
                        <?php
                        foreach ($markets as $temp) {
                            if (isset($tracker_detail['market']) && $temp['id'] == $tracker_detail['market']) {
                                echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                            } else {
                                echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                            }
                        }
                        ?>

                    </select>
                    <?php echo form_error('market', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">customer</label>
                <div class="col-8">
                    <select class="select_box" id="customer" name="customer">
                        <option value="">select customer</option>
                        <?php
                        foreach ($vendors as $temp) {
                            if (isset($tracker_detail['vendor']) && $temp['id'] == $tracker_detail['vendor']) {
                                echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                            } else {
                                echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                            }
                        }
                        ?>
                    </select>
                    <?php echo form_error('customer', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">PO date</label>
                <div class="col-8">                   
                    <input type="text" class="form-control st_box" id="po_date" name="po_date" placeholder="PO Date" value="<?php echo date('m/d/Y', strtotime($tracker_detail['po_date'])); ?>" >
                    <?php echo form_error('po_date', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">PO number</label>
                <div class="col-8">
                    <input type="text" class="form-control st_box" id="po_number" name="po_number" placeholder="PO Number" value="<?php echo $tracker_detail['po_number']; ?>">
                    <?php echo form_error('po_number', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">line</label>
                <div class="col-8">
                    <input id="po_line" class="form-control st_box" name="po_line" placeholder="Line" value="<?php echo $tracker_detail['line']; ?>" type="text">
                    <?php echo form_error('po_line', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">PO and line</label>
                <div class="col-8">
                    <input id="po_number_line" class="form-control st_box" name="po_number_line" placeholder="PO and Line" value="<?php echo $tracker_detail['po_line_rev']; ?>"  type="text">
                    <?php echo form_error('po_number_line', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">ID 1</label>
                <div class="col-8">
                    <input id="site_id" class="form-control st_box" name="site_id" placeholder="SITE ID" value="<?php echo $tracker_detail['site_name']; ?>"  type="text">
                    <?php echo form_error('site_id', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">description</label>
                <div class="col-8">
                    <textarea type="text" class="form-control t_area" id="description" name="description" placeholder="Description"><?php echo $tracker_detail['description']; ?></textarea>
                    <?php echo form_error('description', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">quantity</label>
                <div class="col-8">
                    <input id="qty" class="form-control st_box" name="qty" placeholder="Quantity" value="<?php echo $tracker_detail['qty']; ?>" type="text">
                    <?php echo form_error('qty', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">unit price</label>
                <div class="col-8">
                    <div class="st_box">
                        <span class="input-group-addon">$</span>
                        <input id="unit_price" class="form-control" name="unit_price" placeholder="Unit Price" value="<?php echo $tracker_detail['unit_price']; ?>" type="text">
                        <?php echo form_error('unit_price', '<span class="in_danger">', '</span>'); ?>
                    </div>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">amount</label>
                <div class="col-8">
                    <div class="st_box">
                        <span class="input-group-addon">$</span>
                        <input id="amount" class="form-control" name="amount" placeholder="Amount" value="<?php echo $tracker_detail['amount']; ?>" type="text">
                        <?php echo form_error('amount', '<span class="in_danger">', '</span>'); ?>
                    </div>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <div class="col-4">
                    <label>status</label>
                </div>
                <div class="col-8">
                    <input type="text" class="form-control st_box" id="status" name="status" placeholder="Status" value="<?php
                    if (isset($report_status[$tracker_detail['status']])) {
                        if ($report_status[$tracker_detail['status']] == 'riv') {
                            echo 'Ready to Invoice';
                        } else {
                            echo ucfirst($report_status[$tracker_detail['status']]);
                        }
                    } else {
                        echo "Open";
                    }
                    ?>" disabled="">
                </div>                      
            </div>
        </div> 
        <div class="col-6 tar">                	
            <span class="btn_small"><input type="submit" value="UPDATE" name="update"></span>
        </div>               
    </div>
</form>
</div>

<script type="text/javascript">
    $('#po_date').datepicker({
        dateFormat: 'mm/dd/yy'
    });
</script>

