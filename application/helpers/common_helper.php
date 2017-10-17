<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}


if (!function_exists('view')) {

    function view($data = '') {
        $layout = 'layout';
        $CI = & get_instance();

        $CI->load->view($layout . "/layout", $data);
    }

}


if (!function_exists('isUserLogin')) {

    function isUserLogin($redirect = FALSE) {
        $CI = & get_instance();  //get instance, access the CI superobject
        $isLoggedIn = $CI->session->userdata('userLoginData');
        if ($isLoggedIn) {
            return TRUE;
        } else {
            if ($redirect) {
                redirect(site_url('user/login'), 'refresh');
            } else {
                return FALSE;
            }
        }
    }

}

if (!function_exists('checkCurrentUserLoginRole')) {

    function checkCurrentUserLoginRole($roles = '', $access = NULL) {
        if (!is_array($roles)) {
            $temp = $roles;
            $roles = array();
            $roles[] = $temp;
        }
        $CI = & get_instance();  //get instance, access the CI superobject
        $CI->load->model(array('usermodel'));
        $userType = $CI->usermodel->getUserType();
        $isLoggedIn = $CI->session->userdata('userLoginData');
        if ($isLoggedIn) {
            $roles_array = array();
            foreach ($roles as $value) {
                if (isset($userType[$value])) {
                    $roles_array[] = $userType[$value];
                }
            }
            if (in_array($isLoggedIn['user_type'], $roles_array)) {
                return TRUE;
            } else {
                return FALSE;
            }
        } else {
            return FALSE;
        }
    }

}

if (!function_exists('generatePassword')) {

    function generatePassword($min = 6, $max = 18) {
        $arr = array('a', 'b', 'c', 'd', 'e', 'f',
            'g', 'h', 'i', 'j', 'k', 'l',
            'm', 'n', 'o', 'p', 'r', 's',
            't', 'u', 'v', 'x', 'y', 'z',
            '1', '2', '3', '4', '5', '6',
            '7', '8', '9', '0');
        $str = "";
        $length = rand($min, $max);
        $array_count = count($arr) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $index = rand(0, $array_count);
            $str .= $arr[$index];
        }
        return $str;
    }

}

if (!function_exists('sendEmail')) {

    function sendEmail($to, $subject = '', $message = '', $refference = '') {
        return TRUE;
        $CI = &get_instance();
        $config = Array(
            'protocol' => 'smtp',
            'smtp_host' => $CI->config->item('email_smtp_host'),
            'smtp_port' => $CI->config->item('email_smtp_port'),
            'smtp_user' => $CI->config->item('email_smtp_user'),
            'smtp_pass' => $CI->config->item('email_smtp_pass'),
            'mailtype' => 'html',
            'crlf' => "\r\n",
            'newline' => "\r\n"
        );

        $CI->load->library('email');
        $CI->email->initialize($config);

        $CI->email->from('info@rechargkit.com', 'Recharge Kit');
        $CI->email->to($to);
        $CI->email->subject($subject);
        $CI->email->message($message);
        if ($CI->email->send()) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

}