<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo (isset($title) ? $title : 'amdocs'); ?></title>
        <link href="<?php echo base_url(CSS . "style.css"); ?>" type="text/css" rel="stylesheet">
    </head>

    <body class="log_bg">

        <div class="content">
            <div class="wraper">
                <div class="logo_sm tac"><a href="<?php echo base_url(); ?>"><img src="<?php echo base_url(IMAGE . "amdocswhite.svg"); ?>" alt="amdocs"></a></div>

                <?php echo form_open('user/login'); ?>
                <div class="form-box">
                    <h1 class="heading-1"><?php echo (isset($page_title) ? $page_title : 'login'); ?></h1>
                    <?php if ($this->session->flashdata('error_message')) { ?>
                    <div class=" alert alert-danger"><?php echo validation_errors(); echo $this->session->flashdata('error_message'); ?> </div>
                <?php } ?>
                <?php if ($this->session->flashdata('success_message')) { ?>
                    <div class=" alert alert-success"><?php echo $this->session->flashdata('success_message'); ?> </div>
                <?php } ?>
                    <div class="form-box-block">
                        <label>email</label>
                        <input id="email" class="st_box" placeholder="email" name="email" type="text" value="<?php echo set_value('email'); ?>" >
                        <?php  echo form_error('email', '<span class="in_danger">', '</span>'); ?>

                    </div>
                    <div class="form-box-block">
                        <label>password</label>
                        <input id="password" class="st_box" placeholder="******" name="password" type="password">
                        <?php  echo form_error('password', '<span class="in_danger">', '</span>'); ?>
                    </div>
                    <div class="form-box-block form-btn-block row">
                        <div class="col-6">
                            <a href="<?php echo base_url("user/forgotpassword"); ?>">Forgot password?</a>
                        </div>
                        <div class="col-6 tar">
                            <span class="btn_small"><input value="LOGIN" type="submit" name="loginsub"></span>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <footer> <div class="wraper">© 2017 AMDOCS | <a href="#">PRIVACY</a> <a href="#">TERMS</a></div></footer>
    </body>
</html>























<?php /* echo form_open('user/login'); ?>
<div class="form login_bg">
    <?php if (validation_errors() || $this->session->flashdata('error_message')) { ?>
        <div class="row">
            <div class="alert-danger"><?php
                echo validation_errors();
                echo $this->session->flashdata('error_message');
                ?><span class="a_close"><i aria-hidden="true" class="fa fa-close"></i></span>
            </div>
        </div>
    <?php } ?>
    <div class="form-group">
        <label class="login_label">Email</label>
        <input type="text" name="email" value="<?php echo set_value('email'); ?>" id="email" class="tbox" autocomplete="off" placeholder="Email" />
        <?php ?>
    </div>
    <div class="form-group">
        <label class="login_label">Password</label>
        <input type="password" name="password" value="<?php echo set_value('password'); ?>" id="password" class="tbox" placeholder="Password"  />
        <?php ?>
    </div>
    <div class="row no_pad">
        <div class="col-8 no_pad"><a href="<?php echo site_url("user/forgotpassword"); ?>">Forgot Password</a></div>
        <div class="col-4 no_pad tar">
            <input type="submit" name="loginsub" value="Submit" id="loginsub" class="btn_small bg_blue"  />
        </div>
    </div>
</div>
<?php echo form_close(); */?>
