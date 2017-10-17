<?php 
//print_r($customerlist);
?>
<div class="row"> 
               	<div class="col-9"><h3>customer management</h3></div><div class="col-3 tar"><a href="<?php echo base_url('user/add_customer/'); ?>" class="btn_small">Add Customer</a></div>
               </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="r_table">
  <thead>
  	<tr>
    	<th>name</th>
        <th>terms</th>
        <th>credit days</th>
        <th>address</th>
        <th>action</th>
    </tr>
  </thead>	
  <tbody>
      <?php foreach ($customerlist as $val){ ?>
    <tr>
      <td><?php  echo $val['name'];?></td>
      <td><?php  echo $val['terms'];?></td>
      <td><?php  echo $val['credit_day'];?></td>
      <td><?php  echo $val['address'];?></td>      
      <td class="action">
          <a href="<?php echo base_url('user/edit_customer/'.$val['id']); ?>" title="Edit"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
      </td>
    </tr>
    
    <?php } ?>
      
  </tbody>
</table>
