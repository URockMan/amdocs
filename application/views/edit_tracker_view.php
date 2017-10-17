<?php
$report_search = $this->session->userdata('tracker_search');
$report_status = array_flip($status);
?>
<!DOCTYPE HTML>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <title>Tracker List</title>
        <!-- Bootstrap styles -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">        
        <style>
            .panel-heading {
                /* background-image: -webkit-linear-gradient(top,#f5f5f5 0,#e8e8e8 100%); */
                background-image: -o-linear-gradient(top,#f5f5f5 0,#e8e8e8 100%);
                /* background-image: -webkit-gradient(linear,left top,left bottom,from(#f5f5f5),to(#e8e8e8)); */
                background-image: linear-gradient(to bottom,#f5f5f5 0,#e8e8e8 100%);
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#fff5f5f5', endColorstr='#ffe8e8e8', GradientType=0);
                background-repeat: repeat-x;
                padding: 10px 15px;
                border-bottom: 1px solid transparent;
                border-top-left-radius: 3px;
                border-top-right-radius: 3px;
                color: #333; 
                background-color: #f5f5f5; 
                border-color: #ddd; 

            }  
            .panel-heading > p{
                font-family: inherit;
                font-weight: 500;
                line-height: 1.1;
                color: inherit;
            }

            .btn{
                transition-duration: .4s;
            }
            .btn:hover {
                box-shadow: 5px 5px 7px #888888;
                transform: scale(1.08,1.08);
            }

        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container">

                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li ><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li class="active"><a href="<?php echo base_url('tracker/trackerList'); ?>">Tracker List</a></li>

                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
            <div class="page-header clear">
                <h1>Edit Tracker <a href="<?php echo base_url('tracker/trackerList'); ?>" class="btn btn-success pull-right" name="search">Back</a></h1>     
            </div>
            <?php if ($this->session->flashdata('error_message')) { ?>
                <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
            <?php } ?>
            <?php if ($this->session->flashdata('success_message')) { ?>
                <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
            <?php } ?>
            <?php echo form_open_multipart('tracker/editTracker/' . $tracker_detail['tracker_id'], 'class="form-horizontal"'); ?>   
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Region:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="region" name="region" placeholder="Region" value="<?php echo $tracker_detail['region']; ?>" disabled="">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Market:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="market" name="market" placeholder="Market" value="<?php echo $tracker_detail['market']; ?>" disabled="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Vendor:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="vendor" name="vendor" placeholder="Vendor" value="<?php echo $tracker_detail['vendor']; ?>" disabled="">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">PO Date:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="po_date" name="po_date" placeholder="PO Date" value="<?php echo $tracker_detail['po_date']; ?>" disabled="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">PO Number:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="po_number" name="po_number" placeholder="PO Number" value="<?php echo $tracker_detail['po_number']; ?>" disabled="">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Line:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="line" name="line" placeholder="Line" value="<?php echo $tracker_detail['line']; ?>" disabled="">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">PO and Line:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="po_number" name="po_number" placeholder="PO and Line" value="<?php echo $tracker_detail['po_number'] . $tracker_detail['line']; ?>" disabled=""> 
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">SITE ID:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="line" name="line" placeholder="SITE ID" value="<?php echo $tracker_detail['line']; ?>" disabled="">
                    </div>
                </div>
            </div>

            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Description:</label>
                    <div class="col-sm-9">
                        <textarea type="text" class="form-control" id="description" name="description" placeholder="Description" ><?php echo set_value('description', $tracker_detail['description']); ?></textarea>
                        <?php echo form_error('description', '<div class="alert-danger">', '</div>'); ?>
                    </div>
                </div>

                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Quantity:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="qty" name="qty" placeholder="Quantity" value="<?php echo $tracker_detail['qty']; ?>">
                        <?php echo form_error('qty', '<div class="alert-danger">', '</div>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Unit Price:</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control" id="unit_price" name="unit_price" placeholder="Unit Price" value="<?php echo set_value('unit_price', $tracker_detail['unit_price']); ?>">
                        </div>
                        <?php echo form_error('unit_price', '<div class="alert-danger">', '</div>'); ?>
                    </div>
                </div>

                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">Amount:</label>
                    <div class="col-sm-9">
                        <div class="input-group">
                            <span class="input-group-addon">$</span>
                            <input type="text" class="form-control" id="amount" name="amount" placeholder="Amount" value="<?php echo set_value('amount', $tracker_detail['amount']); ?>">
                        </div>
                        <?php echo form_error('amount', '<div class="alert-danger">', '</div>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="status" class="col-sm-3 control-label">Status:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="status" name="status" placeholder="Status" value="<?php
                        if (isset($report_status[$tracker_detail['status']])) {
                            if ($report_status[$tracker_detail['status']] == 'riv') {
                                echo 'Ready for Invoice';
                            } else {
                                echo ucfirst($report_status[$tracker_detail['status']]);
                            }
                        } else {
                            echo "Open";
                        }
                        ?>" disabled="">

                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <div class="col-sm-9 col-sm-offset-3">
                        <button type="submit" class="btn btn-success" value="update" name="update">Update</button>
                    </div>
                </div>
            </div>

        </form>

    </div>

</body>
</html>