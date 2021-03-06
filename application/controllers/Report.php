<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller {

    public function __construct() {
        parent::__construct();
        #Load Model
        $this->load->model(array('crawlmodel', 'trackermodel', 'mastermodel', 'batchlogs', 'invoicemodel'));
    }

    public function index() {
        redirect('report/customer_report');
    }

    public function customer_report() {
        isUserLogin(TRUE);
        if ($this->uri->segment(3) == 'reset') {
            $this->session->unset_userdata('customer_report_search');
            redirect('report/customer_report');
        }

        if ($this->input->post('search')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('vendor_id', 'Customer', 'trim|xss_clean|required|is_natural_no_zero');
            $this->form_validation->set_rules('fromdate', 'Fromdate', 'trim|xss_clean');
            $this->form_validation->set_rules('todate', 'Todate', 'trim|xss_clean');
            if ($this->form_validation->run()) {
                $report_search['todate'] = $this->input->post('todate');
                $report_search['fromdate'] = $this->input->post('fromdate');
                $report_search['vendor_id'] = $this->input->post('vendor_id');
                $report_search['reset'] = TRUE;
                $this->session->set_userdata('customer_report_search', $report_search);
            }
        } elseif (!$this->session->userdata('customer_report_search')) {
            $report_search['vendor_id'] = '';
            $report_search['todate'] = '';
            $report_search['fromdate'] = '';
            $this->session->set_userdata('customer_report_search', $report_search);
        }
        $report_search = $this->session->userdata('customer_report_search');

        if (isset($report_search['vendor_id']) && !empty($report_search['vendor_id'])) {
            $data['reportList'] = $this->trackermodel->getTrackerByVendor($report_search['vendor_id'], $report_search['fromdate'], $report_search['todate']);
//            echo $this->db->last_query();
        }
        $data['vendors'] = $this->mastermodel->getAllVendor();

        $data['menu_id'] = 'report';
        $data['sub_menu_id'] = 'customer_report';
        $data['title'] = 'order summary report';
        $data['page_heading'] = 'order summary report';
        $data['view_file'] = 'report/report_layout';
        $data['sub_view_file'] = 'customer_report_view';
        view($data);
    }

    public function customer_report_export() {
        if (isUserLogin()) {
            $report_search = $this->session->userdata('customer_report_search');
            $vendors = $this->mastermodel->getAllVendor();
            $this->load->library('excelphp');
            if (isset($report_search['vendor_id']) && !empty($report_search['vendor_id'])) {
                $reportList = $this->trackermodel->getTrackerByVendor($report_search['vendor_id'], $report_search['fromdate'], $report_search['todate']);

                $borderArray = array(
                    'borders' => array(
                        'right' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => 'CCCCCCCC'),
                        ),
                    ),
                );
                $this->excelphp->setActiveSheetIndex(0);
                $this->excelphp->getActiveSheet()->setTitle('Order Summary  Report');
                $this->excelphp->getActiveSheet()->getColumnDimension('A')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('B')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('C')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('D')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('E')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('F')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('G')->setWidth(15);
                $this->excelphp->getActiveSheet()->getColumnDimension('H')->setWidth(15);
                $row = 2;
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'customer')->getStyle('A' . $row)->getFont()->setSize(12)->setBold(true);
                $this->excelphp->getActiveSheet()->setCellValue('B' . $row, 'SAI');
                $this->excelphp->getActiveSheet()->setCellValue('D' . $row, 'from date')->getStyle('D' . $row)->getFont()->setSize(12)->setBold(true);
                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $report_search['fromdate']);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $row, 'to date')->getStyle('G' . $row)->getFont()->setSize(12)->setBold(true);
                $this->excelphp->getActiveSheet()->setCellValue('H' . $row, $report_search['todate']);
                $row++;
                $row++;
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
                $this->excelphp->getActiveSheet()->getStyle('A' . $row . ':H' . $row)->getFont()->setSize(12)->setBold(true);
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'region')->mergeCells('A' . $row . ':B' . $row);
                $this->excelphp->getActiveSheet()->setCellValue('C' . $row, 'market')->mergeCells('C' . $row . ':D' . $row);
                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, '#PO\'s')->mergeCells('E' . $row . ':F' . $row);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $row, 'total $')->mergeCells('G' . $row . ':H' . $row);
                $this->excelphp->getActiveSheet()->getStyle('B' . $row)->applyFromArray($borderArray);
                $this->excelphp->getActiveSheet()->getStyle('D' . $row)->applyFromArray($borderArray);
                $this->excelphp->getActiveSheet()->getStyle('F' . $row)->applyFromArray($borderArray);
                $this->excelphp->getActiveSheet()->getStyle('H' . $row)->applyFromArray($borderArray);
                $row++;
                $oldregion = 0;
                $startRow = $row;
                $regNameArr = array();
                $custNameArr = array();
                $marketNameArr = array();
                foreach ($reportList as $val) {                    
                    if ($oldregion != $val['region']) {
                        $regNameArr[] = $val['region_name'];
                        $custNameArr[] = $val['vendor_name'];
                        $marketNameArr[] = $val['market_name'];
                        $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $val['region_name'])->mergeCells('A' . $row . ':H' . $row);
                        $this->excelphp->getActiveSheet()
                                ->getStyle('A' . $row . ':H' . $row)
                                ->getFill()
                                ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                                ->getStartColor()
                                ->setARGB('CCCCCCCC');
                        $row++;
                        $oldregion = $val['region'];
                    }
                    $this->excelphp->getActiveSheet()->setCellValue('C' . $row, $val['market_name'])->mergeCells('C' . $row . ':D' . $row);
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $val['total_count'])->mergeCells('E' . $row . ':F' . $row);
                    $this->excelphp->getActiveSheet()->setCellValue('G' . $row, $val['amount'])->mergeCells('G' . $row . ':H' . $row);
                    $row++;
                }
                $endrow = $row;
                $row++;
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, 'Total')->mergeCells('A' . $row . ':D' . $row);
                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, '=SUM(E' . $startRow . ':E' . $endrow . ')')->mergeCells('E' . $row . ':F' . $row);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $row, '=SUM(G' . $startRow . ':G' . $endrow . ')')->mergeCells('G' . $row . ':H' . $row);
                $this->excelphp->getActiveSheet()->getStyle('G' . $startRow . ':G' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                
                
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
                
                $filename = strtolower($filekey_cust.'_'.$filekey_reg. '_' . $filekey_market . '_').'invoice_list' . time() . '.xlsx'; //save our workbook as this file name
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
                header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel2007');

                $objWriter->save('php://output');
            } else {
                $this->session->set_flashdata('error_message', 'please select a customer');
                redirect('report/customer_report');
            }
        } else {
            redirect('report/customer_report');
        }
    }

    public function invoice_report() {
        isUserLogin(TRUE);
        if ($this->uri->segment(3) == 'reset') {
            $this->session->unset_userdata('invoice_report_search');
            redirect('report/invoice_report');
        }
        if (isset($_POST['vendorid'])) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('vendorid', 'Vendor', 'trim|xss_clean|required');
            $this->form_validation->set_rules('regionid', 'Region', 'trim|xss_clean|required');
            $this->form_validation->set_rules('marketid', 'Market', 'trim|xss_clean|required');
            $this->form_validation->set_rules('invoicefromdate', 'From Date', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicetodate', 'To Date', 'trim|xss_clean');
            $this->form_validation->set_rules('orderfield', 'orderfield', 'trim|xss_clean|in_list[vendor_id,region_id,market_id,invoice_number,invoice_date,created_time,po_number,amount]');
            $this->form_validation->set_rules('ordertype', 'ordertype', 'trim|xss_clean|in_list[asc,desc]');
            if ($this->form_validation->run()) {
                $report_search['invoicetodate'] = $this->input->post('invoicetodate');
                $report_search['invoicefromdate'] = $this->input->post('invoicefromdate');
                $report_search['todate'] = '';
                $report_search['fromdate'] = '';
                $report_search['vendorid'] = $this->input->post('vendorid');
                $report_search['regionid'] = $this->input->post('regionid');
                $report_search['marketid'] = $this->input->post('marketid');
                $report_search['ponumber'] = '';
                $report_search['invoicenumber'] = '';
                $report_search['orderfield'] = $this->input->post('orderfield');
                $report_search['ordertype'] = $this->input->post('ordertype');
                $report_search['reset'] = TRUE;
                $this->session->set_userdata('invoice_report_search', $report_search);
            }
        } else if (!$this->session->userdata('invoice_report_search')) {
            $report_search['todate'] = '';
            $report_search['fromdate'] = '';
            $report_search['invoicefromdate'] = '';
            $report_search['invoicetodate'] = '';

            $report_search['vendorid'] = '';
            $report_search['regionid'] = 'All';
            $report_search['marketid'] = 'All';
            $report_search['ponumber'] = '';
            $report_search['invoicenumber'] = '';
            $report_search['orderfield'] = 'po_number';
            $report_search['ordertype'] = 'asc';
            $this->session->set_userdata('invoice_report_search', $report_search);
        }
        $report_search = $this->session->userdata('invoice_report_search');

        $this->load->library("pagination");
        $config['base_url'] = base_url("report/invoice_report");
        $config['total_rows'] = $this->invoicemodel->getInvoiceCount($report_search['vendorid'], $report_search['regionid'], $report_search['marketid'], $report_search['ponumber'], $report_search['invoicenumber'], $report_search['fromdate'], $report_search['todate'], $report_search['invoicefromdate'], $report_search['invoicetodate'], TRUE);
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
        if (isset($report_search['vendorid']) && !empty($report_search['vendorid'])) {
            $data['reportList'] = $this->invoicemodel->getAllInvoice($config["per_page"], ($page - 1) * $config['per_page'], $report_search['vendorid'], $report_search['regionid'], $report_search['marketid'], $report_search['ponumber'], $report_search['invoicenumber'], $report_search['fromdate'], $report_search['todate'], $report_search['invoicefromdate'], $report_search['invoicetodate'], $report_search['orderfield'], $report_search['ordertype'], TRUE);
        }
        $data['vendors'] = $this->mastermodel->getAllVendor();
        $data['regions'] = $this->mastermodel->getAllRegion();
        $data['markets'] = $this->mastermodel->getAllMarket();
        $data['title'] = 'invoice report';
        $data['menu_id'] = 'report';
        $data['sub_menu_id'] = 'invoice_report';
        $data['page_heading'] = 'invoice summery report';
        $data['view_file'] = 'report/report_layout';
        $data['sub_view_file'] = 'invoice_report_view';
        view($data);
    }

    public function invoice_report_export($invoice_id = 0) {
        if (isUserLogin()) {
            $report_search = $this->session->userdata('invoice_report_search');
            $this->load->library('excelphp');
            if (isset($report_search['vendorid']) && !empty($report_search['vendorid'])) {
                $invoiceList = $this->invoicemodel->getAllInvoice(NULL, NULL, $report_search['vendorid'], $report_search['regionid'], $report_search['marketid'], NULL, NULL, $report_search['fromdate'], $report_search['todate'], NULL, NULL, $report_search['orderfield'], $report_search['ordertype'], TRUE);
                $vendor = $this->mastermodel->getVendorById($report_search['vendorid']);
                $this->excelphp->setActiveSheetIndex(0);
                $this->excelphp->getActiveSheet()->setTitle('Invoice Report');

                $row = 1;
                $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $vendor['name'] . ' invoice summery')->mergeCells('A1:H1');
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );
                $this->excelphp->getActiveSheet()->getStyle("A1:H1")->applyFromArray($style);
                $row++;
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
                $this->excelphp->getActiveSheet()->setCellValue('D' . $row, 'date created');
                $this->excelphp->getActiveSheet()->setCellValue('E' . $row, 'invoice number');
                $this->excelphp->getActiveSheet()->setCellValue('F' . $row, 'PO number');
                $this->excelphp->getActiveSheet()->setCellValue('G' . $row, 'invoice date');
                $this->excelphp->getActiveSheet()->setCellValue('H' . $row, 'invoice amount');
                $row++;
                $regNameArr = array();
                $custNameArr = array();
                $marketNameArr = array();
                foreach ($invoiceList as $value) {
                    $regNameArr[] = $value['region_name'];
                    $custNameArr[] = $value['vendor_name'];
                    $marketNameArr[] = $value['market_name'];
                    $this->excelphp->getActiveSheet()->setCellValue('A' . $row, $value['vendor_name']);
                    $this->excelphp->getActiveSheet()->setCellValue('B' . $row, $value['region_name']);
                    $this->excelphp->getActiveSheet()->setCellValue('C' . $row, $value['market_name']);
                    $this->excelphp->getActiveSheet()->setCellValue('D' . $row, date('Y-m-d H:i:s', strtotime($value['created_time'])));
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $value['invoice_number']);
                    $this->excelphp->getActiveSheet()->setCellValue('F' . $row, $value['po_number']);
                    $this->excelphp->getActiveSheet()->setCellValue('G' . $row, date('Y-m-d', strtotime($value['invoice_date'])));
                    $this->excelphp->getActiveSheet()->setCellValue('H' . $row, $value['amount']);
                    $this->excelphp->getActiveSheet()->getStyle('H' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                    $row++;
                }
                $borderArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

                $this->excelphp->getActiveSheet()->getStyle('A2:H'.($row - 1))->applyFromArray($borderArray);
                
                $this->excelphp->getActiveSheet()->setCellValue('H' . $row, '=sum(H2:H' . ($row - 1) . ')')->getStyle('H' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $styleArray = array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                );               
                
                $this->excelphp->getActiveSheet()->getStyle('H' . $row)->applyFromArray($styleArray);
                
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

                $filename = strtolower($filekey_cust.' '.$filekey_reg.' '. $filekey_market).' invoice summery report' . time() . '.xlsx'; //save our workbook as this file name
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); //mime type
                header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel2007');

                $objWriter->save('php://output');
            } else {
                $this->session->set_flashdata('error_message', 'please select a customer');
                redirect('report/invoice_report');
            }
        } else {
            redirect('report/invoice_report');
        }
    }

}
