
<script>
var ajax_url = "<?php echo $ajax_url; ?>";
var ajax_pass_url = "<?php echo $ajax_pass_url; ?>";
</script>

               <div class="row"> 
               	<div class="col-9"><h3>user management</h3></div><div class="col-3 tar"><a href="<?php  echo base_url("user/add_staff/"); ?>" class="btn_small">Add User</a></div>
               </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="r_table">
  <thead>
  	<tr>
    	<th>name</th>
        <th>e-mail</th>
        <th>password</th>
        <th>status</th>
        <th>action</th>
    </tr>
  </thead>	
  <tbody>
      <?php foreach ($stafflist as $val){ ?>
    <tr>
      <td><?php  echo $val['user_name']; ?></td>
      <td><?php  echo $val['email']; ?></td>
      <td><?php  echo $val['password']; ?></td>
      <td><a href="#" onclick="changeStaffStatus(<?php echo $val['user_id']; ?>)" title="<?php echo ($val['user_status']==$user_status['active'])?"Active":"Inactive"; ?>"><i class="fa <?php echo ($val['user_status']==$user_status['active'])?"fa-check-circle":"fa-times-circle"; ?>" aria-hidden="true"></i></a></td>
      <td class="action">
          <a href="<?php  echo base_url("user/edit_staff/".$val['user_id']); ?>" title="Edit"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
          <a href="#" onclick="resetStaffPass(<?php echo $val['user_id']; ?>)" title="Reset Password"><i class="fa fa-key" aria-hidden="true"></i></a>
      </td>
    </tr>
      <?php } ?>
    
  </tbody>
</table>


           