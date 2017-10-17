<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Usermodel extends CI_Model {

    protected $userType = array('admin' => 1, 'staff' => 2);
    protected $userStatus = array('active' => 1, 'inactive' => 0);
    protected $userTable = 'user';
    protected $accessTable = 'user_access';

    public function __construct() {
        parent::__construct();
    }

    public function getUserType() {
        return $this->userType;
    }

    public function getUserStatus() {
        return $this->userStatus;
    }

    public function validateUserLogin($email = '', $password = '') {
        $this->db->select('user_id, user_name, email, user_type, last_login, last_login_ip');
        $this->db->where('email', $email);
        $this->db->where('password', $password);
        $this->db->where('user_status', $this->userStatus['active']);
        $result = $this->db->get($this->userTable);
        return $result->row_array();
    }

    public function updateUserLogin($user_id = 0) {
        $this->db->set('last_login_ip', $this->input->ip_address());
        $this->db->set('last_login', 'now()', FALSE);
        $this->db->where('user_id', $user_id);
        $this->db->update($this->userTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->affected_rows();
        } else {
            return 0;
        }
    }

    public function updateUserPassword($user_id = 0, $password = '') {
        $this->db->set('password', $password);
        $this->db->where('user_id', $user_id);
        $this->db->update($this->userTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function validateUserPassword($user_id = NULL, $password = '') {
        if ($user_id === NULL) {
            $user = $this->session->userdata('userLoginData');
            if ($user) {
                $user_id = $user['user_id'];
            } else {
                $user_id = 0;
            }
        }
        $this->db->select('*');
        $this->db->where('user_id', $user_id);
        $this->db->where('user_status', $this->userStatus['active']);
        $this->db->where('password', $password);
        $result = $this->db->get($this->userTable);
        return $result->row_array();
    }

    public function checkUserEmailExists($email = '', $user_id = NULL,$user_active=FALSE) {
        $this->db->select('user_id, user_name, user_type, user_status');
        $this->db->where('email', $email);
        if ($user_id !== NULL) {
            $this->db->where('user_id !=', $user_id);
        }
        if ($user_active) {
            $this->db->where('user_status', $this->userStatus['active']);
        }
        
        $result = $this->db->get($this->userTable);
        return $result->row_array();
    }

    public function getUserById($user_id = 0, $user_active = FALSE) {
        $this->db->select('*');
        if ($user_active) {
            $this->db->where('user_status', $this->userStatus['active']);
        }
        $this->db->where('user_id', $user_id);
        $result = $this->db->get($this->userTable);
        return $result->row_array();
    }

    public function addUser($user_name = '', $password = '', $email = '') {
        $this->db->set('user_name', $user_name);
        $this->db->set('password', $password);
        $this->db->set('email', $email);
        $this->db->insert($this->userTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function updateStaff($user_id = 0, $user_name = '', $password = '', $email = '') {
        $this->db->set('user_name', $user_name);
        $this->db->set('password', $password);
        $this->db->set('email', $email);
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $this->userType['staff']);
        if ($this->db->update($this->userTable)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function changeStaffStatus($user_id = 0, $user_status = 0) {
        $data = array(
            "user_status" => $user_status,
        );
        $this->db->where('user_id', $user_id);
        $this->db->where('user_type', $this->userType['staff']);
        $this->db->update($this->userTable, $data);
        return $this->db->affected_rows();
    }

    public function getStaffListCount($user_active = FAlSE) {
        $this->db->select('*');
        if ($user_active) {
            $this->db->where('user_status', $this->userStatus['active']);
        }


        $this->db->where('user_type', $this->userType['staff']);
        $this->db->from($this->userTable);
        return $this->db->count_all_results();
    }

    public function getStaffList($user_active = FALSE, $limit = NULL, $offset = NULL) {
        $this->db->select('*');
        if ($user_active) {
            $this->db->where('user_status', $this->userStatus['active']);
        }
        $this->db->where('user_type', $this->userType['staff']);
        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        $result = $this->db->get($this->userTable);
        return $result->result_array();
    }

}
