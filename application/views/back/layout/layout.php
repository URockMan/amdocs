<!doctype html>
<html>
    <head>
        <meta charset="utf-8">
        <title><?php echo (isset($title) ? $title : 'amdocs'); ?></title>
        <link href="<?php echo base_url(CSS . 'style.css'); ?>" type="text/css" rel="stylesheet">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        <link rel="stylesheet" href="<?php echo base_url(CSS . 'font-awesome.css'); ?>">
        <?php if (isset($css_files)) { ?>
            <?php foreach ($css_files as $css) { ?>
                <link rel="stylesheet" href="<?php echo base_url(CSS . $css); ?>">
            <?php } ?>
        <?php } ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    </head>

    <body>
        <header>
            <div class="wraper">
                <div class="row">
                    <div class="col-10">
                        <ul class="t_menu">
                            <li><a href="<?php echo base_url(); ?>" class="<?php echo isset($menu_id) && ($menu_id == 'opupload') ? "active" : ""; ?>">home</a></li>
                            <li><a href="<?php echo base_url('tracker/trackerList'); ?>" class="<?php echo isset($menu_id) && ($menu_id == 'trackerList') ? "active" : ""; ?>">tracker</a></li>
                            <li><a href="<?php echo base_url('invoice/invoiceList'); ?>" class="<?php echo isset($menu_id) && ($menu_id == 'invoicelist') ? "active" : ""; ?>">invoices</a></li>
                            <li><a href="<?php echo base_url('report'); ?>" class="<?php echo isset($menu_id) && ($menu_id == 'report') ? "active" : ""; ?>">report</a></li>
                            <?php
                            if (isUserLogin()) {
                                $userLoginData = $this->session->userdata('userLoginData');
                                ?>
                                <?php if (checkCurrentUserLoginRole('admin')) { ?>
                                    <li><a href="<?php echo base_url('user/staff_list') ?>" class="<?php echo isset($menu_id) && ($menu_id == 'admin') ? "active" : ""; ?>">admin</a></li>
    <?php } ?>
                                <li class="sub"><span>welcome <?php echo $userLoginData['user_name'] ?></span>
                                    <ul class="sub_menu">
                                        <li><a href="<?php echo base_url('user/change_password') ?>">change password</a></li>
                                        <li><a href="<?php echo base_url('user/logout') ?>">logout</a></li>
                                    </ul>                                
                                </li>
<?php } ?>
                        </ul>
                    </div>
                    <div class="col-2"><span class="logo"><img src="<?php echo base_url(IMAGE . 'amdocswhite.svg'); ?>" alt="amdocs"></span></div>
                </div>
            </div>
        </header>
        <div class="content">
<?php $this->load->view($view_file); ?>
        </div>
        <footer> <div class="wraper">© 2017 AMDOCS | <a href="#">PRIVACY</a> <a href="#">TERMS</a></div></footer>
            <?php
            if (isset($js_files)) {
                foreach ($js_files as $js) {
                    ?>
                <script src="<?php echo base_url(JS . $js); ?>"></script>
                <?php
            }
        }
        ?>
    </body>
</html>
