<?php
error_reporting(E_ALL);



function generic_error_handler($nError, $strError, $strFile = "", $nErrorLine = 0 , $arrErrorContext = array())
{
	$strErrorDetail = "<File: >".$strFile." Line: ".$nErrorLine." ErrorNumber: ";
	$strErrorDetail.= $nError." Error: ".$strError."\n";
	echo $strErrorDetail;
}
set_error_handler("generic_error_handler"); 
defined('BASEPATH') OR exit('No direct script access allowed');
class Home extends CI_Controller {

    public function __construct() {
        parent::__construct();
        #Load Model
        $this->load->model(array('crawlmodel', 'trackermodel', 'mastermodel', 'batchlogs'));
    }
	

    public function index() {
        isUserLogin(TRUE);
        $data['vendors'] = $this->mastermodel->getAllVendor();
        $data['regions'] = $this->mastermodel->getAllRegion();
        $data['markets'] = $this->mastermodel->getAllMarket();

        $data['title'] = 'upload POs';
        $data['menu_id'] = 'opupload';
        $data['page_heading'] = 'upload POs';
        $data['view_file'] = 'home/file_upload';
        if ($this->session->flashdata('formdata')) {
            $formdata = $this->session->flashdata('formdata');
            if (isset($formdata['upload'])) {
                $data['batch_id'] = $formdata['batch_id'];
                $data['fileUploadData'] = $this->batchlogs->getFileUploadStatusCount($data['batch_id']);
                $data['fileUploadStatus'] = $this->batchlogs->getFileUploadStatus();
            }
        }
        view($data);
    }

    public function handel_upload() { 

        
        if (isUserLogin()) {
            
            $config['upload_path'] = UPLOAD;
            $config['allowed_types'] = 'pdf';
            $config['overwrite'] = FALSE;
            $config['file_ext_tolower'] = TRUE;
            $config['encrypt_name'] = TRUE;
            $config['remove_spaces'] = TRUE;
            $config['detect_mime'] = TRUE;
            $config['mod_mime_fix'] = TRUE;
            $config['max_size'] = 5120;
            $this->load->library('upload');
            $this->upload->initialize($config);
            if ($_POST['submit']) {
                
                $this->load->library('form_validation');
                $this->form_validation->set_rules('region', 'Region', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('customer', 'Customer', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('market', 'Market', 'trim|xss_clean|required|is_natural_no_zero');
                if ($this->form_validation->run() == TRUE) {
                    $region = $formdata['region'] = $this->input->post('region');
                    $customer = $formdata['customer'] = $this->input->post('customer');
                    $customerNameArr = $this->mastermodel->getVendorByID($customer);
                    $customerName = $customerNameArr['name'];
                    $crawlFunction = "";
                    if (strpos(strtolower($customerName), 'velocitel') !== false) {
                        $crawlFunction = 'crawlVelocitel';
                    } else if (strpos(strtolower($customerName), 'mastec') !== false) {
                        $crawlFunction = 'crawlMastec';
                    } else if (strpos(strtolower($customerName), 'black & veatch') !== false) {
                        $crawlFunction = 'crawlBV';
                    } else if (strpos(strtolower($customerName), 'sai') !== false) {
                        $crawlFunction = 'crawlSAI';
                    }
                 
                    //echo $crawlFunction;
                    //die();
                    $market = $formdata['market'] = $this->input->post('market');
                    $formdata['batch_id'] = 0;
                    $this->session->set_flashdata('formdata', $formdata); 
                    if (isset($_FILES['attachment']) && !empty($_FILES['attachment']['name'])) {
                        

                        include APPPATH . 'third_party/PdfToText/PdfToText.phpclass';
                        $filesCount = count($_FILES['attachment']['name']);
                        $fileUploadStatus = $this->batchlogs->getFileUploadStatus();
						echo "<pre>";
						print_r( $fileUploadStatus);
                        for ($i = 0; $i < $filesCount; $i++) { 
                            $pdf_name = $_FILES['attachment']['name'][$i];
                            /********************************/
                            $pdf_name_keys = explode("-",$pdf_name);
                            $regAbv = substr($pdf_name_keys[1], 0, 2);
                            
                            $fileExist = $this->crawlmodel->checkFileNameExists($pdf_name);
							
                            if ($fileExist) {
                                //add duplicate file
                                if ($formdata['batch_id'] == 0) {
                                    //insert upload log
                                    $formdata['upload'] = TRUE;
                                    $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                    $this->session->set_flashdata('formdata', $formdata);
                                }
                                //insert file name by upload log
                                $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                continue;
                            }
                            $_FILES['userFile']['name'] = $_FILES['attachment']['name'][$i];
                            $_FILES['userFile']['type'] = $_FILES['attachment']['type'][$i];
                            $_FILES['userFile']['tmp_name'] = $_FILES['attachment']['tmp_name'][$i];
                            $_FILES['userFile']['error'] = $_FILES['attachment']['error'][$i];
                            $_FILES['userFile']['size'] = $_FILES['attachment']['size'][$i];
                            
                            if (!$this->upload->do_upload('userFile')) { 
                                //echo __LINE__;
                                //exit;
                                //add error file                                
                                if ($formdata['batch_id'] == 0) {
                                    //insert upload log
                                    $formdata['upload'] = TRUE;
                                    $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                    $this->session->set_flashdata('formdata', $formdata);
                                }
                                //insert file name by upload log
                                $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                continue;
                                //$this->session->set_flashdata('error_message', $this->upload->display_errors());                               
                            } else {
                                $upFile = $this->upload->data();
                                $file_name = $upFile['file_name'];

                                //insert crawl log with status pending
                                $crawId = $this->crawlmodel->addCrawlLog($file_name, $pdf_name, '', $customer, 2);
								#echo "CrwId:$crawId"; 
                                //echo $crawId = 2;
                                if ($crawId) {
                                    $pdf = new PdfToText(NULL, PdfToText::PDFOPT_BASIC_LAYOUT);
                                    $pdf->BlockSeparator = "@@";
                                    $pdf->load($upFile['full_path']); //echo $pdf->Text;//exit;
                                    $lines = explode("\n", $pdf->Text);
									#echo "<pre>";
									#print_r($lines);
                                    $foundrow = FALSE;
                                    $ponumber = $POdate = $codate = '';
                                    $uploadOk = TRUE;
                                    $rev = 0;
                                    $isrev = FALSE;
                                    $orders = array();
                                    $orderkey = 0;
                                    //added by rajarshi
                                    $siteID = "";
                                    $supplier_no_flag = FALSE;
                                    $errorTrackerFlag = FALSE;
                                    $supplier_no = "";
									$lineCount = 0;
                                    $orderDetails = array();
                                    #echo "<pre>";
                                    if ($crawlFunction == 'crawlMastec') {
										
                                        $contRemove = FALSE;
                                        foreach ($lines as $linekey => $line) {
                                            if(preg_match('/Proprietary and Confidential/', $line)){
                                                unset($lines[$linekey]);
                                                $contRemove = TRUE;
                                            }
                                            if($contRemove == TRUE && $line == ""){
                                                unset($lines[$linekey]);
                                            }
                                            if($contRemove == TRUE && preg_match('/Standard Purchase Order/', $line)){
                                                unset($lines[$linekey]);
                                                $contRemove = FALSE;
                                            }
                                        }
										$gloablArray = array();
                                        foreach ($lines as $linekey => $line) {
                                            //print_r($line);
                                            if (preg_match('/@@Order@@/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $ponumber = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'order') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }

                                            if (preg_match('/@@Revision@@/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $rev = trim($value);
														$gloablArray['revision_date'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'revision') {
                                                        $found = TRUE;
                                                    }
                                                }
                                                if (!empty($ponumber)) {
                                                    //check po no and revision and vendor
                                                    $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                                    if ($orderDetails) {
                                                        //check rev
                                                        if ($orderDetails['revision'] > $rev) {
                                                            //add duplicate file                                                    
                                                            if ($formdata['batch_id'] == 0) {
                                                                //insert upload log
                                                                $formdata['upload'] = TRUE;
                                                                $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                                $this->session->set_flashdata('formdata', $formdata);
                                                            }
                                                            //insert file name by upload log
                                                            $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                                            //$this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                                                            $uploadOk = FALSE;
                                                            break;
                                                        }
                                                    }
                                                } else {
                                                    //some error                                                
                                                    if ($formdata['batch_id'] == 0) {
                                                        //insert upload log
                                                        $formdata['upload'] = TRUE;
                                                        $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                        $this->session->set_flashdata('formdata', $formdata);
                                                    }
                                                    //insert file name by upload log
                                                    $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                                    //$this->session->set_flashdata('error_message', 'error in  pdf file');
                                                    $uploadOk = FALSE;
                                                    break;
                                                }
                                            }

                                            if (preg_match('/Order Date/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $POdate = date("Y-m-d", strtotime(trim($value)));
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'order date') {
                                                        $found = TRUE;
                                                    }
                                                    if (preg_match('/Order Date/', trim($value)) && !$found) {
                                                        $POdate = date("Y-m-d", strtotime(trim(str_replace('Order Date', '', $value))));
                                                    }
                                                }
												$gloablArray['order_date'] = $POdate;
                                            }

                                            if (preg_match('/Project Type Class/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
													echo $value."\n"; 	
                                                    if ($found) {
														
                                                        $found = FALSE;
                                                        $gloablArray['project_type'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'project type class') {
                                                        $found = TRUE;
                                                    }
                                                }
												if(!isset($gloablArray['project_type']))
												{
													$temp = explode('Project Type Class', $line);
													 $gloablArray['project_type'] = $temp[1];
													
												}
                                            }
											if (preg_match('/Created By/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
													
                                                    if ($found) {
														
                                                        $found = FALSE;
                                                        $gloablArray['created_by'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'created by') {
                                                        $found = TRUE;
                                                    }
                                                }
												if(!isset($gloablArray['created_by']))
												{
													$temp = explode('Created By', $line);
													 $gloablArray['created_by'] = $temp[1];
													
												}
												 $gloablArray['created_by'] = str_replace('@@','',$gloablArray['created_by']);
                                            }
											if (preg_match('/FA Code/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        #$state_name = trim($value);
														$gloablArray['fa_code'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'fa code') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }

                                            if (preg_match('/Project#/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $supplier_no = trim($value);
														$gloablArray['project_no'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'project#') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
											
											if (preg_match('/Site Name/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $state_name = trim($value);
														$gloablArray['site_name'] = trim($value);
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'site name') {
                                                        $found = TRUE;
                                                    }
                                                }
												
												if(!isset($gloablArray['site_name']))
												{
													$temp = explode('Site Name', $line);
													$gloablArray['site_name'] = $temp[1];
													$state_name = $temp[1];
													
												}
												
                                            }
											
											if (preg_match('/Supplier No./', $line)) {
												$currentLine = $lineCount;
                                                $temp = explode($pdf->BlockSeparator, $lines[$currentLine+1]);
												$supplier_no= trim($temp[0]);
												$gloablArray['supplier_no'] = trim($temp[0]);
												$gloablArray['terms'] = trim($temp[1]);
												#echo "<pre>";
												#print_r($gloablArray);
												#exit;
                                            }
											
											

                                            if ($foundrow == TRUE && preg_match('/Total:/', $line)) {
                                                $foundrow = FALSE;
                                            }
                                            if ($foundrow == TRUE) {
                                                #echo "<------->>";print_r($line);echo "<<<------->>\n";
                                                $temp = explode($pdf->BlockSeparator, $line);
                                               /*  echo "<br>";
												echo "Desc:".trim($orders[$orderkey]['description']);
												 echo "<br>";
												 $test = preg_match('/Line@@Part Number \/ Description@@Delivery Date\/Time@@Quantity@@UOM@@Unit Price@@Tax@@Amount/', $line);
												 echo "test:<pre>";
												 print_r($test);
												 echo "temp:<pre>";
												 print_r($temp);
												 echo "loop close\n";  */
                                                /************##################****************/
                                                if(preg_match('/Line@@Part Number \/ Description@@Delivery Date\/Time@@Quantity@@UOM@@Unit Price@@Tax@@Amount /', $line) ){
                                                    $waitForNewRow = TRUE;
                                                    $waitForNewRowCount = 2;
                                                    #echo "DESC****:".$orders[$orderkey]['description'];
													#exit;
                                                }
                                                /************##################****************/
												#echo "chkDesNxtLine:$chkDesNxtLine && $chkDesNxtLine > 0 \n";
												#echo "line:$line  \n";
                                                if ($chkDesNxtLine && $chkDesNxtLine > 0) {
                                                    if($waitForNewRow == TRUE){
														if($waitForNewRowCount-- == 0){
															
															$orders[$orderkey]['description'] = trim($line);
															$chkDesNxtLine--;
															$waitForNewRow = FALSE;
														}
                                                    }
													else{
														if ($chkDesNxtLine == 1) {
															
															if($orders[$orderkey]['description']){
																$orders[$orderkey]['description'] = $orders[$orderkey]['description'] . " \n" . trim($line);
															}else{
																$orders[$orderkey]['description'] = trim($line);
															}

														}
														else if ($chkDesNxtLine == 2) {
																$time = strtotime(trim($line));
																$newformat = date('Y-m-d',$time);
																$orders[$orderkey]['delivery_date'] = $newformat;
														}
														$chkDesNxtLine--;
                                                    }
                                                }

                                                if (isset($temp[0]) && ctype_digit($temp[0])) {
                                                    #echo count($temp);
                                                    #echo "temp:<pre>";
													#print_r($temp);
                                                    #exit;
													if (count($temp) == 8) {
                                                        $orderkey++;
                                                        $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                        $orders[$orderkey]['description'] = trim($temp[1]);
                                                        $orders[$orderkey]['qty'] = trim($temp[3]);
                                                        $orders[$orderkey]['state_name'] = isset($state_name) ? $state_name : '';
														$orders[$orderkey]['uco'] = trim($temp[4]);
                                                        $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[5]));
                                                        $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[7]));
                                                        $chkDesNxtLine = 2;
                                                        //add rev row
                                                    } else if (count($temp) == 7) {
                                                        $orderkey++;
                                                        $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                        $orders[$orderkey]['description'] = trim($temp[1]);
                                                        $orders[$orderkey]['qty'] = trim($temp[2]);
														$orders[$orderkey]['uco'] = trim($temp[3]);
                                                        $orders[$orderkey]['state_name'] = isset($state_name) ? $state_name : '';
                                                        $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[4]));
                                                        $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[6]));
                                                        $chkDesNxtLine = 1;
                                                        //add rev row
                                                    } else if (count($temp) == 6) {
                                                        $orderkey++;
                                                        $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                        $orders[$orderkey]['description'] = "";
                                                        $orders[$orderkey]['qty'] = trim($temp[1]);
														 $orders[$orderkey]['uco'] = trim($temp[2]);
                                                        $orders[$orderkey]['state_name'] = isset($state_name) ? $state_name : '';
                                                        $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[3]));
                                                        $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[5]));
                                                        $chkDesNxtLine = 1;
                                                        //add rev row
                                                    }else {
                                                        //some error                                                
                                                        if ($formdata['batch_id'] == 0) {
                                                            //insert upload log
                                                            $formdata['upload'] = TRUE;
                                                            $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                            $this->session->set_flashdata('formdata', $formdata);
                                                        }
                                                        //insert file name by upload log
                                                        $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                                        //$this->session->set_flashdata('error_message', 'error in  pdf file');
                                                        $uploadOk = FALSE;
                                                        break;
                                                    }
                                                }
                                            }
                                            if ($foundrow == FALSE && preg_match('/Line@@Part Number \/ Description@@Delivery Date\/Time@@Quantity@@UOM@@Unit Price@@Tax@@Amount/', $line)) {
                                                $foundrow = TRUE;
                                                if ($rev) {
                                                    $isrev = TRUE;
                                                } else {
                                                    $isrev = FALSE;
                                                }
                                            }
										$lineCount++;
                                        }
										#exit;
                                    }
									else if ($crawlFunction == 'crawlVelocitel') { 
                                        if (strpos($pdf_name, 'Rev') !== false) {
                                            $veloPO = explode("Rev",$pdf_name);
                                            //print_r($veloPO);
                                        }
                                        if(count($veloPO)>0){
                                            if($veloPO[1]){
                                                $revtemp = explode(".",$veloPO[1]);
                                                if(ctype_digit(trim($revtemp[0]))){
                                                    $rev = trim($revtemp[0]);
                                                }
                                            }
                                        }
                                        foreach ($lines as $linekey => $line) {
                                            //print_r($line); 
                                            //echo "\n=========================\n";
                                            if (preg_match('/Subcontract Purchase Order Number:/', $line)) {
                                                $temp = explode(':', $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    //echo "temp";print_r($value);exit;
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $temp2 = explode('/', trim($value));
                                                        $ponumber = isset($temp2[0]) ? trim($temp2[0]) : '';
                                                        //$rev = isset($temp2[1]) ? trim($temp2[1]) : 0;
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'subcontract purchase order number') {
                                                        $found = TRUE;
                                                    }
                                                }
                                                //check po no and revision and vendor
                                                $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                                if ($orderDetails) {
                                                    //check rev
                                                    if ($orderDetails['revision'] > $rev) {
                                                        //add duplicate file                                                    
                                                        if ($formdata['batch_id'] == 0) {
                                                            //insert upload log
                                                            $formdata['upload'] = TRUE;
                                                            $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                            $this->session->set_flashdata('formdata', $formdata);
                                                        }
                                                        //insert file name by upload log
                                                        $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                                        $this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                                                        $uploadOk = FALSE;
                                                        break;
                                                    }
                                                }
                                            }

                                            if (preg_match('/Date Issued/', $line)) {
                                                $temp = explode(':', $line);
                                                $found = FALSE;
                                                if(trim($temp[1]) != ""){
                                                    foreach ($temp as $value) { 
                                                        if ($found) {
                                                            $found = FALSE;
                                                            $value = str_replace('@', '', $value);
                                                            if(trim($value) != ""){
                                                               $POdate = date("Y-m-d", strtotime(trim($value))); 
                                                            }else{
                                                                $POdate = date("Y-m-d");
                                                            } 
                                                            //echo $POdate;
                                                            break;
                                                        }
                                                        if (strtolower(trim($value)) == 'date issued') {
                                                            $found = TRUE;
                                                        }
                                                    }
                                                }else{
                                                    $altPo = explode("Date Issued:",$line); 
                                                    $altPoVal = trim(str_replace('@', '', $altPo[0]));
                                                    $poTemp = explode(' ', $altPoVal);
                                                    $POdate = $poTemp[0]?date("Y-m-d", strtotime(trim($poTemp[0]))):"";
                                                }
                                            }
                                            if (preg_match('/C.O. DATE/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $codate = trim($value);
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'C.O. DATE') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            if (preg_match('/Job Number:/', $line)) {
                                                $supplier_no_flag = TRUE;
                                            }
                                            if ($supplier_no_flag && !preg_match('/Job Number:/', $line)) {
                                                $supplier_no = trim(str_replace('@', '', $line));
                                                $supplier_no_flag = FALSE;
                                            }
                                            if (preg_match('/PTN Number:/', $line)) {
                                                $temp = explode(':', $line);
                                                $found = FALSE;
                                                if(trim($temp[1]) != ""){ 
                                                   foreach ($temp as $value) {
                                                        //print_r($value);echo "\n";
                                                        if ($found) {
                                                            $found = FALSE;
                                                            $ptnVal = trim(str_replace('@', '', $value));
                                                            $ptnTemp = explode(' ', $ptnVal);
                                                            if($ptnTemp[0] != ""){
                                                                echo $siteID = $ptnTemp[0];
                                                            }else{
                                                               $siteID = "";
                                                            }

                                                            break;
                                                        }
                                                        if (strtoupper(trim($value)) == 'PTN NUMBER') {
                                                            $found = TRUE;
                                                        }

                                                    } 
                                                }else{
                                                    $altPtn = explode("PTN Number",$line); 
                                                    $ptnVal = trim(str_replace('@', '', $altPtn[0]));
                                                    $ptnTemp = explode(' ', $ptnVal);
                                                    $siteID = $ptnTemp[0]?$ptnTemp[0]:"";
                                                }
                                                //echo "siteID-".$siteID;
                                            }

                                            if ($foundrow == TRUE && (preg_match('/Total:/', $line) || trim($line) == "Notes") ) {
                                                $foundrow = FALSE;
                                            }
                                            if ($foundrow == TRUE) {
                                                //print_r($line);
                                                $temp = explode('@@', $line);
                                                //echo count($temp);
                                                if (count($temp) == 1 || count($temp) == 2) {
                                                    //add description
                                                    if (!isset($orders[$orderkey]['description'])) {
                                                        $orders[$orderkey]['description'] = '';
                                                    }
                                                    $orders[$orderkey]['description'] .= $temp[0];
                                                    if (isset($temp[1])) {
                                                        $orders[$orderkey]['description'] .= ' ' . $temp[1];
                                                    }
                                                } elseif (!$isrev && count($temp) == 7) {
                                                    $orderkey++;
                                                    if (!isset($orders[$orderkey]['description'])) {
                                                        $orders[$orderkey]['description'] = '';
                                                    }
                                                    //add non rev row
                                                    $orders[$orderkey]['line'] = intval(trim($temp[1]));
                                                    $orders[$orderkey]['qty'] = trim($temp[3]);
                                                    $orders[$orderkey]['state_name'] = $siteID;
                                                    $orders[$orderkey]['description'] .= trim($temp[2]);
                                                    $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[5]));
                                                    $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[6]));
                                                } else {
                                                    //some error                                                
                                                    if ($formdata['batch_id'] == 0) {
                                                        //insert upload log
                                                        $formdata['upload'] = TRUE;
                                                        $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                        $this->session->set_flashdata('formdata', $formdata);
                                                    }
													
					                                //insert file name by upload log
                                                    $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                                    //$this->session->set_flashdata('error_message', 'error in  pdf file');
                                                    $uploadOk = FALSE;
                                                    break;
                                                }
												$orders[$orderkey]['uco'] = 'NA';
												$orders[$orderkey]['delivery_date'] = '';
                                            }
                                            if ($foundrow == FALSE && preg_match('/LINE@@QTY@@SITE NUMBER@@UNIT PRICE@@AMOUNT/', $line)) {
                                                $foundrow = TRUE;
                                                $isrev = TRUE;
                                            }
                                            if ($foundrow == FALSE && preg_match('/Group@@Item@@Description@@Quantity@@Units@@Unit Price@@Amount/', $line)) {
                                                $foundrow = TRUE;
                                                $isrev = FALSE;
                                            }
                                        } //print_r($orders);die('------------------');
										$gloablArray['fa_code'] = '';
										$gloablArray['project_type'] = '';
										$gloablArray['project_no'] = '';
										$gloablArray['terms']= '';
										$gloablArray['created_by'] = '';
										$gloablArray['order_date'] = '';
                                    }
									else if ($crawlFunction == 'crawlBV') { 
                                        $startTbl = FALSE;
                                        $skipTbl = FALSE;
                                        $cancelImposed = FALSE;
                                        $rowEnd = FALSE;
                                        $itmEnd = FALSE;
                                        $tblrowcnt = 0;
                                        $itmRowArr = array();
                                        $descrp = "";
                                        foreach ($lines as $linekey => $line) {
                                            if(preg_match('/Price@@Delivery Date/', $line)){
                                                unset($lines[$linekey]);
                                            }
                                            if(trim($line) == 'Delivery'){
                                                unset($lines[$linekey]);
                                            }
                                            if(trim($line) == 'Date'){
                                                unset($lines[$linekey]);
                                            }
                                        }
                                        foreach ($lines as $linekey => $line) {
                                            //print_r($line);
                                            
                                            if (preg_match('/PO No./', $line)) {
                                                $temp = explode('Rev.', $line);
                                                $po_parts = explode('PO No.:', $temp[0]);
                                                $ponumber = isset($po_parts[1]) ? trim($po_parts[1]) : '';
                                                $rev = isset($temp[1]) ? trim($temp[1]) : 0;
                                                $found = FALSE;

                                                //check po no and revision and vendor
                                                $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                                if ($orderDetails) {
                                                    //check rev
                                                    if ($orderDetails['revision'] > $rev) {
                                                        //add duplicate file                                                    
                                                        if ($formdata['batch_id'] == 0) {
                                                            //insert upload log
                                                            $formdata['upload'] = TRUE;
                                                            //$formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                            $this->session->set_flashdata('formdata', $formdata);
                                                        }
                                                        //insert file name by upload log
                                                        $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                                        //$this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                                                        $uploadOk = FALSE;
                                                        break;
                                                    }
                                                }
                                            }
                                            if (preg_match('/EFFECTIVE DATE/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $POdate = date("Y-m-d", strtotime(trim($value)));
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'EFFECTIVE DATE:') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            if (preg_match('/Revision Date/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $codate = date("Y-m-d", strtotime(trim($value)));
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'revision date:') {
                                                        $found = TRUE;
                                                    }
                                                }
                                                $found = FALSE;
                                            }
                                            if (preg_match('/PO Title/', $line)) {
                                                $temp = explode(':', $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $temp3 = explode('-', $value);
                                                        $supplier_no = trim(str_replace('@', '', $temp3[0]));
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'PO TITLE') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            if (preg_match('/Project Number:/', $line)) {
                                                $temp = explode(':', $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $site_name = trim(str_replace('@', '', $value));
                                                        break;
                                                    }
                                                    if (strtolower(trim($value)) == 'project number') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            /*                                             * *********************************************************** */
                                            if (preg_match('/This Purchase Order is issued pursuant to the terms and conditions/', $line)) {
                                                $rowEnd = TRUE;
                                            }
                                            if (preg_match('/Prequalification Expiration:/', $line)) {
                                                $rowEnd = TRUE;
                                            }
                                            if (preg_match('/Prequal Expiration:/', $line)) {
                                                $rowEnd = TRUE;
                                            }
                                            
                                            if ($rowEnd == FALSE) {
                                                if ($startTbl == TRUE && preg_match('/Total Purchase Order Price:/', $line)) { 
                                                    $startTbl = FALSE;
                                                    $skipTbl = TRUE;
                                                }
                                                if ($startTbl == FALSE && preg_match('/Line@@Item Description@@Quantity@@UOM@@Unit Price@@/', $line)) {
                                                    $startTbl = TRUE;
                                                    $triggerRun = 2;
                                                }
                                                if ($startTbl == TRUE) {
                                                    $triggerRun -= 1;
                                                    if ($triggerRun < 1) { // read lines  
                                                        //echo "\n".$line . "\n=====================\n";
                                                        //echo "\n=====================\n";
                                                        $itmRowArr[$tblrowcnt][] = $line;
                                                        if (preg_match('/Supplier Item Name/', $line) || preg_match('/REMOVING PAYMENT LINE/', $line)) { 
                                                            //echo "==This may be the end of item ==";
                                                            $endRowApproch = TRUE;
                                                            $iii = 1; 
                                                        }
                                                        
                                                        if (($iii) == 0 && $endRowApproch == TRUE && preg_match('/This Purchase Order/', $line)) {
                                                            //echo "==Quantity CANCELLED will be the last line==";
                                                            $cancelImposed = TRUE;
                                                        }
                                                        if (($iii) == 0 && $endRowApproch == TRUE && !preg_match('/This Purchase Order/', $line)) {
                                                            //echo "==this is the end of item==";
                                                            $endRowApproch = FALSE;
                                                            array_pop($itmRowArr[$tblrowcnt]);
                                                            $itmRowArr[$tblrowcnt+1][]= $line; 
                                                            $tblrowcnt++;
                                                            $itmEnd = TRUE; 
                                                        }
                                                        if(($iii) < 0 && $cancelImposed == TRUE && $endRowApproch == TRUE){
                                                            if (preg_match('/Quantity CANCELLED/', $line)) {
                                                                //echo "== THIS IS THE END OF ITEM ==";
                                                                $endRowApproch = FALSE;
                                                                $cancelImposed = FALSE;
                                                                $tblrowcnt++;
                                                                $itmEnd = TRUE;
                                                            }                                                        
                                                        }
                                                        if($endRowApproch == TRUE){
                                                            $iii--;
                                                        }
                                                    }
                                                }
                                                
                                            }
                                        }
                                        //print_r($itmRowArr);
                                        
                                        /************** rechecking the array items for have last element has Supplier Item Name
                                         * if not then check consicutive line no and break them to new elment
                                         **********************/
                                        $brkHappend = FALSE;
                                        $_1RowHasItem = FALSE;
                                        foreach ($itmRowArr as $key => $childArr) {
                                            //echo '==============';print_r($childArr);echo '==============';
                                            $curRow = array();
                                            if(preg_match('/Supplier Item Name/', end($childArr)) || preg_match('/REMOVING PAYMENT LINE/', end($childArr)) || preg_match('/Quantity CANCELLED/', end($childArr))){
                                                //echo "<strong>OK</strong> \n";
                                            }else{
                                                //echo "<strong>NOT OK</strong> \n";  
                                                foreach($childArr as $keyCh => $child){
                                                    //print_r($child);echo "===> \n";
                                                    $_1RowbrkLine = explode($pdf->BlockSeparator, $childArr[0]);
                                                    if($keyCh == 0 && preg_match('/Each/', $childArr[0]) && ctype_digit($_1RowbrkLine[0])){
                                                        $_1RowHasItem = TRUE;
                                                    }
                                                    if($_1RowHasItem == TRUE){
                                                        if($keyCh > 0){
                                                            $prevRow = $childArr[$keyCh-1];
                                                            $curRow = $childArr[$keyCh];
                                                            //print_r($childArr[$lop]);
                                                            //array_pop($childArr[$lop]); 
                                                            $curRowbrkLine = explode($pdf->BlockSeparator, $curRow);
                                                            if(preg_match('/Each/', $curRow) && ctype_digit($curRowbrkLine[0])){
                                                                //echo "+++++++++++++++++";
                                                                unset($itmRowArr[$key][$keyCh]);
                                                                //array_splice( $itmRowArr, count($itmRowArr)+1, 0, $curRow );
                                                                $itmRowArr[count($itmRowArr)+1][] = $curRow; 
                                                                $brkHappend = TRUE;
                                                            }else{
                                                                if($brkHappend == TRUE){
                                                                    unset($itmRowArr[$key][$keyCh]);
                                                                    $itmRowArr[count($itmRowArr)][] = $curRow;
                                                                }else{                                                                
                                                                    //$itmRowArr[count($itmRowArr)][] = $curRow;
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        $prevRow = $childArr[$keyCh-1];
                                                        $curRow = $childArr[$keyCh];
                                                        //print_r($childArr[$lop]);
                                                        //array_pop($childArr[$lop]); 
                                                        $curRowbrkLine = explode($pdf->BlockSeparator, $curRow);
                                                        if(preg_match('/Each/', $curRow) && ctype_digit($curRowbrkLine[0])){
                                                            //echo "+++++++++++++++++";
                                                            //unset($itmRowArr[$key][$keyCh]);
                                                            $_1RowHasItem = TRUE;
                                                        }
                                                    }
                                                    
                                                }
                                            }
                                        }
                                        //print_r($itmRowArr);
                                        /********** end of the recheck ****************/
                                        //echo "\n ====================================================";
                                        foreach ($itmRowArr as $key => $items) {
                                            if(!empty($items)){
                                                //print_r($items);
                                                $skipDEs = FALSE;
                                                $itmDesc = "";
                                                $qty = 0;
                                                foreach ($items as $key1 => $value) { 
                                                    //echo $value;
                                                    $brkLine = explode($pdf->BlockSeparator, $value);
                                                    $brkLine=array_map('trim',$brkLine);
                                                    if(ctype_digit($brkLine[0])){
                                                        $lineNo = $brkLine[0];
                                                    }

                                                    if(preg_match('/Each/', $value)){
                                                        $posofEACH = array_search('Each', $brkLine);
                                                        $itcnt = count($brkLine);
                                                        if($posofEACH){
                                                            if($itcnt == 6){
                                                                $ty = $brkLine[$posofEACH-1];
                                                                $tyArr = explode(' ',$ty);
                                                                //print_r($tyArr);
                                                                if(ctype_digit(end($tyArr))){
                                                                    $qty = end($tyArr);
                                                                }
                                                            }
                                                            if($itcnt == 7){
                                                               if(ctype_digit($brkLine[$posofEACH-1])){
                                                                    $qty = $brkLine[$posofEACH-1];
                                                                } 
                                                            }                                                        
                                                            $unitPrice = $brkLine[$posofEACH+1];
                                                            $AMT = $brkLine[$posofEACH+2];
                                                        }
                                                    }

                                                    if(preg_match('/Supplier Item Name/', $value) || preg_match('/REMOVING PAYMENT LINE/', $value)){
                                                        $skipDEs = TRUE;
                                                    }
                                                    if($skipDEs == FALSE){
                                                        $brkLine1 = explode($pdf->BlockSeparator, $value);
                                                        $brkLine1=array_map('trim',$brkLine1);
                                                        $itDcnt = count($brkLine1);
                                                        if(ctype_digit($brkLine1[0])){
                                                            if($itDcnt == 6){
                                                                $tyd = $brkLine1[$posofEACH-1];
                                                                $tydArr = explode(' ',$tyd);
                                                                //echo "-=-=-=-=";print_r($tydArr);
                                                                if(ctype_digit(end($tydArr))){ 
                                                                    //$tydArr = array_pop($tydArr); 
                                                                    $lastKey = key($tydArr);
                                                                    unset($tydArr[$lastKey]);
                                                                    $itmDesc .= " ".implode(" ",$tydArr);
                                                                }else{
                                                                    $itmDesc .= " ".implode(" ",$tydArr);
                                                                }
                                                            }
                                                            if($itDcnt == 7){
                                                                $itmDesc .= " ".$brkLine1[1];
                                                            }

                                                        }else{
                                                            if(!preg_match('/Total Purchase Order Price/', $value)){
                                                                $itmDesc .= " ".$value;
                                                            }
                                                        }

                                                    }
                                                }


                                                $orders[$key]['line'] = intval(trim($lineNo));
                                                $orders[$key]['qty'] = trim($qty);
                                                $orders[$key]['state_name'] = trim($site_name);
                                                $orders[$key]['description'] = $itmDesc;
                                                $orders[$key]['unite_price'] = trim(str_replace(['$', ','], '', $unitPrice));
                                                $orders[$key]['amount'] = trim(str_replace(['$', ','], '', $AMT));
                                                //echo "\n =================================\n";
                                            }
                                            
                                        }
                                        
                                        //echo '\n-------------------------------------------';
                                        //print_r($orders);
                                        //die('\n-------------------------------------------');
                                    } 
                                    else if ($crawlFunction == 'crawlSAI'){ // SAI only
                                        //echo "test";
                                        // echo __LINE__;
                                       // print_r($lines);
                                        //exit;
                                     
                                        $state_name_found = FALSE;
                                        $sitenameFound = FALSE;
                                       
                                        foreach ($lines as $linekey => $line) {
                                                 
                                              if (preg_match('/Purchase Order/', $line)) {
                                               //echo "<br>"; echo $line;exit;
                                                     $order_ids_new = explode('@@',$line);
                                                     $rder_id = $order_ids_new['1'];
                                                     //$orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                                    // $formdata['upload'] = TRUE;
                                                    // $uploadOk = TRUE;
                                                      $temp = explode($pdf->BlockSeparator, $line);

                                                $found = FALSE;
                                                
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        //$found = FALSE;
                                                        $temp2 = explode('/', trim($value));
                                                        $ponumber = isset($temp2[0]) ? trim($temp2[0]) : '';
                                                        $rev = isset($temp2[1]) ? trim($temp2[1]) : 0;
                                                        break;
                                                    }
                                                   // echo  $value;
                                                    if (strtoupper(trim($value)) == strtoupper(trim('Standard Purchase Order'))) {
                                                         $found = TRUE;
                                                    }
                                                }
                                                $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                           // var_dump($found);
                                              //  print_r($orderDetails);
                                                if ($orderDetails) {
                                                    //check rev
                                                    //echo $formdata['batch_id'];
                                                    //exit;
                                                    if ($orderDetails['revision'] > $rev) {
                                                        //add duplicate file                                                    
                                                        if ($formdata['batch_id'] == 0) {
                                                            //insert upload log
                                                            $formdata['upload'] = TRUE;
                                                            $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                            $this->session->set_flashdata('formdata', $formdata);
                                                        }
                                                        //insert file name by upload log
                                                        $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                                        //$this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                                                        $uploadOk = FALSE;
                                                        $foundrow = TRUE;
                                                        break;
                                                    }
                                                }

                                              }

                                            if (preg_match('/P.O.  NUMBER/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $temp2 = explode('/', trim($value));
                                                        $ponumber = isset($temp2[0]) ? trim($temp2[0]) : '';
                                                        $rev = isset($temp2[1]) ? trim($temp2[1]) : 0;
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'P.O.  NUMBER') {
                                                        $found = TRUE;
                                                    }
                                                }
                                                //check po no and revision and vendor
                                                $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                                                if ($orderDetails) {
                                                    //check rev
                                                    if ($orderDetails['revision'] > $rev) {
                                                        //add duplicate file                                                    
                                                        if ($formdata['batch_id'] == 0) {
                                                            //insert upload log
                                                            $formdata['upload'] = TRUE;
                                                            $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                            $this->session->set_flashdata('formdata', $formdata);
                                                        }
                                                        //insert file name by upload log
                                                        $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['duplicate']);
                                                        //$this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                                                        $uploadOk = FALSE;
                                                        break;
                                                    }
                                                }
                                            }
                                            if (preg_match('/P.O. DATE/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $POdate = date("Y-m-d", strtotime(trim($value)));
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'P.O. DATE') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            if (preg_match('/C.O. DATE/', $line)) {
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $codate = trim($value);
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'C.O. DATE') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
                                            if (preg_match('/SITE NAME/', $line)) { 
                                                print_r($line);
                                                $sitenameFound = TRUE; $snf = 2;
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $supplier_no = trim(str_replace('@', '', $value));
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'SITE NAME:') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
											if (preg_match('/PROJECT TYPE/', $line)) { 
                                                #print_r($line);
												#exit;
                                                $sitenameFound = TRUE; $snf = 2;
                                                $temp = explode($pdf->BlockSeparator, $line);
                                                $found = FALSE;
                                                foreach ($temp as $value) {
                                                    if ($found) {
                                                        $found = FALSE;
                                                        $supplier_no = trim(str_replace('@', '', $value));
                                                        break;
                                                    }
                                                    if (strtoupper(trim($value)) == 'SITE NAME:') {
                                                        $found = TRUE;
                                                    }
                                                }
                                            }
											
                                            if(trim($supplier_no) == "" && $sitenameFound && ($snf--) == 1){
                                                $sntemp = explode($pdf->BlockSeparator, $line);
                                                $supplier_no = trim(str_replace('@', '', $sntemp[1]));
                                            }
                                            //echo "ID2->".$supplier_no."==";
                                            if ($foundrow == TRUE && preg_match('/TOTAL:/', $line)) {
                                                $foundrow = FALSE;
                                            }
                                            if ($foundrow == TRUE ) {
                                                $state_name_found = FALSE;
                                                if($orders[$orderkey]['state_name']){
                                                    //echo $regAbv."-|".$orders[$orderkey]['state_name']."--|";
                                                    $lastTw = substr(trim($orders[$orderkey]['state_name']), -2);
                                                    if(strtolower(trim($lastTw)) == strtolower(trim($regAbv))){
                                                        $state_name_found = TRUE;
                                                    }
                                                }
                                                $temp = explode($pdf->BlockSeparator, $line); echo "->".count($temp) ;
                                                if (count($temp) == 1 || count($temp) == 2) {
                                                    //add description
                                                    if (!isset($orders[$orderkey]['description'])) {
                                                        $orders[$orderkey]['description'] = '';
                                                    }
                                                    if(count($temp) == 1 && $state_name_found == FALSE){
                                                        $orders[$orderkey]['state_name'] .= ' '.trim($temp[0]);
                                                    }
                                                    if(count($temp) == 1 && $state_name_found == TRUE){
                                                        $orders[$orderkey]['description'] .= ' ' . $temp[0];
                                                    }
                                                    if(count($temp) == 2 && $state_name_found == TRUE){
                                                        $orders[$orderkey]['description'] .= $temp[0].' '.$temp[1];
                                                    }
                                                    if(count($temp) == 2 && $state_name_found == FALSE){
                                                        $orders[$orderkey]['state_name'] .= ' '.trim($temp[0]);
                                                        $orders[$orderkey]['description'] .= ' '.$temp[1];
                                                    }
                                                } elseif ($isrev && count($temp) == 5) {
                                                    $orderkey++;
                                                    $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                    $orders[$orderkey]['qty'] = trim($temp[1]);
                                                    $orders[$orderkey]['state_name'] = trim($temp[2]);
                                                    $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[3]));
                                                    $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[4]));
                                                    //add rev row
                                                } elseif (!$isrev && count($temp) == 6) {
                                                    $orderkey++;
                                                    if (!isset($orders[$orderkey]['description'])) {
                                                        $orders[$orderkey]['description'] = '';
                                                    }
                                                    //add non rev row
                                                    $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                    $orders[$orderkey]['qty'] = trim($temp[1]);
                                                    $orders[$orderkey]['state_name'] = trim($temp[2]);
                                                    $orders[$orderkey]['description'] .= trim($temp[3]);
                                                    $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[4]));
                                                    $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[5]));
                                                } elseif (!$isrev && count($temp) == 5) {
                                                    $orderkey++;
                                                    if (!isset($orders[$orderkey]['description'])) {
                                                        $orders[$orderkey]['description'] = '';
                                                    }
                                                    //add non rev row
                                                    $orders[$orderkey]['line'] = intval(trim($temp[0]));
                                                    $orders[$orderkey]['qty'] = trim($temp[1]);
                                                    $orders[$orderkey]['state_name'] = trim($temp[2]);
                                                    $orders[$orderkey]['description'] .= '';
                                                    $orders[$orderkey]['unite_price'] = trim(str_replace(['$', ','], '', $temp[3]));
                                                    $orders[$orderkey]['amount'] = trim(str_replace(['$', ','], '', $temp[4]));
                                                } else {
                                                    //some error                                                
                                                    if ($formdata['batch_id'] == 0) {
                                                        //insert upload log
                                                        $formdata['upload'] = TRUE;
                                                        $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                        $this->session->set_flashdata('formdata', $formdata);
                                                    }
                                                    //insert file name by upload log
                                                    $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                                    //$this->session->set_flashdata('error_message', 'error in  pdf file');
                                                    $uploadOk = FALSE;
                                                    break;
                                                }
												$orders[$orderkey]['uco'] = 'NA';
												$orders[$orderkey]['delivery_date'] = '';
                                            }
                                            if ($foundrow == FALSE && preg_match('/LINE@@QTY@@SITE NUMBER@@UNIT PRICE@@AMOUNT/', $line)) {
                                                $foundrow = TRUE;
                                                $isrev = TRUE;
                                            }
                                            if ($foundrow == FALSE && preg_match('/LINE@@QTY@@SITE NUMBER@@DESCRIPTION@@UNIT PRICE@@AMOUNT/', $line)) {
                                                $foundrow = TRUE;
                                                $isrev = FALSE;
                                            }
                                            //echo "--".$orders[0]['description']."--";
                                        } //print_r($orders);die('-------------------');
										
										$gloablArray['fa_code'] = '';
										$gloablArray['project_type'] = '';
										$gloablArray['project_no'] = '';
										$gloablArray['terms']= '';
										$gloablArray['created_by'] = '';
										$gloablArray['order_date'] = '';
										
                                    }
                                                   
                                    else {
                                        $this->session->set_flashdata('error_message', 'There is some mismatch with the Vendor');
                                    }
												
                                    if ($uploadOk) {
                                        //check ponumber and order exist
                                      # echo "AAAA";
                                        if (!empty($ponumber) && !empty($orders) || !empty($order_ids_new)) {
                                       #  echo "BBBBB";
                                            if ($orderDetails) {
                                                //update in tracker and order


                                                $this->trackermodel->updateOrder($orderDetails['order_id'], $rev, $file_name, $pdf_name);
												$last_rev = $this->trackermodel->getTrackerLastRev($customer, $ponumber);

												if(is_array($last_rev))
												{
													$affected_rows = $this->trackermodel->updateStatus($customer, $ponumber,$last_rev[0]['max_rev']);
													
												}
		
                                                foreach ($orders as $order) {
													#echo "<pre>";
													#print_r($order);
                                                    /* check for error in order */
                                                    if($order['line'] == "" || $order['line'] == 0 || $order['line'] == '0'
                                                        || $POdate == "" || $POdate == '0000-00-00' || $POdate == '1970-01-01'
                                                        
                                                        || trim($order['description']) == ""
                                                        || $order['unite_price'] == ""
                                                        
                                                        || $supplier_no == ""   ) 
														{
                                                          # echo "Exit here..."; exit;
														   $this->trackermodel->addTrackerError($region, $market, $customer, $ponumber, $order['line'], $POdate, $order['state_name'], $order['description'], $order['unite_price'], $order['amount'], $order['qty'], $rev, $supplier_no);
                                                            $errorTrackerFlag = TRUE;
															
                                                        }else{
															
															$arraDesc = explode(',',$order['description']);
															$order['state_name'] = $arraDesc[0];
                                                            $this->trackermodel->addTracker($region, $market, $customer, $ponumber, $order['line'], $POdate, $order['state_name'], $order['description'], $order['unite_price'], $order['amount'], $order['qty'], $rev, $supplier_no,$order,$gloablArray);
                                                        } 
                                                }
                                            } else {
                                                //insert in tracker and order
                                                $this->trackermodel->addOder($ponumber, $rev, $file_name, $pdf_name, $customer);
                                                foreach ($orders as $order) {
													$arr_desc = explode(' ',$order['description'],2);
													if(count($arr_desc) == 2)
													{
														$gloablArray['part_no'] = $arr_desc[0];
														$order['description'] = $arr_desc[1];
													}
													else {
														$gloablArray['part_no'] = '';
														#$order['description'] = $arr_desc[1];
													}
													
													
                                                    /* check for error in order */
                                                    if($order['line'] == "" || $order['line'] == 0 || $order['line'] == '0'
                                                        || $POdate == "" || $POdate == '0000-00-00' || $POdate == '1970-01-01'
                                                        || $order['state_name'] == ""
                                                        || trim($order['description']) == ""
                                                        || $order['unite_price'] == ""
                                                        || $supplier_no == ""   ) {
															# echo "No Exit here..."; exit;
                                                            $this->trackermodel->addTrackerError($region, $market, $customer, $ponumber, $order['line'], $POdate, $order['state_name'], $order['description'], $order['unite_price'], $order['amount'], $order['qty'], $rev, $supplier_no);
                                                            $errorTrackerFlag = TRUE;
                                                        }else{
                                                            $this->trackermodel->addTracker($region, $market, $customer, $ponumber, $order['line'], $POdate, $order['state_name'], $order['description'], $order['unite_price'], $order['amount'], $order['qty'], $rev, $supplier_no,$order,$gloablArray);
                                                        }
                                                }
                                            }
											#exit;
                                            //make crawl log success rev and po no                                            
                                            if ($formdata['batch_id'] == 0) {
                                                //insert upload log
                                                $formdata['upload'] = TRUE;
                                                $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                $this->session->set_flashdata('formdata', $formdata);
                                            }
											// Code inserted by Virat
											if ($formdata['batch_id'] == '') {
														$formdata['batch_id']= $order['description'];
											}
												     
                                            //insert file name by upload log
                                            $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['uploaded']);
                                            //$this->session->set_flashdata('success_message', 'upload complete.');
                                            $fileExist = $this->crawlmodel->updatelog($crawId, 3, $ponumber, $rev);
                                            continue;
                                        } else {
											 echo "CCCC";
											 
                                            //add error log
                                            if ($formdata['batch_id'] == 0) {
                                                //insert upload log
                                                $formdata['upload'] = TRUE;
                                                $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                                $this->session->set_flashdata('formdata', $formdata);
                                            }
                                            //insert file name by upload log
                                            $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                            //$this->session->set_flashdata('error_message', 'error in  pdf file');
                                            continue;
                                        }
                                    }
                                } else {
									
                                    //add error log
                                    if ($formdata['batch_id'] == 0) {
                                        //insert upload log
                                        $formdata['upload'] = TRUE;
                                        $formdata['batch_id'] = $this->batchlogs->addUploadLog();
                                        $this->session->set_flashdata('formdata', $formdata);
                                    }
                                    //insert file name by upload log
                                    $this->batchlogs->addFileUploadMap($formdata['batch_id'], $pdf_name, $fileUploadStatus['error']);
                                    continue;
                                }
                            }
                        }
                        //die('------------------||||||-------------');
                        $extMsg = ""; 
                        if($errorTrackerFlag == TRUE){
                            $extMsg = '<span style="color:red; font-width:bolder"> There are some tracker with wrong data. Please check the errors section.</span>';
                        }
                        $this->session->set_flashdata('success_message', 'upload complete.'.$extMsg);
                        redirect('home');
                    } else {
                        $this->session->set_flashdata('error_message', 'Please upload a pdf file');
                        redirect('home');
                    }
                } else {
                    $this->session->set_flashdata('error_message', validation_errors());
                    redirect('home');
                }
            } else {
                $this->session->set_flashdata('error_message', 'Error ocure when upload your data!');
                redirect('home');
            }
        } else {
            redirect('home');
        }
    }

    public function download_report($upload_id = 0) {
        if (isUserLogin()) {
            $uploadId = xss_clean($upload_id);
            $this->load->library('excelphp');
            $fileUploadData = $this->batchlogs->getFileUploadMap($uploadId);
            if ($fileUploadData) {
                $fileUploadStatus = $this->batchlogs->getFileUploadStatus();
                $ploadStatus = array_flip($fileUploadStatus);
                $row = 1;
                $this->excelphp->setActiveSheetIndex(0);
                $this->excelphp->getActiveSheet()->setTitle('File Uplaod Report');
                $this->excelphp->getActiveSheet()
                        ->getStyle('A' . $row . ':H' . $row)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('#FFFF00');
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'File Name');
                $this->excelphp->getActiveSheet()->getStyle('A' . $row)->getFont()->setSize(14);
                $this->excelphp->getActiveSheet()->getStyle('A' . $row)->getFont()->setBold(true);
                $this->excelphp->getActiveSheet()->mergeCells('A' . $row . ':D' . $row);

                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, 'Status');
                $this->excelphp->getActiveSheet()->getStyle('E' . $row)->getFont()->setSize(14);
                $this->excelphp->getActiveSheet()->getStyle('E' . $row)->getFont()->setBold(true);
                $this->excelphp->getActiveSheet()->mergeCells('E' . $row . ':H' . $row);
                $row++;
                foreach ($fileUploadData as $value) {
                    $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $value['file_name']);
                    $this->excelphp->getActiveSheet()->mergeCells('A' . $row . ':D' . $row);
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $ploadStatus[$value['status']]);
                    $this->excelphp->getActiveSheet()->mergeCells('E' . $row . ':H' . $row);
                    $row++;
                }

                $filename = 'file_upload_report' . time() . '.xls'; //save our workbook as this file name
                header('Content-Type: application/vnd.ms-excel'); //mime type
                header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel5');

                $objWriter->save('php://output');
            } else {
                show_404();
            }
        } else {
            redirect('home');
        }
    }

}
