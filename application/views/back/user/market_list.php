 
<div class="row"> 
               	<div class="col-9"><h3>market management</h3></div><div class="col-3 tar"><a href="<?php echo base_url("user/add_market"); ?>" class="btn_small">Add Market</a></div>
               </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="r_table">
  <thead>
  	<tr>
    	<th>region name</th>
        <th>market name</th>        
        <th>action</th>
    </tr>
  </thead>	
  <tbody>
       <?php foreach($marketlist as $val) { ?>
    <tr>
      <td><?php  echo $val['region_name'];?></td>
      <td><?php  echo $val['name'];?></td>     
      <td class="action">
          <a href="<?php echo base_url("user/edit_market/".$val['id']); ?>" title="Edit"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
      </td>
    </tr>
     <?php } ?>
    
  </tbody>
</table>