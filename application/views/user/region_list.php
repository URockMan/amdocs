        

<div class="row"> 
               	<div class="col-9"><h3>region management</h3></div><div class="col-3 tar"><a href="<?php echo base_url("user/add_region/"); ?>" class="btn_small">Add Region</a></div>
               </div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="r_table">
  <thead>
  	<tr>
    	<th>name</th>        
        <th>action</th>
    </tr>
  </thead>	
  <tbody>
      <?php foreach($regionlist as $val) { ?>
    <tr>
      <td><?php  echo $val['name'];?></td>        
      <td class="action">
          <a href="<?php echo base_url("user/edit_region/".$val['id']); ?>" title="Edit"><i class="fa fa-pencil-square" aria-hidden="true"></i></a>
          <a href="<?php echo base_url('user/delete_region/'.$val['id']); ?>" title="Delete"><i class="fa fa-trash" aria-hidden="true"></i></a>
      </td>
    </tr>
      <?php } ?>
    
  </tbody>
</table>