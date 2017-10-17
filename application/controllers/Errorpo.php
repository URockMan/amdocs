<?php

class Errorpo extends CI_Controller {

    protected $user_id;
    protected $userId = NULL;
    protected $vendor_id = NULL;
    protected $region_id = NULL;
    protected $market_id = NULL;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('errorpomodel', 'mastermodel'));
    }

    public function index() {
        isUserLogin(TRUE);
        if ($this->uri->segment(3) == 'reset') {
            $this->session->unset_userdata('error_search');
            redirect('errorpo');
        }
        if ($this->input->post('vendor')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('vendor', 'Vendor', 'trim|xss_clean|required');
            $this->form_validation->set_rules('ponumber', 'PO Number', 'trim|xss_clean');
            if ($this->form_validation->run()) {
                $error_search['vendor'] = $this->input->post('vendor');
                $error_search['ponumber'] = $this->input->post('ponumber');
                $error_search['reset'] = TRUE;
                $this->session->set_userdata('error_search', $error_search);
            }
        } else if (!$this->session->userdata('error_search')) {
            $error_search['vendor'] = 'All';
            $error_search['ponumber'] = '';
            $this->session->set_userdata('error_search', $error_search);
        }
        $error_search = $this->session->userdata('error_search');

        $this->load->library("pagination");
        $config['base_url'] = base_url("errorpo/index");
        $config['total_rows'] = $this->errorpomodel->geterrortrackerCount($error_search['vendor'], $error_search['ponumber']);
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

        //  echo "hello";
        $data['vendors'] = $this->mastermodel->getAllVendor();
        $data['trackerList'] = $this->errorpomodel->errorpolist($config["per_page"], ($page - 1) * $config['per_page'], $error_search['vendor'],$error_search['ponumber']);
        //$result = $this->errorpomodel->errorpolist();
        $data['resultset'] = $data['trackerList'];
        $data['menu_id'] = 'error';
        $data['title'] = 'error po';
        $data['page_heading'] = 'error po list';



        $data['view_file'] = 'errorpo/list';

        // print_r($result);
        view($data);
    }

    public function test() {
//        //  echo "hello";
//        $data['vendors'] = $this->mastermodel->getAllVendor();
//        $result = $this->errorpomodel->mytest();
//        $data['resultset'] = $result;
//        $data['menu_id'] = 'admin';
//        $data['sub_menu_id'] = 'market_list';
//        $data['title'] = 'error po';
//        $data['page_heading'] = 'Error PO';
//        $data['view_file'] = 'errorpo/list';
//        // print_r($result);
//        view($data);
    }

    public function edit_errorpo($po_number) {
        isUserLogin(TRUE);
        //echo $po_number;
        $po_number = urldecode($po_number);
        $data['resultset'] = $this->errorpomodel->errorpolistbyID($po_number);
        //  echo sizeof($data['resultset']);
        //   print_r($data['resultset']); 
        // $data['menu_id'] = 'admin';
        //  $data['sub_menu_id'] = 'market_list';
        $data['title'] = 'edit errorpo';
        $data['page_heading'] = 'edit po';


        $data['menu_id'] = 'error';
        $data['view_file'] = 'errorpo/edit_errorpo';
        view($data);
    }

    public function ajax_save() {
        isUserLogin(TRUE);
        //$data = str_replace('data=','',$this->input->get('po_date'));
        $data['po_date'] = date('Y-m-d', strtotime($this->input->get('po_date')));
        $data['line'] = $this->input->get('po_line');
        $data['rev'] = $this->input->get('rev');
        $data['site_name'] = $this->input->get('site_name');
        $data['supplier_no'] = $this->input->get('supplier_no');
        $data['description'] = trim($this->input->get('description'));
        $data['qty'] = $this->input->get('qty');
        $data['unit_price'] = $this->input->get('unit_price');
        $data['amount'] = $data['qty'] * $data['unit_price'];
        $data['po_line_rev'] = $this->input->get('po_number') . $this->input->get('po_line');
        $data['tracker_id'] = $this->input->get('hidden_trackerid');
        
        

        $res = $this->errorpomodel->updateErrorpo($data);

        if ($res) {
            echo json_encode(array('status' => 'success'));
        } else {
            echo json_encode(array('status' => 'fail'));
        }
    }

    public function ajax_delete() {
        isUserLogin(TRUE);
        $tracker_id = $this->input->get('tracker_id');
        $this->errorpomodel->deleteErrorpo($tracker_id);
        echo json_encode(array('status' => 'success'));
    }

}
