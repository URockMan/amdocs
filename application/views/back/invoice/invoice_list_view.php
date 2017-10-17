<?php
$report_search = $this->session->userdata('invoice_search');
?>
<div class="wraper">
    <h1 class="heading-1">invoices</h1>
    <?php if ($this->session->flashdata('error_message')) { ?>
        <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
    <?php } ?>
    <?php if ($this->session->flashdata('success_message')) { ?>
        <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
    <?php } ?>
    <?php echo form_open_multipart('invoice/inportFile/', 'class="form label_right inline_alert"'); ?>  
    <div class="row">
        <div class="col-4">
            <div class="upload-box tac"><span class="uf_name">Drag and Drop files here</span> <input class="input-file" type="file" id="invoice_attachment" name="invoice_attachment" multiple> </div>
        </div>
        <div class="col-8 scol_form mar0">
            <div class="form-group">             
                <span class="btn_small"><input type="submit" value="UPLOAD" name="upload"></span>
            </div>
        </div> 
    </div> 
</form> 

<?php echo form_open_multipart('invoice/invoiceList/', 'class="form label_right inline_alert" id="invoicefilterform"'); ?>  
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label class="col-4">customer</label>
            <div class="col-8">
                <select class="select_box" id="vendorid" name="vendorid">
                    <option value="All" <?php
                    if (isset($report_search['vendorid']) && $report_search['vendorid'] == 'All') {
                        echo 'selected="selected"';
                    }
                    ?>>All</option>
                            <?php
                            foreach ($vendors as $temp) {
                                echo '<option value="' . $temp['id'] . '"';
                                if (isset($report_search['vendorid']) && $report_search['vendorid'] == $temp['id']) {
                                    echo 'selected="selected"';
                                }
                                echo '>' . $temp['name'] . '</option>';
                            }
                            ?>
                </select>
                <?php echo form_error('vendorid', '<span class="in_danger">', '</span>'); ?>        
            </div>                    
        </div>
        <div class="form-group">
            <label class="col-4">region</label>
            <div class="col-8">
                <select class="select_box" id="regionid" name="regionid">
                    <option value="All" <?php
                    if (isset($report_search['regionid']) && $report_search['regionid'] == 'All') {
                        echo 'selected="selected"';
                    }
                    ?>>All</option>
                            <?php
                            foreach ($regions as $temp) {
                                echo '<option value="' . $temp['id'] . '"';
                                if (isset($report_search['regionid']) && $report_search['regionid'] == $temp['id']) {
                                    echo 'selected="selected"';
                                }
                                echo '>' . $temp['name'] . '</option>';
                            }
                            ?>
                </select>
                <?php echo form_error('regionid', '<span class="in_danger">', '</span>'); ?>
            </div>                   
        </div>
        <div class="form-group">
            <label class="col-4">market</label>
            <div class="col-8">
                <select class="select_box" id="marketid" name="marketid">
                    <option value="All" <?php
                    if (isset($report_search['marketid']) && $report_search['marketid'] == 'All') {
                        echo 'selected="selected"';
                    }
                    ?>>All</option>
                            <?php
                            foreach ($markets as $temp) {
                                echo '<option value="' . $temp['id'] . '"';
                                if (isset($report_search['marketid']) && $report_search['marketid'] == $temp['id']) {
                                    echo 'selected="selected"';
                                }
                                echo '>' . $temp['name'] . '</option>';
                            }
                            ?>
                </select>
                <?php echo form_error('marketid', '<span class="in_danger">', '</span>'); ?>
            </div>                  
        </div>                    
        <div class="form-group">
            <label class="col-4">invoice from date</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="invoicefromdate" name="invoicefromdate" value="<?php echo $report_search['invoicefromdate']; ?>">
                <?php echo form_error('invoicefromdate', '<span class="in_danger">', '</span>'); ?>

            </div>                   
        </div>
        <div class="form-group">
            <label class="col-4">invoice to date</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="invoicetodate" name="invoicetodate" value="<?php echo $report_search['invoicetodate']; ?>">
                <?php echo form_error('invoicetodate', '<span class="in_danger">', '</span>'); ?>

            </div>                   
        </div>                                                             
    </div>
    <div class="col-6">
        <div class="form-group">
            <label class="col-4">PO number</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="PO Number" id="ponumber" name="ponumber" value="<?php echo $report_search['ponumber']; ?>">
                <?php echo form_error('ponumber', '<span class="in_danger">', '</span>'); ?>
            </div>                    
        </div>
        <div class="form-group">
            <label class="col-4">invoice number</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="invoice Number" id="invoicenumber" name="invoicenumber" value="<?php echo $report_search['invoicenumber']; ?>">
                <?php echo form_error('invoicenumber', '<span class="in_danger">', '</span>'); ?>
            </div>                    
        </div> 
        <div class="form-group">
            <label class="col-4">date created from</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" name="fromdate" id="fromdate" value="<?php echo $report_search['fromdate']; ?>">
                <?php echo form_error('fromdate', '<span class="in_danger">', '</span>'); ?>  
            </div>                   
        </div>                     
        <div class="form-group">
            <label class="col-4">date created to</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="todate" name="todate" value="<?php echo $report_search['todate']; ?>">
                <?php echo form_error('todate', '<span class="in_danger">', '</span>'); ?>
            </div>                   
        </div> 
        <div class="form-group">
            <div class="btn_grup tar">
                <span class="btn_small"><input type="submit" value="FILTER" name="search"></span>
                <a href="<?php echo site_url('invoice/invoiceList/reset'); ?>" class="btn_small">CLEAR</a>
                <a href="<?php echo site_url('invoice/exportExcelInvoice'); ?>" class="btn_small">EXPORT</a>
            </div>
        </div>                                      
    </div>
</div>
<input type="hidden" name="orderfield" id="orderfield" value="<?php echo $report_search['orderfield']; ?>">
<input type="hidden" name="ordertype" id="ordertype" value="<?php echo $report_search['ordertype']; ?>">
</form>

<table class="r_table table-striped table-bordered invoice_list">
    <thead>
        <tr>   
            <th width="8%"><div class="point <?php
                if ($report_search['orderfield'] == 'vendor_id') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="vendor_id">customer</div></th>

            <th><div class="point <?php
                if ($report_search['orderfield'] == 'region_id') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="region_id">region</div></th>

            <th width="8%"><div class="point <?php
                if ($report_search['orderfield'] == 'market_id') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="market_id">market</div></th> 

            <th><div class="point <?php
                if ($report_search['orderfield'] == 'invoice_number') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="invoice_number"><span>invoice</span>number</div></th>  

            <th><div class="point <?php
                if ($report_search['orderfield'] == 'po_number') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="po_number"><span>PO</span>number</div></th>

            <th width="8%"><div class="point <?php
                if ($report_search['orderfield'] == 'invoice_date') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="invoice_date"><span>invoice</span>date</div></th>

            <th width="8%"><div class="point <?php
                if ($report_search['orderfield'] == 'created_time') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="created_time"><span>date</span>created</div></th>

            <th><div class="point <?php
                if ($report_search['orderfield'] == 'amount') {
                    if ($report_search['ordertype'] == 'asc') {
                        echo 'up_ico';
                    } else {
                        echo 'down_ico';
                    }
                }
                ?>" data-key="amount"><span>invoice</span>amount</div></th>    

            <th width="5%">excel</th>
            <th width="5%">PDF</th>
            <th width="5%">cancel</th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($invoiceList) {

            foreach ($invoiceList as $val) {
                ?>
                <tr>
                    <td><?php echo $val['vendor_name']; ?></td>
                    <td><?php echo $val['region_name']; ?></td>
                    <td><?php echo $val['market_name']; ?></td>                      
                    <td><?php echo $val['invoice_number']; ?></td>
                    <td><?php echo $val['po_number']; ?></td>
                    <td><?php echo date('m/d/Y', strtotime($val['invoice_date'])); ?></td>
                    <td><?php echo date('m/d/Y', strtotime($val['created_time'])); ?></td>
                    <td><?php echo "$" . number_format($val['amount'], 2); ?></td>
                    <td> <a href="<?php echo base_url('invoice/genarateEcel/' . $val['invoice_id']); ?>"  class="btn_ico excel" title="Download Excel"></a></td>
                    <td><a href="<?php echo base_url('invoice/genaratePdf/' . $val['invoice_id']); ?>" class="btn_ico pdf" title="PDF"></a></td>
                    <td><a href="<?php echo base_url('invoice/cancelinvoice/' . $val['invoice_id']); ?>" class="btn_ico cancel" title="cancel invoice"></a></td>
                </tr>
                <?php
            }
        } else {
            ?>
            <tr><td colspan="10">No invoice available</td>
            <?php } ?>                                     
    </tbody>
</table>
<?php echo $this->pagination->create_links(); ?>    
</div>

<script type="text/javascript">
    $(function () {
        var dateFormat = "mm/dd/yy";
        from = $("#fromdate")
                .datepicker({
                    dateFormat: 'mm/dd/yy'
                })
                .on("change", function () {
                    to.datepicker("option", "minDate", getDate(this));
                });
        to = $("#todate").datepicker({
            dateFormat: 'mm/dd/yy'
        })
                .on("change", function () {
                    from.datepicker("option", "maxDate", getDate(this));
                });

        invoicefromdate = $("#invoicefromdate")
                .datepicker({
                    dateFormat: 'mm/dd/yy'
                })
                .on("change", function () {
                    invoicetodate.datepicker("option", "minDate", getDate(this));
                });
        invoicetodate = $("#invoicetodate").datepicker({
            dateFormat: 'mm/dd/yy'
        })
                .on("change", function () {
                    invoicefromdate.datepicker("option", "maxDate", getDate(this));
                });

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }
        $('.input-file').change(function () {
            var names = [];
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push('<span>' + $(this).get(0).files[i].name + '</span>');
            }

            $('.uf_name').html(names);
        });

        $('.invoice_list th div.point').click(function () {
            element = $(this);
            $('#orderfield').val(element.data('key'));
            if (element.hasClass('up_ico')) {
                $('#ordertype').val('desc');
            } else {
                $('#ordertype').val('asc');
            }
            document.forms["invoicefilterform"].submit();
        });

    });
</script>