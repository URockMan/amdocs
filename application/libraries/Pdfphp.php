<?php
// require_once APPPATH."/third_party/MPDF57/mpdf.php";
class Pdfphp {
    public function __construct() {
//        parent::__construct();
    }
    
    function load($param=NULL)

	{

		include_once APPPATH."/third_party/mpdf60/mpdf.php";


		if ($params == NULL)

		{

			$param = '"en-GB-x","A4-L","","",10,10,10,10,6,3';

		}


		return new mPDF($param);

	}
}
  
