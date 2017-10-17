<?php

class User extends CI_Controller {

    protected $user_id;
    protected $userId = NULL;
    protected $vendor_id = NULL;
    protected $region_id = NULL;
    protected $market_id = NULL;

    public function __construct() {
        parent::__construct();
        $this->load->model(array('usermodel', 'mastermodel', 'trackermodel'));
    }

    public function index() {
        isUserLogin(TRUE);
        redirect();
    }

    public function login() {
        if (isUserLogin()) {
            redirect('home');
        }
        if (isset($_POST['email'])) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('email', 'Email', 'trim|required|xss_clean|valid_email');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|xss_clean|callback_validate_user');
            if ($this->form_validation->run()) {
                redirect('home');
            }
        }
        $data['menu_id'] = 'login';
        $data['title'] = 'amdocs login';
        $data['page_heading'] = 'login';
        $this->load->view('user/login');
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('user/login');
    }

    public function forgotpassword() {
        if (isUserLogin()) {
            redirect('home');
        }
        if ($this->input->post('forget_submit')) {
            $this->load->library(array('form_validation'));
            $this->form_validation->set_rules('email', 'Email-id', 'trim|required|xss_clean|valid_email|callback_validate_email');
            if ($this->form_validation->run()) {
                $password = generatePassword(8, 12);

                $this->usermodel->updateUserPassword($this->user_id, $password);

                // Mail Function
                $to = (string) $this->input->post('email');
                $subject = "New Password";
                $body = '';
                $body .= '<div style="background:#ededed; padding:0; margin:0; font-size:14px; font-family: Arial, sans-serif;">';
                $body .= '<table border="0" cellspacing="0" cellpadding="0" style="width:80%; margin:0 auto; border:1px solid #cccccc;">';
                $body .= '<tbody>';
                $body .= '<tr>';
                $body .= '<td colspan="2" style="background:#FFFFFF; padding:15px 0; border-bottom:1px solid #ccc; text-align:left; padding:0 0 0 25px; text-align:center;">';
                $body .= '<img src="' . base_url(IMAGE . '/amdocswhite.svg') . '">';
                $body .= ' </td>';
                $body .= '</tr>';
                $body .= '<tr>';
                $body .= '<td colspan = "2" style = "background:#ffffff; padding:25px;">';
                $body .= '<p>Respected Sir/ Madam, </p>';

                $body .= '<p>New password for your Account on <a href="' . site_url() . '">Amdocs</a> has been created, </p>';

                $body .= '<p>Your new password is ' . $password . ' </p>';

                $body .= ' <p>Yours truly, </p>';
                $body .= '<p>Support Team </p>';
                $body .= '</td>';
                $body .= '</tr>';
                $body .= '<tr>';
                $body .= '<td colspan = "2" style = "background:#333333; color:#f3f3f3; padding:10px; text-align:center; font-size:12px;">';
                $body .= '&copy 2017. All Rights Reserved.';
                $body .= ' </td>';
                $body .= '</tr>';
                $body .= ' </tbody>';
                $body .= ' </table>';
                $body .= ' </div>';

                if (sendEmail($to, $subject, $body)) {
                    $this->session->set_flashdata('success_message', "Please check your phone or mail to get your new password!");
                } else {
                    $this->session->set_flashdata('error_message', "Try after some time later!");
                }
                redirect('user/forgotpassword');
            }
        }

        $data['page_title'] = "forget password";
        $this->load->view('user/fogotpassword', $data);
    }

    public function change_password() {

        isUserLogin(TRUE);

        if ($this->input->post('change')) {
            $this->load->library('form_validation');
            $this->form_validation->set_rules('old_pass', 'Old Password', 'trim|required|xss_clean|callback_validate_password');
            $this->form_validation->set_rules('new_pass', 'New Pass', 'trim|required|xss_clean|min_length[6]');
            $this->form_validation->set_rules('repeat_pass', 'Repeat', 'trim|required|xss_clean|matches[new_pass]');
            if ($this->form_validation->run() == TRUE) {
                $user = $this->session->userdata('userLoginData');
                $chagePass = $this->usermodel->updateUserPassword($user['user_id'], $this->input->post('new_pass'));
                if ($chagePass) {
                    $this->session->set_flashdata('success_message', 'Your password changed succesfully ');
                    redirect('user/change_password');
                } else {
                    $this->session->set_flashdata('error_message', 'Try after some time later');
                }
            }
        }

        $data['title'] = "Change Password";
        $data['page_heading'] = "Change Password";
        $data['view_file'] = "user/change_password_view";
        view($data);
    }

    //staff
    public function staff_list() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'staff_list';
            $data['title'] = 'User List';
            $data['page_heading'] = 'Staff List';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'staff_list_view';
            $data['ajax_url'] = site_url('user/ajaxstafftoggle');
            $data['ajax_pass_url'] = site_url('user/ajaxresetpass');
            $data['js_files'] = array('user/user.js');


            $this->load->library("pagination");
            $config['base_url'] = base_url("user/user_list");
            $config['total_rows'] = $this->usermodel->getStaffListCount();
            $config['per_page'] = $this->config->item('per_page');
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;
            $config['num_links'] = 2;
            $config['cur_tag_open'] = '<li class="active"><a class="current">';
            $config['cur_tag_close'] = '</a></li>';
            $config['next_link'] = '<i class="fa fa-chevron-right"></i>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = '<i class="fa fa-chevron-left"></i>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['full_tag_open'] = '<ul class="pagination mart15">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['last_link'] = '<i class="fa fa-arrow-right"></i>';
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

            $data['user_status'] = $this->usermodel->getUserStatus();
            //get all partner
            $data['stafflist'] = $this->usermodel->getStaffList(FALSE, $config["per_page"], ($page - 1) * $config['per_page']);
            view($data);
        } else {
            show_404();
        }
    }

    public function add_staff() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            if ($this->input->post('submit')) {

                $this->load->library(array('form_validation'));
                //formvalidation
                $this->form_validation->set_rules('user_name', 'Staff  Name', 'trim|required|xss_clean');
                $this->form_validation->set_rules('user_email', 'Staff  Email', 'trim|required|xss_clean|valid_email|callback_validate_email_exists');
                $this->form_validation->set_rules('user_password', 'Staff Password', 'trim|required|min_length[6]|xss_clean|alpha_numeric');

                if ($this->form_validation->run()) {
                    $user_name = $this->input->post('user_name');
                    $user_email = $this->input->post('user_email');
                    $user_password = $this->input->post('user_password');
                    $result = $this->usermodel->adduser($user_name, $user_password, $user_email);
                    if ($result) {
                        $this->session->set_flashdata('success_message', 'Added successfully');
                        redirect('user/staff_list');
                    } else {
                        $this->session->set_flashdata('error_message', 'Error ocure when add your data!');
                        redirect('user/add_staff');
                    }
                }
            }
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'staff_list';
            $data['title'] = 'Add New User';
            $data['page_heading'] = 'Add New Staff';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'add_staff_view';
            view($data);
        } else {
            show_404();
        }
    }

    public function edit_staff($user_id = 0) {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $this->userId = xss_clean($user_id);

            $data['user_details'] = $user_details = $this->usermodel->getUserById($this->userId);
            if ($user_details['user_id']) {
                if ($this->input->post('submit')) {
                    $this->load->library(array('form_validation'));
                    $this->form_validation->set_rules('user_name', 'User  Name', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('user_email', 'User  Email', 'trim|required|xss_clean|valid_email|callback_validate_email_on_edit');
                    $this->form_validation->set_rules('user_password', 'User Password', 'trim|required|xss_clean|min_length[6]|alpha_numeric');
                    if ($this->form_validation->run()) {
                        $user_name = $this->input->post('user_name');
                        $user_email = $this->input->post('user_email');
                        $user_password = $this->input->post('user_password');
                        $result = $this->usermodel->updateStaff($this->userId, $user_name, $user_password, $user_email);
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Update successfully');
                            redirect('user/staff_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when update your data!');
                        }
                    }
                }
                $data['menu_id'] = 'admin';
                $data['sub_menu_id'] = 'staff_list';
                $data['title'] = 'Edit New User';
                $data['page_heading'] = 'Edit New Staff';
                $data['view_file'] = 'user/admin_layout';
                $data['sub_view_file'] = 'edit_staff_view';
                view($data);
            } else {
                $this->session->set_flashdata('error_message', "No user found with this key!");
                redirect("user/staff_list");
            }
        } else {
            show_404();
        }
    }

    public function ajaxstafftoggle() {
        if (isUserLogin()) {
            if (checkCurrentUserLoginRole(array('admin'))) {
                $this->load->library(array('form_validation'));
                $this->form_validation->set_rules('user_id', 'Staff Id', 'trim|required|xss_clean');
                if ($this->form_validation->run()) {
                    $staff = $this->usermodel->getUserById($this->input->post('user_id'));

                    $staffStatus = $this->usermodel->getUserStatus();

                    if (isset($staff['user_id'])) {
                        if ($staff['user_status'] == $staffStatus['inactive']) {
                            $status = $staffStatus['active'];
                        } else {
                            $status = $staffStatus['inactive'];
                        }
                        $result_status = $this->usermodel->changeStaffStatus($staff['user_id'], $status);
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
        } else {
            $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => 0)));
        }
    }

    public function ajaxresetpass() {
        if (isUserLogin()) {
            if (checkCurrentUserLoginRole(array('admin'))) {
                $this->load->library(array('form_validation'));
                $this->form_validation->set_rules('user_id', 'Staff Id', 'trim|required|xss_clean');
                if ($this->form_validation->run()) {

                    $staff_details = $this->usermodel->getUserById($this->input->post('user_id'));
                    if ($staff_details) {
                        $staff_password = generatePassword(8, 12);

                        $change = $this->usermodel->updateUserPassword($this->input->post('user_id'), $staff_password);
                        if ($change) {
                            // Mail Function
                            $to = (string) $staff_details['email'];
                            $subject = "Reset Account Detais";
                            $body = '';
                            $body .= '<div style="background:#ededed; padding:0; margin:0; font-size:14px; font-family: Arial, sans-serif;">';
                            $body .= '<table border="0" cellspacing="0" cellpadding="0" style="width:80%; margin:0 auto; border:1px solid #cccccc;">';
                            $body .= '<tbody>';
                            $body .= '<tr>';
                            $body .= '<td colspan="2" style="background:#FFFFFF; padding:15px 0; border-bottom:1px solid #ccc; text-align:left; padding:0 0 0 25px; text-align:center;">';
                            $body .= '<img src="' . base_url(IMAGE . '/amdocswhite.svg') . '">';
                            $body .= ' </td>';
                            $body .= '</tr>';
                            $body .= '<tr>';
                            $body .= '<td colspan = "2" style = "background:#ffffff; padding:25px;">';
                            $body .= '<p>Respected Sir/ Madam, </p>';

                            $body .= '<p>password for your Account on <a href="' . site_url() . '">Amdocs</a> has been changed, </p>';

                            $body .= '<p>Your new password is ' . $staff_password . ' </p>';

                            $body .= ' <p>Yours truly, </p>';
                            $body .= '<p>Support Team </p>';
                            $body .= '</td>';
                            $body .= '</tr>';
                            $body .= '<tr>';
                            $body .= '<td colspan = "2" style = "background:#333333; color:#f3f3f3; padding:10px; text-align:center; font-size:12px;">';
                            $body .= '&copy 2017. All Rights Reserved.';
                            $body .= ' </td>';
                            $body .= '</tr>';
                            $body .= ' </tbody>';
                            $body .= ' </table>';
                            $body .= ' </div>';

                            if (sendEmail($to, $subject, $body)) {
                                $this->session->set_flashdata('success_message', "user password and pin successfully reset");
                                $this->output
                                        ->set_content_type('application/json')
                                        ->set_output(json_encode(array('status' => 1)));
                            } else {
                                $this->session->set_flashdata('success_message', "user password and pin successfully reset but mail send error");
                                $this->output
                                        ->set_content_type('application/json')
                                        ->set_output(json_encode(array('status' => 1)));
                            }
                        } else {
                            $this->output
                                    ->set_content_type('application/json')
                                    ->set_output(json_encode(array('status' => 0)));
                        }
                    } else {
                        $this->session->set_flashdata('error_message', "Staff not found with this id!.");
                        $this->output
                                ->set_content_type('application/json')
                                ->set_output(json_encode(array('status' => 0)));
                    }
                } else {
                    $this->output
                            ->set_content_type('application/json')
                            ->set_output(json_encode(array('status' => 0)));
                }
            } else {
                $this->output
                        ->set_content_type('application/json')
                        ->set_output(json_encode(array('status' => 0)));
            }
        } else {
            $this->output
                    ->set_content_type('application/json')
                    ->set_output(json_encode(array('status' => 0)));
        }
    }

    //vendor
    public function customer_list() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'customer_list';
            $data['title'] = 'Customer List';
            $data['page_heading'] = 'Customer List';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'customer_list';

            $this->load->library("pagination");
            $config['base_url'] = base_url("user/customer_list");
            $config['total_rows'] = $this->mastermodel->getVendorCount();
            $config['per_page'] = $this->config->item('per_page');
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;
            $config['num_links'] = 2;
            $config['cur_tag_open'] = '<li class="active"><a class="current">';
            $config['cur_tag_close'] = '</a></li>';
            $config['next_link'] = '<i class="fa fa-chevron-right"></i>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = '<i class="fa fa-chevron-left"></i>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['full_tag_open'] = '<ul class="pagination mart15">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['last_link'] = '<i class="fa fa-arrow-right"></i>';
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

            //get all partner
            $data['customerlist'] = $this->mastermodel->getAllVendor($config["per_page"], ($page - 1) * $config['per_page']);
            view($data);
        } else {
            show_404();
        }
    }

    public function add_customer() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            if ($this->input->post('submit')) {
                $this->load->library(array('form_validation'));
                //formvalidation
                $this->form_validation->set_rules('customer_name', 'Customer  Name', 'trim|required|xss_clean|callback_check_vendor_name_exists');
                $this->form_validation->set_rules('address', 'Customer  Address', 'trim|required|xss_clean');
                $this->form_validation->set_rules('terms', 'Payment Terms ', 'trim|required|xss_clean');
                $this->form_validation->set_rules('credit_day', 'Credit Day', 'trim|required|xss_clean|is_natural');
                if ($this->form_validation->run()) {
                    $result = $this->mastermodel->addVendor($this->input->post('customer_name'), $this->input->post('address'), $this->input->post('terms'), $this->input->post('credit_day'));
                    if ($result) {
                        $this->session->set_flashdata('success_message', 'Customer added successfully');
                        redirect('user/customer_list');
                    } else {
                        $this->session->set_flashdata('error_message', 'Error ocure when add your data!');
                        redirect('user/customer_list');
                    }
                }
            }
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'customer_list';
            $data['title'] = 'Add New Customer';
            $data['page_heading'] = 'Add New Customer';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'add_customer_view';
            view($data);
        } else {
            show_404();
        }
    }

    public function edit_customer($vendor_id = 0) {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $this->vendor_id = xss_clean($vendor_id);

            $data['vendor'] = $this->mastermodel->getvendorById($this->vendor_id);
            if (isset($data['vendor']['id'])) {
                if ($this->input->post('submit')) {
                    $this->load->library(array('form_validation'));
                    $this->form_validation->set_rules('customer_name', 'Customer  Name', 'trim|required|xss_clean|callback_check_vendor_name_on_edit');
                    $this->form_validation->set_rules('address', 'Customer  Address', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('terms', 'Payment Terms ', 'trim|required|xss_clean');
                    $this->form_validation->set_rules('credit_day', 'Credit Day', 'trim|required|xss_clean|is_natural');
                    if ($this->form_validation->run()) {
                        $result = $this->mastermodel->editVendor($this->vendor_id, $this->input->post('customer_name'), $this->input->post('address'), $this->input->post('terms'), $this->input->post('credit_day'));
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Update successfully');
                            redirect('user/customer_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when update your data!');
                            redirect('user/customer_list');
                        }
                    }
                }
                $data['menu_id'] = 'admin';
                $data['sub_menu_id'] = 'customer_list';
                $data['title'] = 'Edit Customer';
                $data['page_heading'] = 'Edit Customer';
                $data['view_file'] = 'user/admin_layout';
                $data['sub_view_file'] = 'edit_customer_view';
                view($data);
            } else {
                $this->session->set_flashdata('error_message', "No customer found with this key!");
                redirect("user/customer_list");
            }
        } else {
            show_404();
        }
    }

    public function delete_customer($vendor_id = 0) {
        if (isUserLogin(TRUE)) {
            if (checkCurrentUserLoginRole(array('admin'))) {
                $this->vendor_id = xss_clean($vendor_id);

                $data['vendor'] = $this->mastermodel->getvendorById($this->vendor_id);
                if (isset($data['vendor']['id'])) {
                    $trackerdetails = $this->trackermodel->getTrackerDetails(NULL, $data['vendor']['id']);
//                    echo '<pre/>';  print_r($trackerdetails); exit();
                    if ($trackerdetails) {
                        //Cannot delete, return
                        $this->session->set_flashdata('error_message', 'You can\'t delete this customer!');
                        redirect('user/customer_list');
                    } else {
                        //delete customer
                        $result = $this->mastermodel->delereVendor($data['vendor']['id']);
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Delete successfully');
                            redirect('user/customer_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when delete your data!');
                            redirect('user/customer_list');
                        }
                    }
                } else {
                    $this->session->set_flashdata('error_message', "No customer found with this key!");
                    redirect("user/customer_list");
                }
            } else {
                $this->session->set_flashdata('error_message', 'You are not authorized person!');
                redirect('user/customer_list');
            }
        } else {
            $this->session->set_flashdata('error_message', 'Login first!');
            redirect('/');
        }
    }

    //region
    public function region_list() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'region_list';
            $data['title'] = 'Region List';
            $data['page_heading'] = 'Region List';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'region_list';

            $this->load->library("pagination");
            $config['base_url'] = base_url("user/region_list");
            $config['total_rows'] = $this->mastermodel->getRegionCount();
            $config['per_page'] = $this->config->item('per_page');
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;
            $config['num_links'] = 2;
            $config['cur_tag_open'] = '<li class="active"><a class="current">';
            $config['cur_tag_close'] = '</a></li>';
            $config['next_link'] = '<i class="fa fa-chevron-right"></i>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = '<i class="fa fa-chevron-left"></i>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['full_tag_open'] = '<ul class="pagination mart15">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['last_link'] = '<i class="fa fa-arrow-right"></i>';
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

            //get all partner
            $data['regionlist'] = $this->mastermodel->getAllRegion($config["per_page"], ($page - 1) * $config['per_page']);
            view($data);
        } else {
            show_404();
        }
    }

    public function add_region() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            if ($this->input->post('submit')) {
                $this->load->library(array('form_validation'));
                //formvalidation
                $this->form_validation->set_rules('region_name', 'Region  Name', 'trim|required|xss_clean|callback_check_region_name_exists');
                if ($this->form_validation->run()) {
                    $result = $this->mastermodel->addRegion($this->input->post('region_name'));
                    if ($result) {
                        $this->session->set_flashdata('success_message', 'Region added successfully');
                        redirect('user/region_list');
                    } else {
                        $this->session->set_flashdata('error_message', 'Error ocure when add your data!');
                        redirect('user/region_list');
                    }
                }
            }
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'region_list';
            $data['title'] = 'Add New Region';
            $data['page_heading'] = 'Add New Region';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'add_region_view';
            view($data);
        } else {
            show_404();
        }
    }

    public function edit_region($region_id = 0) {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $this->region_id = xss_clean($region_id);

            $data['region'] = $this->mastermodel->getRegionById($this->region_id);
            if (isset($data['region']['id'])) {
                if ($this->input->post('submit')) {
                    $this->load->library(array('form_validation'));
                    $this->form_validation->set_rules('region_name', 'Region  Name', 'trim|required|xss_clean|callback_check_region_name_on_edit');
                    if ($this->form_validation->run()) {
                        $result = $this->mastermodel->editRegion($this->region_id, $this->input->post('region_name'));
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Update successfully');
                            redirect('user/region_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when update your data!');
                            redirect('user/region_list');
                        }
                    }
                }
                $data['menu_id'] = 'admin';
                $data['sub_menu_id'] = 'region_list';
                $data['title'] = 'Edit Region';
                $data['page_heading'] = 'Edit Region';
                $data['view_file'] = 'user/admin_layout';
                $data['sub_view_file'] = 'edit_region_view';
                view($data);
            } else {
                $this->session->set_flashdata('error_message', "No region found with this key!");
                redirect("user/region_list");
            }
        } else {
            show_404();
        }
    }

    public function delete_region($region_id = 0) {
        if (isUserLogin(TRUE)) {
            if (checkCurrentUserLoginRole(array('admin'))) {
                $this->region_id = xss_clean($region_id);

                $data['region'] = $this->mastermodel->getRegionByID($this->region_id);
                if (isset($data['region']['id'])) {
                    $trackerdetails = $this->trackermodel->getTrackerDetails(NULL, NULL, NULL, $data['region']['id']);

                    if ($trackerdetails) {
                        //Cannot delete, return
                        $this->session->set_flashdata('error_message', 'You can\'t delete this region as tracker exists for this region!');
                        redirect('user/region_list');
                    } else {
                        $marketDetails = $this->mastermodel->getAllMarket(NULL, NULL, $data['region']['id']);
                        if ($marketDetails) {
                            $this->session->set_flashdata('error_message', 'You can\'t delete this region as market exists for this region!');
                            redirect('user/region_list');
                        } else {
                            //delete region
                            $result = $this->mastermodel->delereRegion($data['region']['id']);
                            if ($result) {
                                $this->session->set_flashdata('success_message', 'Delete successfully');
                                redirect('user/region_list');
                            } else {
                                $this->session->set_flashdata('error_message', 'Error ocure when delete your data!');
                                redirect('user/region_list');
                            }
                        }
                    }
                } else {
                    $this->session->set_flashdata('error_message', "No region found with this key!");
                    redirect("user/region_list");
                }
            } else {
                $this->session->set_flashdata('error_message', 'You are not authorized person!');
                redirect('user/region_list');
            }
        } else {
            $this->session->set_flashdata('error_message', 'Login first!');
            redirect('/');
        }
    }

    //market
    public function market_list() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'market_list';
            $data['title'] = 'Market List';
            $data['page_heading'] = 'Market List';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'market_list';

            $this->load->library("pagination");
            $config['base_url'] = base_url("user/market_list");
            $config['total_rows'] = $this->mastermodel->getMarketCount();
            $config['per_page'] = $this->config->item('per_page');
            $config['uri_segment'] = 3;
            $config['use_page_numbers'] = TRUE;
            $config['num_links'] = 2;
            $config['cur_tag_open'] = '<li class="active"><a class="current">';
            $config['cur_tag_close'] = '</a></li>';
            $config['next_link'] = '<i class="fa fa-chevron-right"></i>';
            $config['next_tag_open'] = '<li>';
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = '<i class="fa fa-chevron-left"></i>';
            $config['prev_tag_open'] = '<li>';
            $config['prev_tag_close'] = '</li>';
            $config['num_tag_open'] = '<li>';
            $config['num_tag_close'] = '</li>';
            $config['full_tag_open'] = '<ul class="pagination mart15">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = '<i class="fa fa-arrow-left"></i>';
            $config['last_link'] = '<i class="fa fa-arrow-right"></i>';
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

            //get all partner
            $data['marketlist'] = $this->mastermodel->getAllMarket($config["per_page"], ($page - 1) * $config['per_page']);
            view($data);
        } else {
            show_404();
        }
    }

    public function add_market() {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            if ($this->input->post('submit')) {
                $this->load->library(array('form_validation'));
                //formvalidation
                $this->form_validation->set_rules('market_name', 'Market  Name', 'trim|required|xss_clean|callback_check_market_name_exists');
                $this->form_validation->set_rules('region_id', 'Market  Name', 'trim|required|xss_clean|callback_check_region_id_exists');
                if ($this->form_validation->run()) {
                    $result = $this->mastermodel->addMarket($this->input->post('region_id'), $this->input->post('market_name'));
                    if ($result) {
                        $this->session->set_flashdata('success_message', 'Market added successfully');
                        redirect('user/market_list');
                    } else {
                        $this->session->set_flashdata('error_message', 'Error ocure when add your data!');
                        redirect('user/market_list');
                    }
                }
            }
            $data['region'] = $this->mastermodel->getAllRegion();
            $data['menu_id'] = 'admin';
            $data['sub_menu_id'] = 'market_list';
            $data['title'] = 'Add New Market';
            $data['page_heading'] = 'Add New Market';
            $data['view_file'] = 'user/admin_layout';
            $data['sub_view_file'] = 'add_market_view';
            view($data);
        } else {
            show_404();
        }
    }

    public function edit_market($market_id = 0) {
        isUserLogin(TRUE);
        if (checkCurrentUserLoginRole(array('admin'))) {
            $this->market_id = xss_clean($market_id);

            $data['market'] = $this->mastermodel->getMarketById($this->market_id);
            if (isset($data['market']['id'])) {
                if ($this->input->post('submit')) {
                    $this->load->library(array('form_validation'));
                    $this->form_validation->set_rules('market_name', 'Market  Name', 'trim|required|xss_clean|callback_check_market_name_on_edit');
                    $this->form_validation->set_rules('region_id', 'Market  Name', 'trim|required|xss_clean|callback_check_region_id_exists');
                    if ($this->form_validation->run()) {
                        $result = $this->mastermodel->editMarket($this->market_id, $this->input->post('region_id'), $this->input->post('market_name'));
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Update successfully');
                            redirect('user/market_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when update your data!');
                            redirect('user/market_list');
                        }
                    }
                }
                $data['region'] = $this->mastermodel->getAllRegion();
                $data['menu_id'] = 'admin';
                $data['sub_menu_id'] = 'market_list';
                $data['title'] = 'Edit Market';
                $data['page_heading'] = 'Edit Market';
                $data['view_file'] = 'user/admin_layout';
                $data['sub_view_file'] = 'edit_market_view';
                view($data);
            } else {
                $this->session->set_flashdata('error_message', "No market found with this key!");
                redirect("user/market_list");
            }
        } else {
            show_404();
        }
    }

    public function delete_market($market_id = 0) {
        if (isUserLogin(TRUE)) {
            if (checkCurrentUserLoginRole(array('admin'))) {
                $this->market_id = xss_clean($market_id);

                $data['market'] = $this->mastermodel->getMarketByID($this->market_id);
                if (isset($data['market']['id'])) {
                    $trackerdetails = $this->trackermodel->getTrackerDetails(NULL, NULL, $data['market']['id']);
//                    echo '<pre/>';  print_r($trackerdetails); exit();
                    if ($trackerdetails) {
                        //Cannot delete, return
                        $this->session->set_flashdata('error_message', 'You can\'t delete this market as tracker exists for this market!');
                        redirect('user/market_list');
                    } else {
                        //delete customer
                        $result = $this->mastermodel->delereMarket($data['market']['id']);
                        if ($result) {
                            $this->session->set_flashdata('success_message', 'Delete successfully');
                            redirect('user/market_list');
                        } else {
                            $this->session->set_flashdata('error_message', 'Error ocure when delete your data!');
                            redirect('user/market_list');
                        }
                    }
                } else {
                    $this->session->set_flashdata('error_message', "No market found with this key!");
                    redirect("user/market_list");
                }
            } else {
                $this->session->set_flashdata('error_message', 'You are not authorized person!');
                redirect('user/market_list');
            }
        } else {
            $this->session->set_flashdata('error_message', 'Login first!');
            redirect('/');
        }
    }

    //*callback function ; due to login*/
    public function validate_user($password) {
        $email = $this->input->post('email');
        $result = $this->usermodel->validateUserLogin($email, $password);
        if (empty($result)) {
            $this->form_validation->set_message('validate_user', 'Invalid credentials');
            return FALSE;
        } else {
            $this->usermodel->updateUserLogin($result['user_id']);
            $this->session->set_userdata('userLoginData', $result);
            return TRUE;
        }
    }

    /* callback function: due to forgot password */

    public function validate_email($email_id) {
        if (!empty($email_id)) {
            $result = $this->usermodel->checkUserEmailExists($email_id, NULL, TRUE);
            if (empty($result)) {
                $this->form_validation->set_message('validate_email', 'Email address not found ');
                return FALSE;
            } else {
                $this->user_id = $result['user_id'];

                return TRUE;
            }
        } else {
            $this->form_validation->set_message('validate_email', 'Email cannot be empty ');
            return FALSE;
        }
    }

    /* callback function for cheking duplicate email address */
    #@param $email
    #@return validation as true or false

    function validate_email_exists($email) {
        $result = $this->usermodel->checkUserEmailExists($email);
        if ($result) {
            $this->form_validation->set_message('validate_email_exists', 'Email address already used.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /* Check email on edit */

    function validate_email_on_edit($email = '') {
        $result = $this->usermodel->checkUserEmailExists($email, $this->userId);
        if ($result) {
            $this->form_validation->set_message('validate_email_on_edit', 'Email address already used.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_vendor_name_exists($customer_name = '') {
        $result = $this->mastermodel->checkVendorExists($customer_name);
        if ($result) {
            $this->form_validation->set_message('check_vendor_name_exists', 'you have already add customer with same name.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_vendor_name_on_edit($customer_name = '') {
        $result = $this->mastermodel->checkVendorExists($customer_name, $this->vendor_id);
        if ($result) {
            $this->form_validation->set_message('check_vendor_name_on_edit', 'Customer name already used.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_region_name_exists($region_name = '') {
        $result = $this->mastermodel->checkRegionExists($region_name);
        if ($result) {
            $this->form_validation->set_message('check_region_name_exists', 'you have already add region with same name.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_region_name_on_edit($region_name = '') {
        $result = $this->mastermodel->checkRegionExists($region_name, $this->region_id);
        if ($result) {
            $this->form_validation->set_message('check_region_name_on_edit', 'Region name already used.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_market_name_exists($market_name = '') {
        $result = $this->mastermodel->checkRegionExists($market_name);
        if ($result) {
            $this->form_validation->set_message('check_market_name_exists', 'you have already add market with same name.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function check_market_name_on_edit($market_name = '') {
        $result = $this->mastermodel->checkRegionExists($market_name, $this->market_id);
        if ($result) {
            $this->form_validation->set_message('check_market_name_on_edit', 'Region name already used.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function validate_password($password = '') {
        $user = $this->session->userdata('userLoginData');
        $result = $this->usermodel->validateUserPassword($user['user_id'], $password);
        if (empty($result)) {
            $this->form_validation->set_message('validate_password', 'Invalid Old Password,Try with exact Password ');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function check_region_id_exists($region_id = 0) {
        $result = $this->mastermodel->getRegionById($region_id);
        if (empty($result)) {
            $this->form_validation->set_message('check_region_id_exists', 'please select an region.');
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
