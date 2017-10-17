<?php

class Tracker extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('trackermodel', 'mastermodel', 'invoicemodel'));
    }

    public function index() {
        redirect('tracker/trackerList');
    }

    public function trackerList() {
        isUserLogin(TRUE);
		#echo "<pre>";
		#print_r($_POST);
		#exit;
        if ($this->uri->segment(3) == 'reset') {
            $this->session->unset_userdata('tracker_search');
            redirect('tracker/trackerList');
        }
        if ($this->input->post('vendor')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('vendor', 'Vendor', 'trim|xss_clean|required');
            $this->form_validation->set_rules('region', 'Region', 'trim|xss_clean|required');
            $this->form_validation->set_rules('ponumber', 'PO Number', 'trim|xss_clean');
            $this->form_validation->set_rules('market', 'Market', 'trim|xss_clean|required');
            $this->form_validation->set_rules('status[]', 'Status', 'trim|xss_clean|required');
            $this->form_validation->set_rules('fromdate', 'Vendor', 'trim|xss_clean');
            $this->form_validation->set_rules('todate', 'Vendor', 'trim|xss_clean');
            $this->form_validation->set_rules('orderfield', 'orderfield', 'trim|xss_clean|in_list[vendor,region,market,po_number,line,po_line_rev,po_date,site_name,supplier_no,amount]');
            $this->form_validation->set_rules('ordertype', 'ordertype', 'trim|xss_clean|in_list[asc,desc]');
            if ($this->form_validation->run()) {
                $report_search['todate'] = $this->input->post('todate');
                $report_search['fromdate'] = $this->input->post('fromdate');
                $report_search['vendor'] = $this->input->post('vendor');
                $report_search['status'] = $this->input->post('status[]');
                $report_search['region'] = $this->input->post('region');
                $report_search['ponumber'] = $this->input->post('ponumber');
                $report_search['market'] = $this->input->post('market');
                $report_search['orderfield'] = $this->input->post('orderfield');
                $report_search['ordertype'] = $this->input->post('ordertype');
                $report_search['reset'] = TRUE;
                $this->session->set_userdata('tracker_search', $report_search);
            }
        } else if (!$this->session->userdata('tracker_search')) {
            $report_search['todate'] = '';
            $report_search['fromdate'] = '';
            $report_search['vendor'] = 'All';
            $report_search['status'] = array(1, 2, 3, 4, 5);
            $report_search['region'] = 'All';
            $report_search['ponumber'] = '';
            $report_search['market'] = 'All';
            $report_search['orderfield'] = 'po_number';
            $report_search['ordertype'] = 'asc';
            $this->session->set_userdata('tracker_search', $report_search);
        }
        $report_search = $this->session->userdata('tracker_search');

        $this->load->library("pagination");
        $config['base_url'] = base_url("tracker/trackerList");
        $config['total_rows'] = $this->trackermodel->gettrackerCount($report_search['vendor'], $report_search['region'], $report_search['market'], $report_search['status'], $report_search['ponumber'], $report_search['fromdate'], $report_search['todate']);
        $config['per_page'] = 50;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['num_links'] = 2;
        $config['cur_tag_open'] = '<li class="active"><a class="current">';
        $config['cur_tag_close'] = '</a></li>';
        $config['next_link'] = '<img src="' . base_url(IMAGE . "right-arrow.png") . '">';
        $config['next_tag_open'] = '<li>';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<img src="' . base_url(IMAGE . "left-arrow.png") . '">';
        $config['prev_tag_open'] = '<li>';
        $config['prev_tag_close'] = '</li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</li>';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['first_tag_open'] = '<li>';
        $config['first_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li>';
        $config['last_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        if ($this->uri->segment($config['uri_segment'])) {
            $page = $this->uri->segment($config['uri_segment']);

            if ((is_int($page) || ctype_digit($page)) && (int) $page > 0) {
                
            } else {
                show_404();
            }
        } else {
            $page = 1;
        }

        $data['trackerList'] = $this->trackermodel->getAllTracker($config["per_page"], ($page - 1) * $config['per_page'], $report_search['vendor'], $report_search['region'], $report_search['market'], $report_search['status'], $report_search['ponumber'], $report_search['fromdate'], $report_search['todate'], $report_search['orderfield'], $report_search['ordertype']);

        $added_invoice_ids = $this->session->flashdata('added_invoice_ids');
        if (!empty($added_invoice_ids) && is_array($added_invoice_ids)) {
            $this->session->set_userdata('generate_invoice_ids', $added_invoice_ids);
            $data['added_invoice_ids'] = $added_invoice_ids;
        } else {
            $this->session->unset_userdata('generate_invoice_ids');
            $data['added_invoice_ids'] = NULL;
        }
		
		if(is_array($this->input->post('selected_column')) &&  !empty($this->input->post('selected_column')))
		{
			$selected_column =  $this->input->post('selected_column');
			$this->session->set_userdata('selected_column', $selected_column);
		}
		else {
			$selected_column =  array("vendor", "region", "market","po_number", "line", "po_line_rev","po_date","site_name","supplier_no","description","rev","qty","unit_price","amount","status");
			
			if (!$this->session->userdata('selected_column')) {
				$this->session->set_userdata('selected_column', $selected_column);
            }
		}
        
		$data['vendors'] = $this->mastermodel->getAllVendor();
        $data['regions'] = $this->mastermodel->getAllRegion();
        $data['markets'] = $this->mastermodel->getAllMarket();
        $data['status'] = $this->trackermodel->getTrackerStatus();
        $data['ajaxurl'] = base_url('tracker/ajaxTrackerToggle');
        $data['title'] = 'tracker';
        $data['menu_id'] = 'trackerList';
        $data['page_heading'] = 'tracker';
        $data['js_files'] = array('tracker.js');
        $data['view_file'] = 'tracker/tracker_list_view';
        view($data);
    }

    public function exportTrackerList() {
        if (isUserLogin()) {
            $report_search = $this->session->userdata('tracker_search');
            $this->load->library('excelphp');
            $status = $this->trackermodel->getTrackerStatus();
            $status = array_flip($status);

            $trackerList = $this->trackermodel->getAllTracker(NULL, NULL, $report_search['vendor'], $report_search['region'], $report_search['market'], $report_search['status'], $report_search['ponumber'], $report_search['fromdate'], $report_search['todate'], $report_search['orderfield'], $report_search['ordertype']);
            $this->excelphp->setActiveSheetIndex(0);
            $this->excelphp->getActiveSheet()->setTitle('Tracker List Report');
            $row = 1;

            $this->excelphp->getActiveSheet()
                    ->getStyle('A' . $row . ':N' . $row)
                    ->getFill()
                    ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setRGB('FFFF00');
            $this->excelphp->getActiveSheet()
                    ->getStyle('A' . $row . ':N' . $row)
                    ->getAlignment()
                    ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            $this->excelphp->getActiveSheet()
                    ->getStyle('A' . $row . ':N' . $row)
                    ->getNumberFormat()
                    ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
            
            $this->excelphp->getActiveSheet()->getColumnDimension('A')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('B')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('C')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('D')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('E')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('F')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('G')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('H')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('I')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('J')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('K')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('L')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('M')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getColumnDimension('N')->setAutoSize(TRUE);
            $this->excelphp->getActiveSheet()->getStyle('A' . $row . ':N' . $row)->getFont()->setSize(12)->setBold(true);
            $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'Region');
            $this->excelphp->getActiveSheet()->setCellValue('B' . $row, 'Market');
            $this->excelphp->getActiveSheet()->setCellValue('C' . $row, 'Customer');
            $this->excelphp->getActiveSheet()->setCellValue('D' . $row, 'PO Number');
            $this->excelphp->getActiveSheet()->setCellValue('E' . $row, 'Line');
            $this->excelphp->getActiveSheet()->setCellValue('F' . $row, 'PO and Line');
            $this->excelphp->getActiveSheet()->setCellValue('G' . $row, 'PO Date');
            $this->excelphp->getActiveSheet()->setCellValue('H' . $row, 'ID1');
            $this->excelphp->getActiveSheet()->setCellValue('I' . $row, 'ID2');
            $this->excelphp->getActiveSheet()->setCellValue('J' . $row, 'Description');
            $this->excelphp->getActiveSheet()->setCellValue('K' . $row, 'Quantity');
            $this->excelphp->getActiveSheet()->setCellValue('L' . $row, 'Amount');
            $this->excelphp->getActiveSheet()->setCellValue('M' . $row, 'Total');
            $this->excelphp->getActiveSheet()->setCellValue('N' . $row, 'Status');
            $row++;
            $regNameArr = array();
            $custNameArr = array();
            $marketNameArr = array();
            foreach ($trackerList as $value) {
                $regNameArr[] = $value['region_name'];
                $custNameArr[] = $value['vendor_name'];
                $marketNameArr[] = $value['market_name'];
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $value['region_name']);
                $this->excelphp->getActiveSheet()->setCellValue('B' . $row, $value['market_name']);
                $this->excelphp->getActiveSheet()->setCellValue('C' . $row, $value['vendor_name']);
                $this->excelphp->getActiveSheet()->setCellValue('D' . $row, $value['po_number']);
                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $value['line']);
                $this->excelphp->getActiveSheet()->setCellValue('F' . $row, $value['po_line_rev']);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $row, date("m/d/Y",strtotime($value['po_date'])));
                $this->excelphp->getActiveSheet()->setCellValue('H' . $row, $value['site_name']);
                $this->excelphp->getActiveSheet()->setCellValue('I' . $row, $value['supplier_no']);
                $this->excelphp->getActiveSheet()->setCellValue('J' . $row, $value['description']);
                $this->excelphp->getActiveSheet()->setCellValue('K' . $row, $value['qty']);
                $this->excelphp->getActiveSheet()->setCellValue('L' . $row, $value['unit_price'])->getStyle('L' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $this->excelphp->getActiveSheet()->setCellValue('M' . $row, $value['amount'])->getStyle('M' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                
//                if ($status[$value['status']] == 'invoiced') {
//                    $styleArray_invoiced = array(
//                        'font' => array(
//                            'bold' => true,
//                            'color' => array('rgb' => 'FF0000'),
//                            'size' => 12,
//                    ));
//
//                    $this->excelphp->getActiveSheet()->getStyle('N' . $row . ':N' . $row)->applyFromArray($styleArray_invoiced);
//                }
//
//                if ($status[$value['status']] == 'open') {
//                    $styleArray_open = array(
//                        'font' => array(
//                            'bold' => true,
//                            'color' => array('rgb' => '0000FF'),
//                            'size' => 12,
//                    ));
//
//                    $this->excelphp->getActiveSheet()->getStyle('N' . $row . ':N' . $row)->applyFromArray($styleArray_open);
//                }

                
                
                $this->excelphp->getActiveSheet()->setCellValue('N' . $row, ($status[$value['status']] == 'riv') ? 'ready to invoice' : $status[$value['status']]);
                $row++;
            }
            $mystyleArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN
                    )
                )
            );
            $newrowval = $row-1;
            $this->excelphp->getActiveSheet()->getStyle('A1:N'.$newrowval)->applyFromArray($mystyleArray);
            
            $style_align = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );

            $this->excelphp->getActiveSheet()->getStyle("A1:N".$newrowval)->applyFromArray($style_align);
            //$this->excelphp->getActiveSheet()->getStyle("K1:N".$newrowval)->applyFromArray($style_align);
            
            $filekey_reg = "Region";
            $filekey_cust = "Customer";
            $filekey_market = "Market";
            if(count($regNameArr)){
                if (count(array_unique($regNameArr)) === 1 && end($regNameArr) === $regNameArr[0]) {
                    //$filekey_reg = "Customer_".$regNameArr[0]."_Tracker";
                    $filekey_reg = $regNameArr[0];
                }else{
                    $filekey_reg = "Region";
                }
            }
            if(count($custNameArr)){
                if (count(array_unique($custNameArr)) === 1 && end($custNameArr) === $custNameArr[0]) {
                    //$filekey_reg = "Customer_".$regNameArr[0]."_Tracker";
                    $filekey_cust = $custNameArr[0];
                }else{
                    $filekey_cust = "Customer";
                }
            }
            if (count($marketNameArr)) {
                if (count(array_unique($marketNameArr)) === 1 && end($marketNameArr) === $marketNameArr[0]) {
                    //$filekey_reg = "Customer_".$regNameArr[0]."_Tracker";
                    $filekey_market = $marketNameArr[0];
                } else {
                    $filekey_market = "Market";
                }
            }
            $filename = $filekey_cust . '_' . $filekey_reg . '_' . $filekey_market . '_Tracker_' . time() . '.xlsx'; //save our workbook as this file name
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel2007');

            $objWriter->save('php://output');
        } else {
            redirect('tracker/trackerList');
        }
    }

    public function editTracker($tracker_id = 0) {
        isUserLogin(TRUE);
        $tracker_id = xss_clean($tracker_id);
        $data['tracker_detail'] = $this->trackermodel->getTrackerById($tracker_id);
        if ($data['tracker_detail']['tracker_id']) {
            if ($this->input->post('update')) {
                $this->load->library(array('form_validation'));
                $this->form_validation->set_rules('region', 'region', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('customer', 'mustomer', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('market', 'market', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('po_date', 'PO date', 'trim|xss_clean|required');
                $this->form_validation->set_rules('po_number', 'PO number', 'trim|xss_clean|required');
                $this->form_validation->set_rules('po_line', 'line', 'trim|xss_clean|required|is_natural_no_zero');
                $this->form_validation->set_rules('po_number_line', 'PO and line', 'trim|xss_clean|required');
                $this->form_validation->set_rules('site_id', 'site id', 'trim|xss_clean|required');

                $this->form_validation->set_rules('description', 'description', 'trim|xss_clean');
                $this->form_validation->set_rules('qty', 'quantity', 'trim|required|xss_clean|is_natural_no_zero');
                $this->form_validation->set_rules('unit_price', 'unit Price', 'trim|required|xss_clean|regex_match[/^(\d)*(\.\d{0,2})?$/]');
                $this->form_validation->set_rules('amount', 'amount', 'trim|required|xss_clean|regex_match[/^(\d)*(\.\d{0,2})?$/]');
                if ($this->form_validation->run()) {
                    //update tracker
                    $result = $this->trackermodel->updareTracker($data['tracker_detail']['tracker_id'], $this->input->post('description'), $this->input->post('unit_price'), $this->input->post('amount'), $this->input->post('qty'), $this->input->post('region'), $this->input->post('customer'), $this->input->post('market'), $this->input->post('po_date'), $this->input->post('po_number'), $this->input->post('po_line'), $this->input->post('po_number_line'), $this->input->post('site_id'));
                    $this->session->set_flashdata('success_message', 'Update successfully');
                    redirect('tracker/trackerList');
                }
            }
            $data['vendors'] = $this->mastermodel->getAllVendor();
            $data['regions'] = $this->mastermodel->getAllRegion();
            $data['markets'] = $this->mastermodel->getAllMarket();
            $data['status'] = $this->trackermodel->getTrackerStatus();

            $data['title'] = 'edit tracker';
            $data['menu_id'] = 'trackerList';
            $data['page_heading'] = 'edit tracker';
            $data['js_files'] = array('tracker.js');
            $data['view_file'] = 'tracker/edit_tracker_view';
            view($data);
        } else {
            $this->session->set_flashdata('error_message', "No Trcker found with this key!");
            redirect("tracker/trackerList");
        }
    }
    
    public function addTracker() {
        isUserLogin(TRUE);
        $tracker_id = xss_clean($tracker_id);
        $data['tracker_detail'] = $this->trackermodel->getTrackerById($tracker_id);
        if ($this->input->post('add')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('region', 'region', 'trim|xss_clean|required|is_natural_no_zero');
            $this->form_validation->set_rules('customer', 'mustomer', 'trim|xss_clean|required|is_natural_no_zero');
            $this->form_validation->set_rules('market', 'market', 'trim|xss_clean|required|is_natural_no_zero');
            $this->form_validation->set_rules('po_date', 'PO date', 'trim|xss_clean|required');
            $this->form_validation->set_rules('po_number', 'PO number', 'trim|xss_clean|required');
            $this->form_validation->set_rules('po_line', 'line', 'trim|xss_clean|required|is_natural_no_zero');
            $this->form_validation->set_rules('site_name', 'id1', 'trim|xss_clean|required');
            $this->form_validation->set_rules('supplier_no', 'id2', 'trim|xss_clean|required');

            $this->form_validation->set_rules('description', 'description', 'trim|xss_clean');
            $this->form_validation->set_rules('qty', 'quantity', 'trim|required|xss_clean');
            $this->form_validation->set_rules('unit_price', 'unit Price', 'trim|required|xss_clean|regex_match[/^(\d)*(\.\d{0,2})?$/]');
            $this->form_validation->set_rules('amount', 'amount', 'trim|required|xss_clean|regex_match[/^(\d)*(\.\d{0,2})?$/]');
            if ($this->form_validation->run()) {
                
                //check po no and revision and vendor
                $insertFlag = TRUE;
                $customer = $this->input->post('customer');
                $ponumber = $this->input->post('po_number');
                $rev = $this->input->post('rev');
                $orderDetails = $this->trackermodel->getOrderDetils($customer, $ponumber);
                if ($orderDetails) {
                    //check rev
                    if ($orderDetails['revision'] > $rev) {                        
                        $this->session->set_flashdata('error_message', 'an upper or same revision already added in server');
                        redirect('tracker/addTracker');
                    }else{
                        $result = $this->trackermodel->addTracker($this->input->post('region'), $this->input->post('market'), $this->input->post('customer'), $this->input->post('po_number'), $this->input->post('po_line'), $this->input->post('po_date'), $this->input->post('site_name'), $this->input->post('description'), $this->input->post('unit_price'), $this->input->post('amount'), $this->input->post('qty'), $this->input->post('rev'), $this->input->post('supplier_no'));
                        $this->session->set_flashdata('success_message', 'Updated successfully');
                        redirect('tracker/trackerList');
                    }
                }else{
                    $result = $this->trackermodel->addTracker($this->input->post('region'), $this->input->post('market'), $this->input->post('customer'), $this->input->post('po_number'), $this->input->post('po_line'), $this->input->post('po_date'), $this->input->post('site_name'), $this->input->post('description'), $this->input->post('unit_price'), $this->input->post('amount'), $this->input->post('qty'), $this->input->post('rev'), $this->input->post('supplier_no'));
                    $this->session->set_flashdata('success_message', 'Added successfully');
                    redirect('tracker/trackerList');
                }
                //add tracker
                
                
            }
        }
        $data['vendors'] = $this->mastermodel->getAllVendor();
        $data['regions'] = $this->mastermodel->getAllRegion();
        $data['markets'] = $this->mastermodel->getAllMarket();
        $data['status'] = $this->trackermodel->getTrackerStatus();

        $data['title'] = 'add tracker';
        $data['menu_id'] = 'trackerList';
        $data['page_heading'] = 'add tracker';
        $data['js_files'] = array('tracker.js');
        $data['view_file'] = 'tracker/add_tracker_view';
        view($data);
    }

    public function ajaxTrackerToggle() {
        if (isUserLogin()) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('tracker_id', 'Tracker', 'trim|required|xss_clean');
            if ($this->form_validation->run()) {
                $tracker_detail = $this->trackermodel->getTrackerById($this->input->post('tracker_id'));
                $trackerStatus = $this->trackermodel->getTrackerStatus();
                if (isset($tracker_detail['tracker_id'])) {
                    if ($tracker_detail['status'] == $trackerStatus['riv']) {
                        $status = $trackerStatus['open'];
                    } else {
                        $status = $trackerStatus['riv'];
                    }
                    $result_status = $this->trackermodel->changeTrackerStatus($tracker_detail['tracker_id'], $status);
                    $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode(array('status' => $result_status)));
                } else {
                    $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode(array('status' => 0)));
                }
            }
        } else {
            $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => 0)));
        }
    }

    public function updateTrakeChk() {
        //$this->session->unset_userdata('trkrChk');
        //print_r($this->input->post('id'));
        $chkTrkArr = $this->session->userdata('trkrChk');
        //print_r($chkTrkArr);
        if($this->input->post('status') == 'true'){
            if($chkTrkArr){
                $chkTrkArr .= ','.$this->input->post('id');
            }else{
                $chkTrkArr .= $this->input->post('id');
            }
        }
        if($this->input->post('status') == 'false'){
            if($chkTrkArr){
                $temp = explode(",",$chkTrkArr);
                $temp = array_diff( $temp, [$this->input->post('id')] );
                $chkTrkArr = implode(",",$temp);
            }else{
                $chkTrkArr ="";
            }
        }
        //print_r($chkTrkArr);
        $this->session->set_userdata('trkrChk', $chkTrkArr);
        //echo "<br>";
        //print_r($this->session->userdata('trkrChk'));
    }
    public function generateInvoice() {
        if (isUserLogin()) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('invoicedate', 'Invoice Date', 'trim|required|xss_clean');
//            $this->form_validation->set_rules('tracker[]', 'Tracker', 'trim|xss_clean|required', array(
//                'required' => 'please select atleast one tracker to invoice',
//            ));
            if ($this->form_validation->run()) {
                if(!$this->session->userdata('trkrChk')){
                    $this->session->set_flashdata('error_message', 'please select atleast one tracker to invoice');
                    redirect('tracker/trackerList');
                }else{
                    $trackerStatus = $this->trackermodel->getTrackerStatus();
                    //get tracker details by po order
                    ////$trackerList = $this->trackermodel->getTrackersForInvoice($this->input->post('tracker'));
                    print_r($this->session->userdata('trkrChk'));
                    $trackerLISTARR = explode(",",$this->session->userdata('trkrChk')); print_r($trackerLISTARR);
                    $trackerList = $this->trackermodel->getTrackersForInvoice($trackerLISTARR); //die('aaa');
//                    if (count($trackerList) !== count($this->input->post('tracker'))) {
//                        $this->session->set_flashdata('error_message', 'some error occured please try later.');
//                        redirect('tracker/trackerList');
//                    }
                    $ponumber = NULL;
                    $amount = $vendor_id = $region_id = $market_id = 0;
                    $invoiceArray = $trackerarray = array();
                    $error = FALSE;
                    $this->db->trans_start();
//                    echo "<pre>";
//                    print_r($trackerList);                
//                    die('uuu');
                    foreach ($trackerList as $tracker) {
                        if ($ponumber != $tracker['po_number'] || $vendor_id != $tracker['vendor']) {
                            if ($ponumber != NULL && !$error) {
                                //insert invoice
                                $invoiceArray[] = $invoiceID = $this->invoicemodel->addInvoice($ponumber, $this->input->post('invoicedate'), $vendor_id, $region_id, $market_id, $amount);
                                if ($invoiceID) {
                                    //update invoice in traker
                                    $row = $this->trackermodel->changetoInvoice($trackerarray, $invoiceID);
                                    if ($row != count($trackerarray)) {
                                        $error = TRUE;
                                        break;
                                    }
                                } else {
                                    $error = TRUE;
                                    break;
                                }
                            }

                            $ponumber = $tracker['po_number'];
                            $trackerarray = array();
                            $amount = $vendor_id = $region_id = $market_id = 0;
                        }
                        $trackerarray[] = $tracker['tracker_id'];
                        $vendor_id = $tracker['vendor'];
                        $region_id = $tracker['region'];
                        $market_id = $tracker['market'];
                        $amount += $tracker['amount'];
                    }
                    if (count($trackerarray) > 0 && !$error) {
                        if ($ponumber != NULL && !$error) {
                            //insert invoice
                            $invoiceArray[] = $invoiceID = $this->invoicemodel->addInvoice($ponumber, $this->input->post('invoicedate'), $vendor_id, $region_id, $market_id, $amount);

                            if ($invoiceID) {
                                //update invoice in traker
                                $row = $this->trackermodel->changetoInvoice($trackerarray, $invoiceID);
                                if ($row != count($trackerarray)) {
                                    $error = TRUE;
                                }
                            } else {
                                $error = TRUE;
                            }
                        }

                        $ponumber = $tracker['po_number'];
                        $trackerarray = array();
                        $amount = $vendor_id = $region_id = $market_id = 0;
                    }
                    if ($this->db->trans_status() === FALSE || $error) {
                        $this->db->trans_rollback();
                        $this->session->set_flashdata('error_message', 'some error occured please try later.');
                        redirect('tracker/trackerList');
                    } else {
                        $this->db->trans_commit();
                        $this->session->set_flashdata('added_invoice_ids', $invoiceArray);
                        $this->session->set_flashdata('success_message', 'invoice added successfully.');
                        $this->session->unset_userdata('trkrChk');
                        redirect('tracker/trackerList');
                    }
                }
                
            } else {
                $this->session->set_flashdata('error_message', validation_errors());
                redirect('tracker/trackerList');
            }
        } else {
            redirect('tracker/trackerList');
        }
    }

    public function exportGenerateInvoice() {
        if (isUserLogin()) {
            $added_invoice_ids = $this->session->userdata('generate_invoice_ids');
            if (!empty($added_invoice_ids) && is_array($added_invoice_ids)) {
                $invoiceList = $this->invoicemodel->getInvoiceDetailsByIds($added_invoice_ids, TRUE);
                if ($invoiceList) {
                    $this->load->library('zip');
                    foreach ($invoiceList as $invoice) {
                        $src = $this->genaratePdfInvoice($invoice['invoice_id']);
                        if ($src) {
                            $name = $src[0];
                            $data = $src[1];
                            $this->zip->add_data($name, $data);
                        }
                    }
                    $this->load->library('excelphp');
                    //get invoice details
                    $this->excelphp->setActiveSheetIndex(0);
                    $this->excelphp->getActiveSheet()->setTitle('Invoice List Report');

                    $row = 1;

                    $this->excelphp->getActiveSheet()
                            ->getStyle('A' . $row . ':H' . $row)
                            ->getFill()
                            ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                            ->getStartColor()
                            ->setARGB('FFFF00');
                    $this->excelphp->getActiveSheet()
                            ->getStyle('A' . $row . ':H' . $row)
                            ->getAlignment()
                            ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    $this->excelphp->getActiveSheet()
                            ->getStyle('A' . $row . ':H' . $row)
                            ->getNumberFormat()
                            ->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
                    $this->excelphp->getActiveSheet()->getColumnDimension('A')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('B')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('C')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('D')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('E')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('F')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('G')->setWidth(20);
                    $this->excelphp->getActiveSheet()->getColumnDimension('H')->setWidth(20);

                    $this->excelphp->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->getFont()->setSize(12)->setBold(true);
                    $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'customer');
                    $this->excelphp->getActiveSheet()->setCellValue('B' . $row, 'region');
                    $this->excelphp->getActiveSheet()->setCellValue('C' . $row, 'market');
                    $this->excelphp->getActiveSheet()->setCellValue('D' . $row, 'invoice number');
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $row, 'PO number');
                    $this->excelphp->getActiveSheet()->setCellValue('F' . $row, 'invoice date');
                    $this->excelphp->getActiveSheet()->setCellValue('G' . $row, 'date created');
                    $this->excelphp->getActiveSheet()->setCellValue('H' . $row, 'invoice amount');
                    $row++;
                    foreach ($invoiceList as $value) {
                        $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $value['vendor_name']);
                        $this->excelphp->getActiveSheet()->setCellValue('B' . $row, $value['region_name']);
                        $this->excelphp->getActiveSheet()->setCellValue('C' . $row, $value['market_name']);
                        $this->excelphp->getActiveSheet()->setCellValue('D' . $row, $value['invoice_number']);
                        $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $value['po_number']);
                        $this->excelphp->getActiveSheet()->setCellValue('F' . $row, $value['invoice_date']);
                        $this->excelphp->getActiveSheet()->setCellValue('G' . $row, $value['created_time']);
                        $this->excelphp->getActiveSheet()->setCellValue('H' . $row, $value['amount']);
                        $this->excelphp->getActiveSheet()->getStyle('H' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                        $row++;
                    }

                    $filename = 'invoice_list' . time() . '.xlsx'; //save our workbook as this file name
                    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                    header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                    header('Cache-Control: max-age=0');
                    $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel2007');
                    ob_start();
                    $objWriter->save('php://output');
                    $excelOutput = ob_get_clean();
                    $this->zip->add_data($filename, $excelOutput);
                    $this->zip->download('invoice_archive.zip');
                } else {
                    $this->session->set_flashdata('error_message', 'No newly created invoice found2.');
                    redirect('tracker/trackerList');
                }
            } else {
                $this->session->set_flashdata('error_message', 'No newly created invoice found1.');
                redirect('tracker/trackerList');
            }
        } else {
            redirect('tracker/trackerList');
        }
    }

    protected function genaratePdfInvoice($invoice_id = 0) { 
        $invoice_id = xss_clean($invoice_id);
        $invoiceDetails = $this->invoicemodel->getInvoiceDetailsById($invoice_id, TRUE);
        if ($invoiceDetails['invoice_id']) {

            $trackerDetais = $this->trackermodel->getTrackerByInvoice($invoiceDetails['invoice_id']);
            $this->load->library('pdfphp');
            $pdf = $this->pdfphp->load();
            $total = 0;
            $data = '';
            foreach ($trackerDetais as $val) {
                $data .= '<tr>       <td align="center">' . $val['po_number'] . '</td>
            <td align="center">' . $val['line'] . '</td>
            <td align="center">' . $val['description'] . '</td>
            <td align="center">' . $val['qty'] . '</td>
            <td align="right"><span style="float:left">$</span>' . number_format($val['unit_price'], 2) . '</td>
            <td align="right"><span style="float:left">$</span>' . number_format($val['amount'], 2) . '</td>
            <td>&nbsp;</td>
          </tr>';
                $total += $val['amount'];
            }

            $html = '<style>
*{ font-family:Arial, sans-serif;}
@media print
   {
      body {font-family:Arial, sans-serif;}
	  
   }
</style>';
            if($invoiceDetails['vendor_name'] == "Black & Veatch"){
            $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="border-bottom:4px solid #d53872; font-family:\'Arial, sans-serif">
	<tbody>
    	<tr>
        	<td colspan="3" style="height:200px;">&nbsp;</td>
        </tr>
    	<tr>
        	<td align="center" colspan="3" style="padding:5px 0; border-bottom:2px solid #000;">
            	<strong>PO Invoice Cover Page</strong>
            </td>
        </tr>    	
        <tr>
        	<td width="48%" style="text-align:right; padding:5px 0;">
            	<strong>Document Type:</strong>
            </td>
            <td width="4%">&nbsp;</td>
            <td width="48%">
            	<strong>PO Invoice</strong>
            </td>
        </tr> 
        
        <tr>
        	<td width="48%" style="text-align:right; padding:5px 0;">
            	<strong>Organization Name*:</strong>
            </td>
            <td width="4%">&nbsp;</td>
            <td width="48%">
            	<strong>OCI2</strong>
            </td>
        </tr> 
            
        <tr>
        	<td width="48%" style="text-align:right; padding:5px 0;">
            	<strong>Priority:</strong>
            </td>
            <td width="4%">&nbsp;</td>
            <td width="48%">
            	<strong>Normal</strong>
            </td>
        </tr> 
        
    	<tr >
        	<td align="center" colspan="3">
            <img src="'.site_url().'/assets/images/barcodes/barcod.jpg" style="width:50%;">
            </td>
        </tr>
        <tr>
        	<td colspan="3" style="height:200px;">&nbsp;</td>
        </tr>
    </tbody>
</table>';
            $html .= "<pagebreak />";
            }
            $html .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" style="font-family:\'Arial, sans-serif">
  <tbody>
    <tr>
      <td><table width="100%" border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse; font-family:\'Arial, sans-serif;">
        <tbody>
          <tr>
            <td colspan="4" rowspan="8" align="center" valign="middle" bgcolor="#2c2a44"><img src="' . base_url(IMAGE . "logo_amdocs.png") . '" width="384" height="140" alt=""/></td>
            <td  align="right" valign="middle" bgcolor="#eee"><strong>Invoice Number</strong></td>
            <td colspan="2" align="center" bgcolor="#eee">' . $invoiceDetails['invoice_number'] . '</td>
          </tr>
          <tr>
            <td  align="right" valign="middle" bgcolor="#eee"><strong>PO Number</strong></td>
            <td colspan="2" align="center" bgcolor="#eee">' . $invoiceDetails['po_number'] . '</td>
          </tr>
          <tr>
            <td  align="right" valign="middle" bgcolor="#eee"><strong>Invoice Date</strong></td>
            <td colspan="2" align="center" bgcolor="#eee">' . date('m/d/Y', strtotime($invoiceDetails['invoice_date'])) . '</td>
          </tr>
          <tr>
            <td  align="right" valign="middle" bgcolor="#eee"><strong>Page Number</strong></td>
            <td colspan="2" align="center" bgcolor="#eee">1</td>
          </tr>
          <tr>
            <td  rowspan="3" align="center" valign="middle" bgcolor="#eee"><strong>Remit to<br>Address</strong></td>
            <td colspan="2" align="center" bgcolor="#eee">Amdocs USA Inc. </td>
          </tr>
          <tr>
            <td colspan="2" align="center" bgcolor="#eee">1390 Timberlake Manoe Pkwy</td>
          </tr>
          <tr>
            <td colspan="2" align="center" bgcolor="#eee">St. Louis, MO 63017</td>
          </tr>
          <tr>
            <td colspan="3" align="center" valign="middle" bgcolor="#eee">&nbsp;</td>
            </tr>
          <tr>
            <td width="10%" bgcolor="#f5ac32">&nbsp;</td>
            <td colspan="2" align="center" valign="middle" bgcolor="#f5ac32"><strong>Customer ID</strong></td>
            <td align="center" valign="middle" bgcolor="#f5ac32"><strong>Invoice Month</strong></td>
            <td colspan="2" align="center" valign="middle" bgcolor="#f5ac32"><strong>Payment Terms</strong></td>
            <td width="10%" bgcolor="#f5ac32">&nbsp;</td>
          </tr>        
          <tr>
            <td width="10%">&nbsp;</td>
            <td colspan="2" align="center" valign="middle">Amdocs USA Inc</td>
            <td align="center" valign="middle">' . date("F", strtotime($invoiceDetails['invoice_date'])) . '</td>
            <td colspan="2" align="center" valign="middle">NET60</td>
            <td width="10%">&nbsp;</td>
          </tr>        
        
          <tr>
            <td width="10%" bgcolor="#f5ac32">&nbsp;</td>
            <td colspan="2" align="center" valign="middle" bgcolor="#f5ac32"><strong>Market </strong></td>
            <td align="center" valign="middle" bgcolor="#f5ac32"><strong>Region</strong></td>
            <td colspan="2" align="center" valign="middle" bgcolor="#f5ac32"><strong>Due Date</strong></td>
            <td width="10%" bgcolor="#f5ac32">&nbsp;</td>
          </tr>        
          <tr>
            <td width="10%">&nbsp;</td>
            <td colspan="2" align="center" valign="middle">' . $invoiceDetails['market_name'] . '</td>
            <td align="center" valign="middle">' . $invoiceDetails['region_name'] . '</td>
            <td colspan="2" align="center" valign="middle">' . date('m/d/Y', strtotime($invoiceDetails['invoice_due_date'])) . '</td>
            <td width="10%">&nbsp;</td>
          </tr>        
        
        
        
          <tr>
            <td width="10%" align="center" bgcolor="#f5ac32"><strong>PO Number</strong></td>
            <td width="10%" align="center" valign="middle" bgcolor="#f5ac32"><strong>line Number</strong></td>
            <td width="30%" align="center" valign="middle" bgcolor="#f5ac32"><strong>Description</strong></td>
            <td align="center" valign="middle" bgcolor="#f5ac32"><strong>Quantity</strong></td>
            <td align="center" valign="middle" bgcolor="#f5ac32"><strong>Price</strong></td>
            <td align="center" valign="middle" bgcolor="#f5ac32"><strong>Total Amount</strong></td>
            <td width="10%" bgcolor="#f5ac32">&nbsp;</td>
          </tr>
          ' . $data . '
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td align="right">&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="right" style="color:#808080">Subtotal </td>
            <td align="right" style="color:#808080"><span style="float:left">$</span>' . number_format($total, 2) . '</td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td colspan="2" align="right"><strong>Total Amount Due</strong></td>
            <td align="right"><strong><span style="float:left">$</span>' . number_format($total, 2) . '</strong></td>
            <td>&nbsp;</td>
          </tr>
          <tr>
            <td colspan="7">&nbsp;</td>
            </tr>';
            
          if($invoiceDetails['vendor_name'] == "Black & Veatch"){
        $html .=    '<tr><td colspan="3" bgcolor="#ddd" style="color:#808080;border-right:0px;">
              If you have any question please contact:
              Saad Khan  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; (678)896 6118<br>
              Finance Manager &nbsp; &nbsp; saad.khan@amdocs.com
              </td>
              <td colspan="4" bgcolor="#ddd" style="color:#808080; border-left:0px" valign="top">
              No liens exist, in lieu of an accompanying notarized lien waiver.
              </td>
            </tr>';
                }else{
                    $html .=    '<tr><td colspan="7" bgcolor="#ddd" style="color:#808080;border-right:0px;">
                    If you have any question please contact:
                    Saad Khan  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; (678)896 6118<br>
                    Finance Manager &nbsp; &nbsp; saad.khan@amdocs.com
                    </td><td colspan="4" bgcolor="#ddd" style="color:#808080; border-left:0px" valign="top"></td>
                  </tr>';
                }
         $html .=    '</tbody>
      </table>
      <table width="100%" border="0" style=" border-top:4px solid #d53872;" cellspacing="0" cellpadding="10">
    <tr>
      <td align="center">&nbsp;</td>
      <td colspan="4" align="left">Leavel 1 . Confidential</td>      
      <td colspan="2" align="center" valign="middle">Page 1 of 1</td>
    </tr>
</table>
    </td>
    </tr>
  </tbody>
</table>';

            $pdf->WriteHTML($html);
            $filename = $invoiceDetails['vendor_name'] . ' Invoice ' . $invoiceDetails['invoice_number'] . ' PO ' . $invoiceDetails['po_number'] . '.pdf';
            return array($filename, $pdf->Output($filename, 'S'));
        }
    }

}
