<?php
if (!defined('BASEPATH')) exit('No direct script access allowed'); 

require_once APPPATH."/third_party/PHPExcel/PHPExcel.php";
class Excelphp extends Phpexcel{
    public function __construct() {
        parent::__construct();
    }
}
