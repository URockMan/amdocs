<?php
$error_search = $this->session->userdata('error_search');
$chkedTracker = $this->session->userdata('trkrChk');
$chkedTracker = explode(",",$chkedTracker);
?>
<div class="wraper">
    <h1 class="heading-1"><?php echo (isset($page_heading) ? $page_heading : 'tracker'); ?></h1>
    <?php if ($this->session->flashdata('error_message')) { ?>
        <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> <span class="a_close"></span></div>
    <?php } ?>
    <?php if ($this->session->flashdata('success_message')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> <span class="a_close"></span></div>
    <?php } ?>
    <?php echo form_open('errorpo', 'class="form label_right" id="errortrackerfilterform"'); ?>         
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">customer</label>
                <div class="col-8">
                    <select class="select_box" id="vendor" name="vendor">
                        <option value="All" <?php
                        if (isset($error_search['vendor']) && $error_search['vendor'] == 'All') {
                            echo 'selected="selected"';
                        }
                        ?>>All</option>
                                <?php
                                foreach ($vendors as $temp) {
                                    echo '<option value="' . $temp['id'] . '"';
                                    if (isset($error_search['vendor']) && $error_search['vendor'] == $temp['id']) {
                                        echo 'selected="selected"';
                                    }
                                    echo '>' . $temp['name'] . '</option>';
                                }
                                ?>
                    </select>
                    <?php echo form_error('vendor', '<span class="in_danger">', '</span>'); ?>                    
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">PO number</label>
                <div class="col-8">
                    <input type="text" class="st_box" name="ponumber" placeholder="PO Number" value="<?php echo $error_search['ponumber']; ?>">
                    <?php echo form_error('ponumber', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>
        
        <div class="col-8">
            <div class="btn_grup">
                <span class="btn_small"><input type="submit" value="FILTER" name="search" ></span>                
                <a href="<?php echo site_url('errorpo/index/reset'); ?>" class="btn_small">CLEAR</a>
                
            </div>
        </div>
        
        
    </div>
    
    <?php echo form_close(); ?>

    <div class="res_table x-scroll-tbl-outer">
        <?php echo form_open('tracker/generateInvoice', 'id="invoiceform"'); ?>
        
        <table class="r_table table-striped table-bordered tracker_list x-scroll-tbl remove-padr-15">
            <thead>
                <tr>
                    <th>po number</th>
                    <th>revision</th>
                    <th>region</th>
                    <th>market</th>
                    <th>customer</th>
                    <th>no.of tracker</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultset) {
                    $status = array_flip($status);
                    foreach ($resultset as $val) {
                        ?>
                        <tr>
                            <td><?php echo $val['po_number']; ?></td>
                            <td><?php echo $val['rev']; ?></td>
                            <td><?php echo $val['region_name']; ?></td>                    
                            <td><?php echo $val['market_name']; ?></td>
                            <td><?php echo $val['cus_name']; ?></td>
                            <td><?php echo $val['cnt']; ?></td>
                            <td class="action"><a href="<?php echo base_url('errorpo/edit_errorpo/'.urlencode($val['po_number']));?>" title="Edit">
                                    <i class="fa fa-pencil-square" aria-hidden="true"></i></a></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr><td colspan="16">No tracker available</td>
                    <?php } ?>

            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    
    <?php echo $this->pagination->create_links(); ?>

    <?php if (isset($added_invoice_ids)) { ?>
        <div class="popup_download">
            <h1 class="heading-1">invoice download</h1>
            <p><strong><?php echo count($added_invoice_ids); ?></strong> invoices created </p>
            <div class="btn_grup same-size-font tac">
                <a href="<?php echo base_url('tracker/exportGenerateInvoice') ?>" class="btn_small">Download</a>
                <span class="btn_small close_d"><input type="button" value="close window"></span>
            </div>
        </div>
        <?php
    } else {
        
    }
    ?>
</div>

<script type="text/javascript">
    $(function () {
        $('.tracker_list th div.point').click(function () {
            element = $(this);
            $('#orderfield').val(element.data('key'));
            if (element.hasClass('up_ico')) {
                $('#ordertype').val('desc');
            } else {
                $('#ordertype').val('asc');
            }
            document.forms["trackerfilterform"].submit();
        });

        var invoicedate = $("#invoicedate").datepicker({
            dateFormat: 'mm/dd/yy'
        });

        $('.open_p').click(function () {
            invoicedate.datepicker("setDate", new Date());
            $('.popup').fadeIn();
            return false;
        });
        $('.close_p').click(function () {
            $('.popup').fadeOut();
        });
        $('.close_d').click(function () {
            $('.popup_download').fadeOut();
        });
        
        $('.chkTrak').click(function(){
            //alert($(this).is(":checked"));
                $.post("<?php echo site_url('tracker/updateTrakeChk')?>",
                {
                    id: $(this).val(),
                    status: $(this).is(":checked")
                },
                function(data, status){
                    //alert("Data: " + data + "\nStatus: " + status);
                });
        
        });
    });
</script>