<?php
$report_search = $this->session->userdata('tracker_search');
$report_status = array_flip($status);
?>

<div class="wraper">
    <div class="row">
        <div class="col-9"><h1 class="heading-1"><?php echo (isset($page_heading) ? $page_heading : 'tracker'); ?></h1></div>
        <div class="col-3 tar"><span class="fr"><a href="<?php echo base_url('errorpo'); ?>" class="btn_small">< back</a></span></div>

    </div>

    <div class="alert" id="msg_div"></div>

    <h3 class="martb15" name="po_number"></h3>

    <?php foreach ($resultset as $key => $val) { ?>



        <div class="mydiv">
            <h3 class="martb15">tracker no:<?php echo $key = $key + 1; ?></h3>

            <?php echo form_open_multipart('javascript:void(0);', 'id="edit_errorpo'.$key.'" class="form label_right inline_alert"'); ?>   
            <input type="hidden" name="hidden_trackerid" id="hidden_trackerid" value="<?php echo $val['tracker_id']; ?>">
            <input type="hidden" name="po_number" id="po_number" value="<?php echo $val['po_number']; ?>">
            <div class="form-group">
                <div class="col-4">
                    <div class="row">
                        <label class="col-4">region</label>
                        <div class="col-8">
                            <input type="text" class="form-control st_box" value="<?php echo $val['reg_name']; ?>" readonly>
                        </div>
                    </div>    
                </div>
                <div class="col-4">
                    <div class="row">
                        <label class="col-4">market</label>
                        <div class="col-8">
                            <input type="text" class="form-control st_box" value="<?php echo $val['market_name']; ?>" readonly>
                        </div> 
                    </div>    
                </div>   

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">customer</label>
                        <div class="col-8">
                            <input type="text" class="form-control st_box" value="<?php echo $val['cus_name']; ?>" readonly>
                        </div>
                    </div>    
                </div>


            </div>

            <div class="form-group">

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">PO date</label>
                        <div class="col-8">                   
                            <input type="text" class="form-control st_box po_date" id="po_date" name="po_date" placeholder="PO Date" value="<?php echo date('m/d/Y', strtotime($val['po_date'])); ?>" >
                            <?php echo form_error('po_date', '<span class="in_danger">', '</span>'); ?>
                        </div> 
                    </div>    
                </div>    



                <div class="col-4">
                    <div class="row">
                        <label class="col-4">line</label>
                        <div class="col-8">
                            <input id="po_line" class="form-control st_box" name="po_line" placeholder="Line" value="<?php echo $val['line']; ?>" type="text">
                            <?php echo form_error('po_line', '<span class="in_danger">', '</span>'); ?>
                        </div> 
                    </div>    
                </div>  

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">revision</label>
                        <div class="col-8">
                            <input id="rev" class="form-control st_box" name="rev" placeholder="revision" value="<?php echo $val['rev']; ?>"  type="text">
                            <?php echo form_error('po_number_line', '<span class="in_danger">', '</span>'); ?>
                        </div>
                    </div>    
                </div>

            </div>

            <div class="form-group">

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">id1</label>
                        <div class="col-8">
                            <input id="site_name" class="form-control st_box" name="site_name" placeholder="id1" value="<?php echo $val['site_name']; ?>"  type="text">
                            <?php echo form_error('site_id', '<span class="in_danger">', '</span>'); ?>
                        </div> 
                    </div>    
                </div>    


                <div class="col-4">
                    <div class="row">
                        <label class="col-4">id2</label>
                        <div class="col-8">
                            <input id="supplier_no" class="form-control st_box" name="supplier_no" placeholder="id2" value="<?php echo $val['supplier_no']; ?>"  type="text" required>
                            <?php echo form_error('site_id', '<span class="in_danger">', '</span>'); ?>
                        </div> 
                    </div>    
                </div>

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">description</label>
                        <div class="col-8">
                            <textarea type="text" class="form-control t_area" id="description" name="description" placeholder="Description"><?php echo $val['description']; ?></textarea>
                            <?php echo form_error('description', '<span class="in_danger">', '</span>'); ?>
                        </div>
                    </div>    
                </div>
            </div>

            <div class="form-group">

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">quantity</label>
                        <div class="col-8">
                            <input id="qty" class="form-control st_box" name="qty" placeholder="Quantity" value="<?php echo $val['qty']; ?>" type="text">
                            <?php echo form_error('qty', '<span class="in_danger">', '</span>'); ?>
                        </div> 
                    </div>    
                </div>  

                <div class="col-4">
                    <div class="row">
                        <label class="col-4">unit price</label>
                        <div class="col-8">                    
                            <input id="unit_price" class="form-control st_box" name="unit_price" placeholder="Unit Price" value="<?php echo $val['unit_price']; ?>" type="text">
                            <?php echo form_error('unit_price', '<span class="in_danger">', '</span>'); ?>                    
                        </div>
                    </div>    
                </div>

                <div class="col-4">

                </div> 

            </div>

            <div class="form-group"> 
                <div class="col-6 tar">                	
                    <span class="btn_small">
                        <input type="button" value="UPDATE" name="update" class="upbtn">
                    </span>
                    <span class="btn_small">
                        <input type="button" value="DELETE" name="delete" class="delbtn" delId ="<?php echo $val['tracker_id']; ?>">
                    </span>

                </div>               
            </div>

            <?php echo form_close(); ?>
        </div>

    <?php } ?>

</div>

<script type="text/javascript">
    $('.po_date').datepicker({
        dateFormat: 'mm/dd/yy'
    });
</script>

<script>
    var flag = 1;
   $(document).on('click', '.upbtn', function () {        
        
            var curFrm = $(this).parents('form');
            $(curFrm).find('.in_danger').remove();
            if($.trim($(curFrm).find("#supplier_no").val()) == ""){
                $(curFrm).find("#supplier_no").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#site_name").val()) == ""){
                $(curFrm).find("#site_name").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#unit_price").val()) == ""){
                $(curFrm).find("#unit_price").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            else if(!/^(\d)*(\.\d{0,2})?$/.test($.trim($(curFrm).find("#unit_price").val()))){
                $(curFrm).find("#unit_price").after('<span class="in_danger">Please input valid data.</span>');
                flag = 0;
            }
            
            if($.trim($(curFrm).find("#qty").val()) == ""){
                $(curFrm).find("#qty").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            else if (!$.isNumeric($.trim($(curFrm).find("#qty").val()))){
                $(curFrm).find("#qty").after('<span class="in_danger">Numeric data required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#description").val()) == ""){
                $(curFrm).find("#description").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#rev").val()) == ""){
                $(curFrm).find("#rev").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#po_line").val()) == ""){
                $(curFrm).find("#po_line").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            if($.trim($(curFrm).find("#po_date").val()) == ""){
                $(curFrm).find("#po_date").after('<span class="in_danger">This field is required.</span>');
                flag = 0;
            }
            
            if(flag==1){
                if (confirm("Do you want to continue?")) {
                var ths = $(this);
                var form = $(this).parents('form:first');
                var data = form.serialize();

                $.ajax({
                    url: '<?php echo base_url(); ?>' + 'errorpo/ajax_save',
                    data: data,
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function () {
                    },
                    success: function (resp) {
                        if (resp.status == 'success') {
                            $('#msg_div').addClass('alert-success');
                            $('#msg_div').html('tracker updated successfully');
                            setTimeout(function () {
                                ths.parents('.mydiv').slideToggle('slow');
                                $('#msg_div').hide();
                            }, 3000);

                        }
                        if (resp.status == 'fail') {

                            // some action
                        }

                    }
                });
            }
            }
        
        
    });

    $(document).on('click', '.delbtn', function () {
        if (confirm("Do you want to delete?")) {
            var tracker_id = $(this).attr('delId');
            var ths = $(this);
            //alert(tracker_id);
            $.ajax({
                url: '<?php echo base_url(); ?>' + 'errorpo/ajax_delete',
                data: {'tracker_id': tracker_id},
                type: 'GET',
                dataType: 'json',
                beforeSend: function () {
                },
                success: function (resp) {
                    if (resp.status == 'success') {
                        $('#msg_div').addClass('alert-success');
                        $('#msg_div').html('tracker deleted successfully');
                        setTimeout(function () {
                            ths.parents('.mydiv').slideToggle('slow');
                            $('#msg_div').hide();
                        }, 3000);

                    }
                    if (resp.status == 'fail') {

                        // some action
                    }

                }
            });




        }
        else {
        }




    });
</script>

