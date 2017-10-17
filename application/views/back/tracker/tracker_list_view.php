<?php
$report_search = $this->session->userdata('tracker_search');
?>
<div class="wraper">
    <h1 class="heading-1"><?php echo (isset($page_heading) ? $page_heading : 'tracker'); ?></h1>
    <?php if ($this->session->flashdata('error_message')) { ?>
        <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> <span class="a_close"></span></div>
    <?php } ?>
    <?php if ($this->session->flashdata('success_message')) { ?>
        <div class="alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> <span class="a_close"></span></div>
    <?php } ?>
    <?php echo form_open('tracker/trackerList', 'class="form label_right" id="trackerfilterform"'); ?>         
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">customer</label>
                <div class="col-8">
                    <select class="select_box" id="vendor" name="vendor">
                        <option value="All" <?php
                        if (isset($report_search['vendor']) && $report_search['vendor'] == 'All') {
                            echo 'selected="selected"';
                        }
                        ?>>All</option>
                                <?php
                                foreach ($vendors as $temp) {
                                    echo '<option value="' . $temp['id'] . '"';
                                    if (isset($report_search['vendor']) && $report_search['vendor'] == $temp['id']) {
                                        echo 'selected="selected"';
                                    }
                                    echo '>' . $temp['name'] . '</option>';
                                }
                                ?>
                    </select>
                    <?php echo form_error('vendor', '<span class="in_danger">', '</span>'); ?>                    
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">PO number</label>
                <div class="col-8">
                    <input type="text" class="st_box" name="ponumber" placeholder="PO Number" value="<?php echo $report_search['ponumber']; ?>">
                    <?php echo form_error('ponumber', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">region</label>
                <div class="col-8">                       
                    <select class="select_box" id="region" name="region">
                        <option value="All" <?php
                        if (isset($report_search['region']) && $report_search['region'] == 'All') {
                            echo 'selected="selected"';
                        }
                        ?>>All</option>
                                <?php
                                foreach ($regions as $temp) {
                                    echo '<option value="' . $temp['id'] . '"';
                                    if (isset($report_search['region']) && $report_search['region'] == $temp['id']) {
                                        echo 'selected="selected"';
                                    }
                                    echo '>' . $temp['name'] . '</option>';
                                }
                                ?>
                    </select>
                    <?php echo form_error('region', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">from date</label>
                <div class="col-8">
                    <input type="text" class="st_box" placeholder="mm/dd/yyyy" name="fromdate" id="fromdate" value="<?php echo $report_search['fromdate']; ?>" autocomplete="off">
                    <?php echo form_error('fromdate', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-6">
            <div class="row">
                <label class="col-4">market</label>
                <div class="col-8">

                    <select class="select_box" id="market" name="market">
                        <option value="All" <?php
                        if (isset($report_search['market']) && $report_search['market'] == 'All') {
                            echo 'selected="selected"';
                        }
                        ?>>All</option>
                                <?php
                                foreach ($markets as $temp) {
                                    echo '<option value="' . $temp['id'] . '"';
                                    if (isset($report_search['market']) && $report_search['market'] == $temp['id']) {
                                        echo 'selected="selected"';
                                    }
                                    echo '>' . $temp['name'] . '</option>';
                                }
                                ?>
                    </select>
                    <?php echo form_error('market', '<span class="in_danger">', '</span>'); ?>
                </div>
            </div>    
        </div>
        <div class="col-6">
            <div class="row">
                <label class="col-4">to date</label>
                <div class="col-8">
                    <input type="text" class="st_box" placeholder="mm/dd/yyyy" id="todate" name="todate" value="<?php echo $report_search['todate']; ?>" autocomplete="off">
                    <?php echo form_error('todate', '<span class="in_danger">', '</span>'); ?>
                </div> 
            </div>    
        </div>    
    </div>
    <div class="form-group">
        <div class="col-4">
            <div class="row">
                <div class="col-6">
                    <label>status</label>
                </div>
                <div class="col-6 ck_box_pan">


                    <?php foreach ($status as $key => $temp) { ?>

                        <label><input  multiple value="<?php echo $temp; ?> " type="checkbox"  class="ck_box" id="status" name="status[]"<?php echo set_checkbox('status[]', $temp, in_array($temp, $report_search['status']) ? TRUE : FALSE); ?> <?php
                            if (in_array($temp, $report_search['status'])) {
                                echo 'checked="checked"';
                            }
                            ?> ><span></span>
                                       <?php
                                       if ($key == 'riv') {
                                           echo 'ready to invoice';
                                       } else {
                                           echo $key;
                                       }
                                       ?>
                        </label>


                    <?php }
                    ?>
                </div>
                <?php echo form_error('status[]', '<div class="col-12 tar"><span class="in_danger">', '</span></div>'); ?>
            </div>
        </div>
        <div class="col-8">
            <div class="btn_grup">
                <span class="btn_small"><input type="submit" value="FILTER" name="search" ></span>                
                <a href="<?php echo site_url('tracker/trackerList/reset'); ?>" class="btn_small">CLEAR</a>
                <a href="<?php echo site_url('tracker/exportTrackerList'); ?>" class="btn_small">EXPORT</a>
                <a href="#" class="btn_small open_p">INVOICE</a>
            </div>
        </div>
    </div>
    <input type="hidden" name="orderfield" id="orderfield" value="<?php echo $report_search['orderfield']; ?>">
    <input type="hidden" name="ordertype" id="ordertype" value="<?php echo $report_search['ordertype']; ?>">
    <?php echo form_close(); ?>

    <div class="res_table">
        <?php echo form_open('tracker/generateInvoice', 'id="invoiceform"'); ?>
        <div class="popup">
            <h1 class="heading-1">add invoice date</h1>
            <p><input type="text" class="st_box" placeholder="mm/dd/yyyy" value="" readonly="" autocomplete="false" id="invoicedate" name="invoicedate"></p>
            <div class="btn_grup tac">
                <span class="btn_small"><input type="submit" value="UPLOAD"></span>
                <span class="btn_small close_p"><input type="button" value="CANCEL"></span>
            </div>
        </div>
        <table class="r_table table-striped table-bordered tracker_list">
            <thead>
                <tr>
                    <th width="8%"><div class="point <?php
                        if ($report_search['orderfield'] == 'vendor') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="vendor">customer</div></th>
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'region') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="region">region</div></th>
                    <th width="8%"><div class="point <?php
                        if ($report_search['orderfield'] == 'market') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="market">market</div></th>               
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'po_number') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="po_number"><span>PO</span>number</div></th>
                    <th width="4%"><div class="point <?php
                        if ($report_search['orderfield'] == 'line') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="line">line</div></th>
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'po_line_rev') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="po_line_rev"><span>PO and</span> Line</div></th>
                    <th width="8%"><div class="point <?php
                        if ($report_search['orderfield'] == 'po_date') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="po_date"><span>PO</span>date</div></th>
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'site_name') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="site_name"><span>site</span> ID</div></th>                                   
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'supplier_no') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="supplier_no">supplier item</div></th>
                    <th width="15%">description</th>
                    <th><div class="point <?php
                        if ($report_search['orderfield'] == 'amount') {
                            if ($report_search['ordertype'] == 'asc') {
                                echo 'up_ico';
                            } else {
                                echo 'down_ico';
                            }
                        }
                        ?>" data-key="amount">amount</div></th>
                    <th>status</th>
                    <th width="5%">edit</th>
                    <th width="5%">invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($trackerList) {
                    $status = array_flip($status);
                    foreach ($trackerList as $val) {
                        ?>
                        <tr>
                            <td><?php echo $val['vendor_name']; ?></td>
                            <td><?php echo $val['region_name']; ?></td>
                            <td><?php echo $val['market_name']; ?></td>                    
                            <td><?php echo $val['po_number']; ?></td>
                            <td><?php echo $val['line']; ?></td>
                            <td><?php echo $val['po_line_rev']; ?></td>
                            <td><?php echo date('m/d/Y',  strtotime($val['po_date'])); ?></td>
                            <td><?php echo $val['site_name']; ?></td>
                            <td><?php echo $val['supplier_no']; ?></td>
                            <td title="<?php echo htmlspecialchars($val['description']); ?>"><?php
                                echo substr($val['description'], 0, 38);
                                if (strlen($val['description']) > 38) {
                                    echo '...';
                                }
                                ?></td>   
                            <td><?php echo "$" . number_format($val['amount'], 2); ?></td>
                            <td><?php
                                if (isset($status[$val['status']])) {
                                    if ($status[$val['status']] == 'riv') {
                                        echo 'ready to invoice';
                                    } else {
                                        echo ($status[$val['status']]);
                                    }
                                } else {
                                    echo "open";
                                }
                                ?></td>

                            <td> <a href="<?php echo base_url('tracker/editTracker/' . $val['tracker_id']); ?>" class="btn_ico edit" title="Edit"></a>
                            </td>
                            <td>   
                                <?php if ($status[$val['status']] == 'riv') { ?>
                                    <label class="btn_ico invoice"><input type="checkbox" name="tracker[]" value="<?php echo $val['tracker_id']; ?>" ></label> 
                                    <?php } ?>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr><td colspan="14">No tracker available</td>
                    <?php } ?>

            </tbody>
        </table>
        <?php echo form_close(); ?>
    </div>
    <?php echo $this->pagination->create_links(); ?>    
</div>

<script type="text/javascript">
    $(function () {
        $('.tracker_list th div.point').click(function () {
            element = $(this);
            $('#orderfield').val(element.data('key'));
            if (element.hasClass('up_ico')) {
                $('#ordertype').val('desc');
            } else {
                $('#ordertype').val('asc');
            }
            document.forms["trackerfilterform"].submit();
        });

        var invoicedate = $("#invoicedate").datepicker({
            dateFormat: 'mm/dd/yy'
        });

        $('.open_p').click(function () {
            invoicedate.datepicker("setDate", new Date());
            $('.popup').fadeIn();
            return false;
        });
        $('.close_p').click(function () {
            $('.popup').fadeOut();
        });
    });
</script>