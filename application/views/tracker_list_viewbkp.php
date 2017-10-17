<?php
$report_search = $this->session->userdata('tracker_search');
echo "<pre>";
print_r('Ssantosh');
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

            .pagination > li > a{
                transition-duration: .3s;
            }
            .pagination > li > a:hover{
                box-shadow: 5px 5px 7px #888888;
                transform: scale(1.0,1.08);
                z-index: 5;
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
                <h1>Tracker List</h1>               
            </div>
            <?php if ($this->session->flashdata('error_message')) { ?>
                <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> </div>
            <?php } ?>
            <?php if ($this->session->flashdata('success_message')) { ?>
                <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?></div>
            <?php } ?>
            <?php echo form_open_multipart('tracker/trackerList', 'class="form-horizontal"'); ?>        
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="vendor" class="col-sm-3 control-label">Vendor:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="vendor" name="vendor">
                            <option value="All" <?php
                            if (isset($report_search['vendor']) && $report_search['vendor'] == 'All') {
                                echo 'selected="selected"';
                            }
                            ?>>All</option>
                                    <?php
                                    foreach ($vendors as $temp) {
                                        echo '<option value="' . $temp['name'] . '"';
                                        if (isset($report_search['vendor']) && $report_search['vendor'] == $temp['name']) {
                                            echo 'selected="selected"';
                                        }
                                        echo '>' . $temp['name'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="region" class="col-sm-3 control-label">Region:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="region" name="region">
                            <option value="All" <?php
                            if (isset($report_search['region']) && $report_search['region'] == 'All') {
                                echo 'selected="selected"';
                            }
                            ?>>All</option>
                                    <?php
                                    foreach ($regions as $temp) {
                                        echo '<option value="' . $temp['name'] . '"';
                                        if (isset($report_search['region']) && $report_search['region'] == $temp['name']) {
                                            echo 'selected="selected"';
                                        }
                                        echo '>' . $temp['name'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="ponumber"  class="col-sm-3 control-label">PO Number:</label>
                    <div class="col-sm-9">
                        <input type="text" class="form-control" id="ponumber" name="ponumber" placeholder="PO Number" value="<?php echo $report_search['ponumber']; ?>">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="market" class="col-sm-3 control-label">Market:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="market" name="market">
                            <option value="All" <?php
                            if (isset($report_search['market']) && $report_search['market'] == 'All') {
                                echo 'selected="selected"';
                            }
                            ?>>All</option>
                                    <?php
                                    foreach ($markets as $temp) {
                                        echo '<option value="' . $temp['name'] . '"';
                                        if (isset($report_search['market']) && $report_search['market'] == $temp['name']) {
                                            echo 'selected="selected"';
                                        }
                                        echo '>' . $temp['name'] . '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="status" class="col-sm-3 control-label">Status:</label>
                    <div class="col-sm-9">
                        <select class="form-control" id="status" name="status">
                            <option value="All" <?php
                            if (isset($report_search['status']) && $report_search['status'] == 'All') {
                                echo 'selected="selected"';
                            }
                            ?>>All</option>
                                    <?php
                                    foreach ($status as $key => $temp) {
                                        echo '<option value="' . $temp . '"';
                                        if (isset($report_search['status']) && $report_search['status'] == $temp) {
                                            echo 'selected="selected"';
                                        }
                                        echo '>';
                                        if ($key == 'riv') {
                                            echo 'Ready for Invoice';
                                        } else {
                                            echo ucfirst($key);
                                        }
                                        echo '</option>';
                                    }
                                    ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-6 col-md-6">
                    <label for="fromdate"  class="col-sm-3 control-label">From Date:</label>
                    <div class="col-sm-9">
                        <input type="date" value="<?php echo $report_search['fromdate']; ?>" class="form-control" name="fromdate" id="fromdate" placeholder="From Date">
                    </div>
                </div>
                <div class="col-6 col-md-6">
                    <label for="todate"  class="col-sm-3 control-label">To Date:</label>
                    <div class="col-sm-9">
                        <input autocomplete="false" type="date" value="<?php echo $report_search['todate']; ?>" class="form-control" id="todate" name="todate" placeholder="To Date">
                    </div>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-2 col-sm-offset-10">
                    <div class="col-sm-6">
                        <?php if (isset($report_search['reset'])) { ?>
                            <a href="<?php echo site_url('tracker/trackerList/reset'); ?>" class="btn btn-info pull-right">Reset</a>
                        <?php } ?>
                    </div> 
                    <div class="col-sm-6">
                        <button type="submit" class="btn btn-success pull-right" name="search">Search</button>
                    </div>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-md-12">
                <?php if ($trackerList) { ?>
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Region</th>
                                <th>Market</th>
                                <th>Vendor</th>
                                <th>PO Date</th>
                                <th>PO Number</th>
                                <th>Line</th>
                                <th>PO and Line</th>
                                <th>SITE ID</th>                                   
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Amount</th>
                                <th>Status</th>               
                                <th colspan="2">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $status = array_flip($status);
                            foreach ($trackerList as $val) {
                                ?>
                                <tr>
                                    <td><?php echo $val['tracker_id']; ?></td>
                                    <td><?php echo $val['region']; ?></td>
                                    <td><?php echo $val['market']; ?></td>
                                    <td><?php echo $val['vendor']; ?></td>
                                    <td><?php echo $val['po_date']; ?></td>
                                    <td><?php echo $val['po_number']; ?></td>
                                    <td><?php echo $val['line']; ?></td>
                                    <td><?php echo $val['po_line_rev']; ?></td>
                                    <td><?php echo $val['site_name']; ?></td>
                                    <td><?php echo $val['description']; ?></td>
                                    <td><?php echo $val['qty']; ?></td>
                                    <td><?php echo "$" . number_format($val['unit_price'], 2); ?></td>
                                    <td><?php echo "$" . number_format($val['amount'], 2); ?></td>
                                    <td><?php
                                        if (isset($status[$val['status']])) {
                                            if ($status[$val['status']] == 'riv') {
                                                echo 'Ready for Invoice';
                                            } else {
                                                echo ucfirst($status[$val['status']]);
                                            }
                                        } else {
                                            echo "Open";
                                        }
                                        ?></td>
                                    <td><a href="<?php echo base_url('tracker/editTracker/' . $val['tracker_id']) ?>" class="btn btn-sm btn-primary glyphicon glyphicon-pencil" title="Edit"></a>                                        
                                    </td>
                                    <td>                                        
                                        <button onclick="toggleStatus(<?php echo $val['tracker_id'] ?>);" class="btn btn-sm <?php
                                        if ($status[$val['status']] != 'riv') {
                                            echo 'btn-success';
                                        } else {
                                            echo 'btn-danger';
                                        }
                                        ?> glyphicon glyphicon-list-alt" title="<?php
                                                if ($status[$val['status']] != 'riv') {
                                                    echo 'Set Ready for Invoice';
                                                } else {
                                                    echo 'Unset Ready for Invoice';
                                                }
                                                ?>"></button>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                    <span class="pagi">
                        <?php echo $this->pagination->create_links(); ?>
                    </span>
                <?php } else { ?>
                    <div class="panel-heading"> <p>No tracker available.</p></div>
                <?php } ?>
            </div>
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src = "https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script>
                                            var ajaxurl = "<?php echo site_url("tracker/ajaxTrackerToggle"); ?>";
                                            $(function () {
                                                var dateFormat = "yy-mm-dd";
                                                from = $("#fromdate")
                                                        .datepicker({
                                                            dateFormat: 'yy-mm-dd'
                                                        })
                                                        .on("change", function () {
                                                            to.datepicker("option", "minDate", getDate(this));
                                                        });
                                                to = $("#todate").datepicker({
                                                    dateFormat: 'yy-mm-dd'
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

                                            function toggleStatus(tracker_id) {
                                                $.ajax({
                                                    url: ajaxurl,
                                                    type: 'POST',
                                                    data: "tracker_id=" + tracker_id,
                                                    dataType: 'json',
                                                    success: function (data) {
                                                        if (data.status == '1')
                                                        {
                                                            window.location.reload();
                                                        } else {
                                                            alert("Try again ! ");
                                                        }
                                                    },
                                                    error: function (e) {
                                                        window.location.reload();
                                                    }
                                                });
                                            }
    </script>
</body>
</html>