<?php
$report_search = $this->session->userdata('invoice_report_search');
?>

<h3 class="martb15"><?php echo (isset($page_heading) ? $page_heading : 'report'); ?></h3>
<?php echo form_open('report/invoice_report', 'class="form label_right inline_alert" id="invoicefilterform"'); ?>
<div class="row">
    <div class="col-6">
        <div class="form-group">
            <label class="col-4">customer</label>
            <div class="col-8">
                <select class="select_box" id="vendorid" name="vendorid">
                    <option value="">Select Customer</option>
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
                    <option value="All" >All</option>
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
    </div>
    <div class="col-6">
        <div class="form-group">
            <label class="col-4"> from date</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="invoicefromdate" name="invoicefromdate" value="<?php echo $report_search['invoicefromdate']; ?>">
                <?php echo form_error('invoicefromdate', '<span class="in_danger">', '</span>'); ?>

            </div>                   
        </div>
        <div class="form-group">
            <label class="col-4"> to date</label>
            <div class="col-8">
                <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="invoicetodate" name="invoicetodate" value="<?php echo $report_search['invoicetodate']; ?>">
                <?php echo form_error('invoicetodate', '<span class="in_danger">', '</span>'); ?>

            </div>                   
        </div> 
        <div class="form-group">
            <div class="btn_grup tar">
                <span class="btn_small"><input type="submit" value="FILTER" name="search"></span>
                <a href="<?php echo site_url('report/invoice_report/reset'); ?>" class="btn_small">CLEAR</a>
                <a href="<?php echo site_url('report/invoice_report_export'); ?>" class="btn_small">EXPORT</a>
            </div>
        </div>
    </div>
</div>
<input type="hidden" name="orderfield" id="orderfield" value="<?php echo $report_search['orderfield']; ?>">
<input type="hidden" name="ordertype" id="ordertype" value="<?php echo $report_search['ordertype']; ?>">
</form>
<?php if (isset($reportList)) { ?>
    <table class="r_table table-striped table-bordered invoice_list">
        <thead>
            <tr>
                <th width="10%"><div class="point <?php
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

                <th width="10%"><div class="point <?php
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
                <th width="10%"><div class="point <?php
                    if ($report_search['orderfield'] == 'invoice_date') {
                        if ($report_search['ordertype'] == 'asc') {
                            echo 'up_ico';
                        } else {
                            echo 'down_ico';
                        }
                    }
                    ?>" data-key="invoice_date"><span>invoice</span>date</div></th>
                <th><div class="point <?php
                    if ($report_search['orderfield'] == 'po_number') {
                        if ($report_search['ordertype'] == 'asc') {
                            echo 'up_ico';
                        } else {
                            echo 'down_ico';
                        }
                    }
                    ?>" data-key="po_number"><span>PO</span>number</div></th>



                <th><div class="point <?php
                         if ($report_search['orderfield'] == 'amount') {
                             if ($report_search['ordertype'] == 'asc') {
                                 echo 'up_ico';
                             } else {
                                 echo 'down_ico';
                             }
                         }
                         ?>" data-key="amount"><span>invoice</span>amount</div></th>


            </tr>
        </thead>
        <tbody>
            <?php
            if (!empty($reportList)) {

                foreach ($reportList as $val) {
                    ?>
                    <tr>
                        <td><?php echo $val['vendor_name']; ?></td>
                        <td><?php echo $val['region_name']; ?></td>
                        <td><?php echo $val['market_name']; ?></td>
                        <td><?php echo $val['invoice_number']; ?></td>
                        <td><?php echo date('m/d/Y', strtotime($val['invoice_date'])); ?></td>
                        <td><?php echo $val['po_number']; ?></td>
                        <td><?php echo "$" . number_format($val['amount'], 2); ?></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr><td colspan="7">No invoice available</td>
                <?php } ?>
        </tbody>
    </table>
    <?php echo $this->pagination->create_links(); ?>
<?php } else { ?>
    <p>Select customer first</p>
<?php } ?>


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
