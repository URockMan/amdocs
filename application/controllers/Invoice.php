<?php

class Invoice extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model(array('invoicemodel', 'mastermodel', 'trackermodel'));
    }

    public function index() {
        redirect('invoice/invoiceList');
    }

    public function invoiceList() {
         
        isUserLogin(TRUE);
        if ($this->uri->segment(3) == 'reset') {
            $this->session->unset_userdata('invoice_search');
            redirect('invoice/invoiceList');
        }
        if ($this->input->post('vendorid')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('vendorid', 'Vendor', 'trim|xss_clean|required');
            $this->form_validation->set_rules('regionid', 'Region', 'trim|xss_clean|required');
            $this->form_validation->set_rules('ponumber', 'PO Number', 'trim|xss_clean');
            $this->form_validation->set_rules('marketid', 'Market', 'trim|xss_clean|required');
            $this->form_validation->set_rules('invoicenumber', 'Invoice Number', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicefromdate', 'invoice from date', 'trim|xss_clean');
            $this->form_validation->set_rules('invoicetodate', 'invoice to date', 'trim|xss_clean');
            $this->form_validation->set_rules('fromdate', 'Date created from', 'trim|xss_clean');
            $this->form_validation->set_rules('todate', 'Date created to', 'trim|xss_clean');
            $this->form_validation->set_rules('orderfield', 'orderfield', 'trim|xss_clean|in_list[vendor_id,region_id,market_id,invoice_number,invoice_date,created_time,po_number,amount]');
            $this->form_validation->set_rules('ordertype', 'ordertype', 'trim|xss_clean|in_list[asc,desc]');
            if ($this->form_validation->run()) {
                $report_search['todate'] = $this->input->post('todate');
                $report_search['fromdate'] = $this->input->post('fromdate');
                $report_search['invoicefromdate'] = $this->input->post('invoicefromdate');
                $report_search['invoicetodate'] = $this->input->post('invoicetodate');

                $report_search['vendorid'] = $this->input->post('vendorid');
                $report_search['regionid'] = $this->input->post('regionid');
                $report_search['marketid'] = $this->input->post('marketid');
                $report_search['ponumber'] = $this->input->post('ponumber');
                $report_search['invoicenumber'] = $this->input->post('invoicenumber');
                $report_search['orderfield'] = $this->input->post('orderfield');
                $report_search['ordertype'] = $this->input->post('ordertype');
                $report_search['reset'] = TRUE;
                $this->session->set_userdata('invoice_search', $report_search);
            }
        } else if (!$this->session->userdata('invoice_search')) {
            $report_search['todate'] = '';
            $report_search['fromdate'] = '';
            $report_search['invoicefromdate'] = '';
            $report_search['invoicetodate'] = '';

            $report_search['vendorid'] = 'All';
            $report_search['regionid'] = 'All';
            $report_search['marketid'] = 'All';
            $report_search['ponumber'] = '';
            $report_search['invoicenumber'] = '';
            $report_search['orderfield'] = 'po_number';
            $report_search['ordertype'] = 'asc';
            $this->session->set_userdata('invoice_search', $report_search);
        }
        $report_search = $this->session->userdata('invoice_search');

        $this->load->library("pagination");
        $config['base_url'] = base_url("invoice/invoiceList");
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

        $data['invoiceList'] = $this->invoicemodel->getAllInvoice($config["per_page"], ($page - 1) * $config['per_page'], $report_search['vendorid'], $report_search['regionid'], $report_search['marketid'], $report_search['ponumber'], $report_search['invoicenumber'], $report_search['fromdate'], $report_search['todate'], $report_search['invoicefromdate'], $report_search['invoicetodate'], $report_search['orderfield'], $report_search['ordertype'], TRUE);
        $data['vendors'] = $this->mastermodel->getAllVendor();
        $data['regions'] = $this->mastermodel->getAllRegion();
        $data['markets'] = $this->mastermodel->getAllMarket();
        $data['title'] = 'Invoices';
        $data['menu_id'] = 'invoicelist';
        $data['page_heading'] = 'Invoices';

        $data['view_file'] = 'invoice/invoice_list_view';
        view($data);
    }

    public function cancelInvoice($invoice_id = 0) {
        if (isUserLogin()) {
            $invoice_id = xss_clean($invoice_id);
            $invoiceDetails = $this->invoicemodel->getInvoiceDetailsById($invoice_id, TRUE);
            if ($invoiceDetails['invoice_id']) {
                //get tracker details by po order
                $invoiceStatus = $this->invoicemodel->getInvoiceStatus();
                $trackerList = $this->trackermodel->getTrackerByInvoice($invoiceDetails['invoice_id']);
                if (count($trackerList) == 0) {
                    $this->session->set_flashdata('error_message', 'some error occured please try later.');
                    redirect('invoice/invoiceList');
                }
                $error = FALSE;
                $this->db->trans_start();
                $row = $this->invoicemodel->changeInvoiceStatus($invoiceDetails['invoice_id'], $invoiceStatus['inactive']);
                if ($row == 0) {
                    $error = TRUE;
                } else {
                    $trackerarray = array();
                    foreach ($trackerList as $tracker) {
                        $trackerarray[] = $tracker['tracker_id'];
                    }
                    $row2 = $this->trackermodel->chancelInvoice($trackerarray);
                    if ($row2 != count($trackerarray)) {
                        $error = TRUE;
                    }
                }
                if ($this->db->trans_status() === FALSE || $error) {
                    $this->db->trans_rollback();
                    $this->session->set_flashdata('error_message', 'some error occured please try later.');
                    redirect('invoice/invoiceList');
                } else {
                    $this->db->trans_commit();
                    $this->session->set_flashdata('success_message', 'invoice cancel successfully.');
                    redirect('invoice/invoiceList');
                }
            } else {
                $this->session->set_flashdata('error_message', 'No Invoice found with this key!');
                redirect('invoice/invoiceList');
            }
        } else {
            redirect('invoice/invoiceList');
        }
    }

    public function exportExcelInvoice($invoice_id = 0) {
        if (isUserLogin()) {
            $report_search = $this->session->userdata('invoice_search');
            $this->load->library('excelphp');

            $invoiceList = $this->invoicemodel->getAllInvoice(NULL, NULL, $report_search['vendorid'], $report_search['regionid'], $report_search['marketid'], $report_search['ponumber'], $report_search['invoicenumber'], $report_search['fromdate'], $report_search['todate'], $report_search['invoicefromdate'], $report_search['invoicetodate'], $report_search['orderfield'], $report_search['ordertype'], TRUE);

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

            $objWriter->save('php://output');
        } else {
            redirect('invoice/invoiceList');
        }
    }

    public function inportFile() {
        if (isUserLogin()) {
            $this->load->library('excelphp');
            $this->load->library('upload');
            $config['upload_path'] = UPLOAD;
            $config['allowed_types'] = 'xls|xlsx';
            $config['overwrite'] = FALSE;
            $config['file_ext_tolower'] = TRUE;
            $config['encrypt_name'] = TRUE;
            $config['remove_spaces'] = TRUE;
            $config['detect_mime'] = TRUE;
            $config['mod_mime_fix'] = TRUE;
            $config['max_size'] = 5120;
            $this->upload->initialize($config);
            $file = TRUE;
            $invoiceFileName = '';
            if ($_POST['upload']) {
                if (isset($_FILES['invoice_attachment']) && is_uploaded_file($_FILES['invoice_attachment']['tmp_name'])) {
                    $config['upload_path'] = UPLOAD;
                    $this->upload->initialize($config, FALSE);
                    if (!$this->upload->do_upload('invoice_attachment')) {
                        $this->session->set_flashdata('error_message', $this->upload->display_errors());
                        $file = FALSE;
                        redirect('invoice/invoiceList');
                    } else {
                        $invoiceFile = $this->upload->data();
                        $invoiceFileName = $invoiceFile['file_name'];

                        if ($invoiceFileName && $file) {
                            $inputFileName = UPLOAD . '/' . $invoiceFileName;
                            try {
                                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                                $objPHPExcel = $objReader->load($inputFileName);
                            } catch (Exception $e) {
                                die('Error loading file "' . pathinfo($inputFileName, PATHINFO_BASENAME) . '": ' . $e->getMessage());
                            }

                            $sheet = $objPHPExcel->getSheet(0);
                            $highestRow = $sheet->getHighestRow();
                            $highestColumn = $sheet->getHighestColumn();


                            for ($row = 2; $row <= $highestRow; $row++) {

                                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row, NULL, TRUE, FALSE);
                                if (strtolower($rowData[0][13]) == 'ready to invoice') {
                                    $vendor = $this->mastermodel->checkVendorExists($rowData[0][2]);
                                    if ($vendor['id']) {
                                        $this->trackermodel->changetoRIV($vendor['id'], $rowData[0][3], $rowData[0][4]);
                                    } else {
                                        //error
                                    }
                                }
                            }
                            $this->session->set_flashdata('success_message', 'updated');
                            redirect('invoice/invoiceList');
                        }
                    }
                } else {
                    //error
                    $this->session->set_flashdata('error_message', 'error');
                    redirect('invoice/invoiceList');
                }
            }
        } else {
            redirect('invoice/invoiceList');
        }
    }

    public function genarateEcel($invoice_id = 0) {
        if (isUserLogin()) {
            $invoice_id = xss_clean($invoice_id);
            $invoiceDetails = $this->invoicemodel->getInvoiceDetailsById($invoice_id, TRUE);
            if ($invoiceDetails['invoice_id']) {

                $trackerDetais = $this->trackermodel->getTrackerByInvoice($invoiceDetails['invoice_id']);
//      print_r($trackerDetais); exit();

                $this->load->library('excelphp');
                $this->excelphp->setActiveSheetIndex(0);
                $this->excelphp->getActiveSheet()->setTitle('Tracker List Report');

                $this->excelphp->getActiveSheet()->getColumnDimension('A')->setWidth(11);
                $this->excelphp->getActiveSheet()->getColumnDimension('B')->setWidth(14);
                $this->excelphp->getActiveSheet()->getColumnDimension('C')->setWidth(14);
                $this->excelphp->getActiveSheet()->getColumnDimension('D')->setWidth(42.88);
                $this->excelphp->getActiveSheet()->getColumnDimension('E')->setWidth(16);
                $this->excelphp->getActiveSheet()->getColumnDimension('F')->setWidth(16);
                $this->excelphp->getActiveSheet()->getColumnDimension('G')->setWidth(16);
                $this->excelphp->getActiveSheet()->getColumnDimension('H')->setWidth(16);
                $this->excelphp->getActiveSheet()->getRowDimension('1')->setRowHeight(17.75);

                $this->excelphp->getActiveSheet()
                        ->getStyle('A:U')
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFFFFFFF');
                $strtinvoice = 0;
                /*0000000000000000000000000000000000000000000000000*/
                if($invoiceDetails['vendor_name'] == "Black & Veatch"){
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    ),
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK,
                            'color' => array('argb' => '00000000'),
                        ),
                    )
                );
                $this->excelphp->getActiveSheet()->setCellValue('B2', 'PO Invoice Cover Page')->mergeCells('B2:H2');
                $this->excelphp->getActiveSheet()->getStyle("B2:H2")->applyFromArray($style);
                
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    ),
                );
                $this->excelphp->getActiveSheet()->setCellValue('D3', 'Document Type:');
                $this->excelphp->getActiveSheet()->getStyle("D3:D3")->applyFromArray($style);
                $this->excelphp->getActiveSheet()->setCellValue('E3', 'PO Invoice');
                
                $this->excelphp->getActiveSheet()->setCellValue('D4', 'Organization Name*:');
                $this->excelphp->getActiveSheet()->getStyle("D4:D4")->applyFromArray($style);
                $this->excelphp->getActiveSheet()->setCellValue('E4', 'OCI2');
                
                $this->excelphp->getActiveSheet()->setCellValue('D5', 'Priority:');
                $this->excelphp->getActiveSheet()->getStyle("D5:D5")->applyFromArray($style);
                $this->excelphp->getActiveSheet()->setCellValue('E5', 'Normal');
                
                
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('Barcode');
                $objDrawing->setDescription('Barcode');
                $objDrawing->setPath(FCPATH . 'assets/images/barcodes/barcod.jpg');
                $objDrawing->setCoordinates('D6');
                $objDrawing->setWidth(630);
//        $objDrawing->setHeight(140);
                $objDrawing->setWorksheet($this->excelphp->getActiveSheet());
                $offsetX = 630 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);
                
                $styleArray = array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK,
                            'color' => array('argb' => 'f2f24db0'),
                        ),
                    ),
                );

                $this->excelphp->getActiveSheet()->getStyle('B19:H19')->applyFromArray($styleArray);
                
                
                
                $strtinvoice = 20;
                }
                /*00000000000000000000000000000000000000000000000000*/
                
                for($iloop=1;$iloop<=15;$iloop++){
                    ${'_' . $iloop} = $strtinvoice + $iloop;
                    
                }
                
                $objDrawing = new PHPExcel_Worksheet_Drawing();
                $objDrawing->setName('Logo');
                $objDrawing->setDescription('Logo');
                $objDrawing->setPath(FCPATH . 'assets/images/logo_amdocs.png');
                $objDrawing->setCoordinates('C'.$_4);
                $objDrawing->setWidth(384);
//        $objDrawing->setHeight(140);
                $objDrawing->setWorksheet($this->excelphp->getActiveSheet());
                $offsetX = 384 - $objDrawing->getWidth();
                $objDrawing->setOffsetX($offsetX);



                $this->excelphp->getActiveSheet()
                        ->getStyle('B'.$_3.':E'.$_10)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('222c2a44');


//            $this->excelphp->getActiveSheet()->mergeCells('B3:E10');
                $this->excelphp->getActiveSheet()
                        ->getStyle('F'.$_3.':H'.$_10)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('EFEFF0F1');


                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 12,
                        'name' => 'Calibri'
                ));



                $this->excelphp->getActiveSheet()->getStyle('F'.$_3.':F'.$_7)->applyFromArray($styleArray);
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_3, 'Invoice Number');
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_4, 'PO Number');
                $borderArray = array(
                    'borders' => array(
                        'allborders' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                );

                $this->excelphp->getActiveSheet()->getStyle('F'.$_3.':H'.$_9)->applyFromArray($borderArray);
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_5, 'Invoice Date');
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_6, 'Page Number');
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_7, 'Remit to Address')->mergeCells('F'.$_7.':F'.$_9);
                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                    )
                );

                $this->excelphp->getActiveSheet()->getStyle('F'.$_7)->applyFromArray($style);
                $this->excelphp->getActiveSheet()->getStyle('F'.$_7)->getAlignment()->setWrapText(true);
                $this->excelphp->getActiveSheet()->setCellValue('G'.$_3, $invoiceDetails['invoice_number'])->mergeCells('G'.$_3.':H'.$_3);
                $this->excelphp->getActiveSheet()->setCellValue('G'.$_4, $invoiceDetails['po_number'])->mergeCells('G'.$_4.':H'.$_4);
                $this->excelphp->getActiveSheet()->setCellValue('G'.$_5, $invoiceDetails['invoice_date'])->mergeCells('G'.$_5.':H'.$_5);

                $this->excelphp->getActiveSheet()->setCellValue('G'.$_6, '1')->mergeCells('G'.$_6.':H'.$_6);

                $this->excelphp->getActiveSheet()->setCellValue('G'.$_7, 'Amdocs USA Inc')->mergeCells('G'.$_7.':H'.$_7);
                $this->excelphp->getActiveSheet()->setCellValue('G'.$_8, '1390 Timberlake')->mergeCells('G'.$_8.':H'.$_8);
                $this->excelphp->getActiveSheet()->setCellValue('G'.$_9, 'St. Louis, MO 63017')->mergeCells('G'.$_9.':H'.$_9);



                $this->excelphp->getActiveSheet()
                        ->getStyle('B'.$_11.':H'.$_11)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('fff5ac32');
                $this->excelphp->getActiveSheet()->getStyle('B'.$_11.':H'.$_11)->applyFromArray($styleArray);
                $this->excelphp->getActiveSheet()
                        ->getStyle('B'.$_11.':H'.$_12)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excelphp->getActiveSheet()->setCellValue('C'.$_11, 'Customer ID')->mergeCells('C'.$_11.':D'.$_11);
                $this->excelphp->getActiveSheet()->setCellValue('E'.$_11, 'Invoice Month');
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_11, 'Payment Terms')->mergeCells('F'.$_11.':G'.$_11);
                $this->excelphp->getActiveSheet()->setCellValue('C'.$_12, 'Amdocs USA Inc')->mergeCells('C'.$_12.':D'.$_12);
                $this->excelphp->getActiveSheet()->setCellValue('E'.$_12, date("F", strtotime($invoiceDetails['invoice_date'])));
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_12, 'NET60')->mergeCells('F'.$_12.':G'.$_12);

                $this->excelphp->getActiveSheet()
                        ->getStyle('B'.$_13.':H'.$_13)
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('fff5ac32');
                $this->excelphp->getActiveSheet()->getStyle('B'.$_13.':H'.$_13)->applyFromArray($styleArray);
                $this->excelphp->getActiveSheet()
                        ->getStyle('B'.$_13.':H'.$_14)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excelphp->getActiveSheet()->setCellValue('C'.$_13, 'Market')->mergeCells('C'.$_13.':D'.$_13);
                $this->excelphp->getActiveSheet()->setCellValue('E'.$_13, 'Region');
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_13, 'Due Date')->mergeCells('F'.$_13.':G'.$_13);
                $this->excelphp->getActiveSheet()->setCellValue('C'.$_14, $invoiceDetails['market_name'])->mergeCells('C'.$_14.':D'.$_14);
                $this->excelphp->getActiveSheet()->setCellValue('E'.$_14, $invoiceDetails['region_name']);
                $this->excelphp->getActiveSheet()->setCellValue('F'.$_14, $invoiceDetails['invoice_due_date'])->mergeCells('F'.$_14.':G'.$_14);



                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                    )
                );

                $this->excelphp->getActiveSheet()->getStyle("F$_3:F$_9")->applyFromArray($style);

                $style = array(
                    'alignment' => array(
                        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    )
                );

                $this->excelphp->getActiveSheet()->getStyle("G$_3:H$_9")->applyFromArray($style);

                $this->excelphp->getActiveSheet()->getStyle("F$_10:H$_10")->applyFromArray(array(
                    'borders' => array(
                        'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                ));

                $this->excelphp->getActiveSheet()
                        ->getStyle("B$_15:H$_15")
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('fff5ac32');
                $this->excelphp->getActiveSheet()->getStyle("B$_15:H$_15")->applyFromArray($styleArray);
                $this->excelphp->getActiveSheet()
                        ->getStyle("B$_15:H$_15")
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                $this->excelphp->getActiveSheet()->setCellValue("B$_15", 'PO Number');
                $this->excelphp->getActiveSheet()->setCellValue("C$_15", 'Line Number');
                $this->excelphp->getActiveSheet()->setCellValue("D$_15", 'Description');
                $this->excelphp->getActiveSheet()->setCellValue("E$_15", 'Quantity');
                $this->excelphp->getActiveSheet()->setCellValue("F$_15", 'Price');
                $this->excelphp->getActiveSheet()->setCellValue("G$_15", 'Total Amount');
//
//
//
//            //loop
                $firstrow = $row = $_15 + 1;
                foreach ($trackerDetais as $val) {
                    $this->excelphp->getActiveSheet()->setCellValue('B' . $row, $val['po_number']);
                    $this->excelphp->getActiveSheet()->setCellValue('C' . $row, $val['line']);
                    $this->excelphp->getActiveSheet()->setCellValue('D' . $row, $val['description']);
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $row, $val['qty']);
                    $this->excelphp->getActiveSheet()->setCellValue('F' . $row, $val['unit_price']);
                    $this->excelphp->getActiveSheet()->getStyle('F' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                    $this->excelphp->getActiveSheet()->setCellValue('G' . $row, $val['amount']);
                    $this->excelphp->getActiveSheet()->getStyle('G' . $row)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                    $row++;
                }
//

                $this->excelphp->getActiveSheet()
                        ->getStyle('B' . $firstrow . ':H' . $row)
                        ->getAlignment()
                        ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

                $currow = $row + 3;

//
                $this->excelphp->getActiveSheet()->setCellValue('F' . $currow, 'Subtotal');
                $this->excelphp->getActiveSheet()->getStyle('F' . $currow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $currow, '=SUM(G' . $firstrow . ':G' . $row . ')');
                $this->excelphp->getActiveSheet()->getStyle('G' . $currow)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $this->excelphp->getActiveSheet()->getStyle('F' . $currow)->applyFromArray(array(
                    'font' => array(
                        'bold' => true,
                        'color' => array('argb' => '80808080'),
                )));
//
                $currow++;
                $this->excelphp->getActiveSheet()->setCellValue('F' . $currow, 'Total Amount Due');
                $this->excelphp->getActiveSheet()->getStyle('F' . $currow)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                $this->excelphp->getActiveSheet()->setCellValue('G' . $currow, '=SUM(G' . $firstrow . ':G' . $row . ')');
                $this->excelphp->getActiveSheet()->getStyle('G' . $currow)->getNumberFormat()->setFormatCode('_("$"* #,##0.00_);_("$"* \(#,##0.00\);_("$"* "-"??_);_(@_)');
                $this->excelphp->getActiveSheet()->getStyle('F' . $currow)->getFont()->setBold(true);

                $currow++;
                $this->excelphp->getActiveSheet()->getStyle("B$_11:H" . $currow)->applyFromArray($borderArray);




                $styleArray = array(
                    'font' => array(
                        'size' => 12,
                        'name' => 'Calibri'
                ));



                $this->excelphp->getActiveSheet()->getStyle("B$_3:H" . $currow)->applyFromArray($styleArray);
//
//            $this->excelphp->getActiveSheet()->getStyle('G' . $currow . ':I' . $currow)->applyFromArray($styleArray);
//            $currow++;
//
//
//
//            $this->excelphp->getActiveSheet()->mergeCells('A' . $currow . ':J' . $currow);
                $currow++;
//
                $this->excelphp->getActiveSheet()
                        ->getStyle('B' . $currow . ':H' . ($currow + 2))
                        ->getFill()
                        ->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()
                        ->setARGB('EFEFF0F1');

                $styleArray = array(
                    'font' => array(
                        'bold' => true,
                        'size' => 10,
                        'name' => 'Calibri',
                        'color' => array('argb' => '80808080'),
                ));

                $this->excelphp->getActiveSheet()->getStyle('B' . $currow . ':H' . ($currow + 2))->applyFromArray($styleArray);

                $array = array(
                    'borders' => array(
                        'outline' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                );

                $this->excelphp->getActiveSheet()->getStyle('B' . $currow . ':H' . ($currow + 2))->applyFromArray($array);

                $this->excelphp->getActiveSheet()->setCellValue('B' . $currow, 'if you have any questions,please contact:');
                if($invoiceDetails['vendor_name'] == "Black & Veatch"){
                    $this->excelphp->getActiveSheet()->setCellValue('E' . $currow, 'No liens exist, in lieu of an accompanying notarized lien waiver.');
                }
                
                $currow++;
                $this->excelphp->getActiveSheet()->setCellValue('B' . $currow, 'Shaad Khan')->setCellValue('C' . $currow, '(678)896 6118');
//
                $currow++;
                $this->excelphp->getActiveSheet()->setCellValue('B' . $currow, 'Finance Manager')->setCellValue('C' . $currow, 'saad.khan@amdocs.com');
//            $currow++;
//
                //////$currow = $row + 3;
                $styleArray = array(
                    'borders' => array(
                        'bottom' => array(
                            'style' => PHPExcel_Style_Border::BORDER_THICK,
                            'color' => array('argb' => 'f2f24db0'),
                        ),
                    ),
                );

                $this->excelphp->getActiveSheet()->getStyle('B' . $currow . ':H' . $currow)->applyFromArray($styleArray);
                $currow++;
                $this->excelphp->getActiveSheet()->setCellValue('C' . $currow, 'Lavel 1-Confidencial')->setCellValue('G' . $currow, 'Page 1 of 1');

                $filename = $invoiceDetails['vendor_name'] . ' Invoice ' . $invoiceDetails['invoice_number'] . ' PO ' . $invoiceDetails['po_number'] . '.xlsx'; //save our workbook as this file name
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $filename . '"'); //tell browser what's the file name
                header('Cache-Control: max-age=0');
                $objWriter = PHPExcel_IOFactory::createWriter($this->excelphp, 'Excel2007');

                $objWriter->save('php://output');
            } else {
                show_404();
            }
        } else {
            redirect('invoice/invoiceList');
        }
    }

    public function genaratePdf($invoice_id = 0) {
        if (isUserLogin()) {
            $invoice_id = xss_clean($invoice_id);
            $invoiceDetails = $this->invoicemodel->getInvoiceDetailsById($invoice_id, TRUE);
            if ($invoiceDetails['invoice_id']) {

                $trackerDetais = $this->trackermodel->getTrackerByInvoice($invoiceDetails['invoice_id']);
                $this->load->library('pdfphp');
                $pdf = $this->pdfphp->load();
//            $pdf->AddPage('L', // L - landscape, P - portrait
//                    '', '', '', '', 10, // margin_left
//                    10, // margin right
//                    10, // margin top
//                    10, // margin bottom
//                    10, // margin header
//                    10
//                    ); // margin footer
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
                    $html .=    '<tr><td colspan="3" bgcolor="#ddd" style="color:#808080;border-right:0px;">
                    If you have any question please contact:
                    Saad Khan  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; (678)896 6118<br>
                    Finance Manager &nbsp; &nbsp; saad.khan@amdocs.com
                    </td>
                    <td colspan="4" bgcolor="#ddd" style="color:#808080; border-left:0px" valign="top"></td>
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





//            $pdf->SetFooter('Leavel 1 . Confidential||Page {PAGENO} of {nb}');

                $pdf->WriteHTML($html);

                $pdf->Output($invoiceDetails['vendor_name'] . ' Invoice ' . $invoiceDetails['invoice_number'] . ' PO ' . $invoiceDetails['po_number'].'.pdf', 'D'); // save to file
//            $pdf->Output(); // save to file because we can
//            print_r($invoiceDetails);
                exit();
            } else {
                show_404();
            }
        } else {
            redirect('invoice/invoiceList');
        }
    }

}
