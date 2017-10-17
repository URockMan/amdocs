<ul class="a_side_menu">
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'staff_list') ? "active" : ""; ?>"><a href="<?php echo base_url('user/staff_list'); ?>" class="">user management</a></li>
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'customer_list') ? "active" : ""; ?>"><a href="<?php echo base_url('user/customer_list'); ?>" class="">customer management</a></li>
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'region_list') ? "active" : ""; ?>"><a href="<?php echo base_url('user/region_list'); ?>" class="">region management</a></li>
    <li class="<?php echo isset($sub_menu_id) && ($sub_menu_id == 'market_list') ? "active" : ""; ?>"><a href="<?php echo base_url('user/market_list'); ?>" class="">market management</a></li>
</ul>