<ul class="a_side_menu">
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'customer_report') ? "active" : ""; ?>"><a href="<?php echo base_url('report/customer_report'); ?>" class="">order summary report</a></li>
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'invoice_report') ? "active" : ""; ?>"><a href="<?php echo base_url('report/invoice_report'); ?>" class="">invoice summary report</a></li>
</ul>