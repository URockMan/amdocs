<?php
$formdata = $this->session->userdata('formdata');
?>
<style type="text/css">
    .upload-box{ border:1px solid #c5c5c5; background:#eeeded url(assets/images/drop-iocn.png) no-repeat center center; min-height:262px; line-height:262px; position:relative; }
    .upload-box .input-file{ opacity:0; width:100%; height:100%; top:0; left:0; z-index:2; position:absolute;}
</style>
<div class="wraper" id="home">
    <h1 class="heading-1"><?php echo (isset($page_heading) ? $page_heading : 'upload POs'); ?></h1>
    <?php echo form_open_multipart('home/handel_upload', 'class="form" id="pdfuploadform"'); ?>
    <div class="row">
        <div class="col-4">
            <div class="upload-box tac"><span class="uf_name">drag and drop files here</span><input class="input-file" id="attachment" name="attachment" type="file"></div>
        </div>

        <div class="col-5 scol_form mar0">
            <div class="form-group">
                <label>assign customer</label>
                <select class="select_box" id="customer" name="customer">
                    <option value="">select customer</option>
                    <?php
                    foreach ($vendors as $temp) {
                        if (isset($formdata['customer']) && $formdata['customer'] == $temp['id']) {
                            echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                        } else {
                            echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label>assign region</label>
                <select class="select_box" id="region" name="region">
                    <option value="">select region</option>
                    <?php
                    foreach ($regions as $temp) {
                        if (isset($formdata['region']) && $formdata['region'] == $temp['id']) {
                            echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                        } else {
                            echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                        }
                    }
                    ?>
                </select>
            </div> 
            <div class="form-group">
                <label>assign market</label>
                <select class="select_box" id="market" name="market">
                    <option value="">select market</option>
                    <?php
                    foreach ($markets as $temp) {
                        if (isset($formdata['market']) && $formdata['market'] == $temp['id']) {
                            echo '<option value="' . $temp['id'] . '" selected="selected">' . $temp['name'] . '</option>';
                        } else {
                            echo '<option value="' . $temp['id'] . '">' . $temp['name'] . '</option>';
                        }
                    }
                    ?>

                </select> 
            </div>                 
            <div class="form-group">             
                <span class="btn_small open_p"><input type="button" value="UPLOAD"  name="submit"></span>
            </div>
        </div> 
        <?php if (isset($fileUploadData)) { ?>
            <div class="col-3 scol_form">
                <div class="report_alert" style="display:block;">
                    <p>Success!</p>
                    <p>1 Files uploaded</p>
                    <p>1  <?php $uploadStatus = array_flip($fileUploadStatus);
        echo $uploadStatus[$fileUploadData[0]['status']] ?></p>
                    <p class="tac down_btn">
                        <a href="<?php echo base_url('home/download_report/' . $batch_id); ?>"><img src="<?php echo base_url(IMAGE . "/download.png"); ?>" ></a>
                        <span>DOWNLOAD</span>REPORT
                    </p>
                </div>
            </div>
<?php } ?>



    </div>
    <div class="popup">
        <h1 class="heading-1">are you sure?</h1>
        <p>1 files</p>
        <p>customer: <span id="showcustomer">SAI</span></p>
        <p>region: <span id="showregion">Region 1</span></p>
        <p>market: <span id="showmarket">ALABAMA</span></p>

        <div class="btn_grup tac">
            <span class="btn_small"><input type="submit" value="UPLOAD" name="submit"/></span>
            <span class="btn_small close_p"><input type="button" value="CANCEL"></span>
        </div>
    </div>
</form>
<?php if ($this->session->flashdata('error_message')) { ?>
    <div class=" alert alert-danger"><?php echo $this->session->flashdata('error_message'); ?> <span class="a_close"><i aria-hidden="true" class="fa fa-close"></i></span></div>
<?php } ?>
        <?php if ($this->session->flashdata('success_message')) { ?>
    <div class="alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> <span class="a_close"><i aria-hidden="true" class="fa fa-close"></i></span></div>
<?php } ?>
</div>


<script type="text/javascript">
    $(function () {
        $('.open_p').click(function () {
            var error = false;
            var error_text = '';
            if ($('#customer').val() == '') {
                error_text += '<p>Please select a customer.</p>';
                error = true;
            }
            if ($('#region').val() == '') {
                error_text += '<p>Please select a region.</p>';
                error = true;
            }

            if ($('#market').val() == '') {
                error_text += '<p>Please select a market.</p>';
                error = true;
            }

            if ($('#attachment').val() == '') {
                error_text += '<p>Please upload a pdf file.</p>';
                error = true;
            }

            if (error) {
                if ($('div.alert-danger').length > 0) {
                    $('div.alert-danger').html(error_text);
                } else {
                    $('#home').append('<div class=" alert alert-danger">' + error_text + '</div>')
                }
            } else {
                $('div.alert-danger').remove();
                $('#showcustomer').text($('#customer option:selected').text());
                $('#showregion').text($('#region option:selected').text());
                $('#showmarket').text($('#market option:selected').text());
                $('.popup').fadeIn();
            }
        });
        $('.close_p').click(function () {
            $('.popup').fadeOut();
            $('#showcustomer').text('');
            $('#showregion').text('');
            $('#showmarket').text('');
        });

        $('.input-file').change(function () {
            var names = [];
            for (var i = 0; i < $(this).get(0).files.length; ++i) {
                names.push('<span>' + $(this).get(0).files[i].name + '</span>');
            }
            $('.uf_name').html(names);
        });
    });


</script>