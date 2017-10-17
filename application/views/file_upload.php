<!DOCTYPE HTML>
<html lang="en">
    <head>

        <meta charset="utf-8">
        <title>File Upload</title>

        <!-- Bootstrap styles -->
        <link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

        <style>
            .form-upload {
                max-width: 330px;
                padding: 15px;
                margin: 0 auto;
            }
            .margin{
                margin:10px 0px;
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-inverse">
            <div class="container">

                <div id="navbar" class="navbar-collapse collapse">
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="<?php echo base_url(); ?>">Home</a></li>
                        <li><a href="<?php echo base_url('tracker/trackerList'); ?>">Tracker List</a></li>

                    </ul>
                </div><!--/.nav-collapse -->
            </div>
        </nav>
        <div class="container">
            <h1>File Upload</h1>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"> Select your File to Upload</h3>
                </div>
                <div class="panel-body">   
                    <?php if ($this->session->flashdata('error_message')) { ?>
                        <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> <span class="a_close"><i aria-hidden="true" class="fa fa-close"></i></span></div>
                    <?php } ?>
                    <?php if ($this->session->flashdata('success_message')) { ?>
                        <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> <span class="a_close"><i aria-hidden="true" class="fa fa-close"></i></span></div>
                    <?php } ?>
                    <?php echo form_open_multipart('home/handel_upload', 'class="form-upload"'); ?>        
                    <div class="form-group">
                        <label for="sel1">Select Region:</label>
                        <select class="form-control" id="region" name="region">
                            <?php
                            foreach ($regions as $temp) {
                                echo '<option value="' . $temp['name'] . '">' . $temp['name'] . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="vendor">Select Vendor:</label>
                        <select class="form-control" id="vendor" name="vendor">
                            <?php
                            foreach ($vendors as $temp) {
                                echo '<option value="' . $temp['name'] . '">' . $temp['name'] . '</option>';
                            }
                            ?>

                        </select>
                    </div>
                    <div class="form-group">
                        <label for="sel1">Select Market:</label>
                        <select class="form-control" id="market" name="market">
                            <?php
                            foreach ($markets as $temp) {
                                echo '<option value="' . $temp['name'] . '">' . $temp['name'] . '</option>';
                            }
                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <label class="btn btn-info" for="my-file-selector">
                            <input id="my-file-selector" type="file" style="display:none;" name="attachment" onchange="$('#upload-file-info').html($(this).val());">
                            Choose File
                        </label>
                        <span class='label label-warning' id="upload-file-info"></span>                        
                        <?php echo form_error('attachment', '<label class="error">', '</label>'); ?>
                    </div>
                    <div class="form-group">
                        <button class="btn btn-lg btn-primary btn-block margin" type="submit" value="Upload" name="submit">Upload File</button>

                    </div>
                    </form>
                </div>
            </div>
        </div>
    </body>
</html>