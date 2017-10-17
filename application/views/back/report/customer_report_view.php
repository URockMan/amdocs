<?php
$report_search = $this->session->userdata('customer_report_search');
?>
<h3 class="martb15"><?php echo (isset($page_heading) ? $page_heading : 'report'); ?></h3>
<?php echo form_open('report/customer_report', 'class="fform inline_alert"'); ?>
<div class="form-group">
    <div class="col-4">
        <label>customer</label>
        <select class="select_box" id="vendor_id" name="vendor_id">
            <option value="" >Select Customer</option>
            <?php
            foreach ($vendors as $temp) {
                $vendor[$temp['id']] = $temp['name'];
                echo '<option value="' . $temp['id'] . '"';
                if (isset($report_search['vendor_id']) && $report_search['vendor_id'] == $temp['id']) {
                    echo 'selected="selected"';
                }
                echo '>' . $temp['name'] . '</option>';
            }
            ?>
        </select>
        <?php echo form_error('vendor_id', '<span class="in_danger">', '</span>'); ?>

    </div>
    <div class="col-4">
        <label>from date</label>
        <input type="text" class="st_box" placeholder="mm/dd/yyyy" name="fromdate" id="fromdate" value="<?php echo $report_search['fromdate']; ?>" autocomplete="off">
        <?php echo form_error('fromdate', '<span class="in_danger">', '</span>'); ?>
    </div>
    <div class="col-4">
        <label>to date</label>
        <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="todate" name="todate" value="<?php echo $report_search['todate']; ?>" autocomplete="off">
        <?php echo form_error('todate', '<span class="in_danger">', '</span>'); ?>
    </div>
</div>
<div class="form-group">
    <div class="col-12 btn_grup tar">
        <span class="btn_small"><input type="submit" value="FILTER" name="search"></span>
        <a href="<?php echo base_url('report/customer_report/reset'); ?>" class="btn_small">CLEAR</a>
        <a href="<?php echo base_url('report/customer_report_export'); ?>" class="btn_small">EXPORT</a>
    </div>
</div>
<?php echo form_close(); ?>
<?php if (!empty($report_search['vendor_id'])) { ?>
    <?php if (!empty($reportList)) { ?>
        <h4 class="co-orange"><?php echo $vendor[$report_search['vendor_id']]; ?></h4>
        <div class="res_table martb15">
            <table class="r_table nostrip">
                <thead>
                    <tr>
                        <th>region</th>
                        <th>market</th>
                        <th>#PO's</th>
                        <th>total $</th>
                    </tr>
                </thead>
                <tbody><?php $oldregion = $total_po = $total_amount = 0; ?>
                    <?php
                    foreach ($reportList as $val) {

                        if ($oldregion != $val['region']) {
                            ?>
                            <tr>
                                <td colspan="4" class="highlight"><?php
                                    echo $val['region_name'];
                                    $oldregion = $val['region'];
                                    ?></td>
                            </tr>
                        <?php } ?>
                        <tr>
                            <td></td>
                            <td><?php echo $val['market_name']; ?></td>
                            <td><?php echo $val['total_count']; ?></td>
                            <td>$<?php echo $val['amount']; ?></td>
                        </tr>

                        <?php
                        $total_po += $val['total_count'];
                        $total_amount += $val['amount'];
                    }
                    ?>

                    <tr class="fb">
                        <td>Total</td>
                        <td></td>
                        <td><?php echo $total_po; ?></td>
                        <td>$<?php echo number_format($total_amount, 2); ?></td>
                    </tr>
                </tbody>
            </table>

        </div>
        <p>** includes both "<strong>Open</strong>" and "<strong>invoiced</strong>" status</p>
    <?php } else { ?>

        <p>no invoice found</p>

    <?php } ?>
<?php } else { ?>
    <p>select a customer first</p>
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

        function getDate(element) {
            var date;
            try {
                date = $.datepicker.parseDate(dateFormat, element.value);
            } catch (error) {
                date = null;
            }

            return date;
        }

    });
</script>
