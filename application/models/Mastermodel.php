<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *
 */

class Mastermodel extends CI_Model {

    //table
    protected $orderTable = 'order_log';
    protected $trackerTable = 'tracker';
    protected $regionTable = 'region';
    protected $marketTable = 'market';
    protected $vendorTable = 'vendor';

    public function __construct() {
        parent::__construct();
    }

    //vendor
    public function getAllVendor($limit = NULL, $offset = NULL) {
        $this->db->select($this->vendorTable . '.*')
                ->from($this->vendorTable);

        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getVendorCount() {
        return $this->db->count_all($this->vendorTable);
    }

    public function getVendorByID($id = 0) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $result = $this->db->get($this->vendorTable);
        return $result->row_array();
    }

    public function addVendor($name = '', $address = '', $terms = '', $credit_day = 0) {
        $this->db->set('name', $name);
        $this->db->set('address', $address);
        $this->db->set('terms', $terms);
        $this->db->set('credit_day', $credit_day);
        $this->db->insert($this->vendorTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function editVendor($id = 0, $name = '', $address = '', $terms = '', $credit_day = 0) {
        $this->db->set('name', $name);
        $this->db->set('address', $address);
        $this->db->set('terms', $terms);
        $this->db->set('credit_day', $credit_day);
        $this->db->where('id', $id);
        $this->db->update($this->vendorTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delereVendor($id = 0) {
        $this->db->where('id', $id);
        $this->db->delete($this->vendorTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkVendorExists($name = '', $id = NULL) {
        $this->db->select('*');
        $this->db->where('name', $name);
        if ($id !== NULL) {
            $this->db->where('id !=', $id);
        }
        $result = $this->db->get($this->vendorTable);
        return $result->row_array();
    }

    //region
    public function getAllRegion($limit = NULL, $offset = NULL) {
        $this->db->select($this->regionTable . '.*')
                ->from($this->regionTable);

        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getRegionCount() {
        return $this->db->count_all($this->regionTable);
    }

    public function getRegionByID($id = 0) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $result = $this->db->get($this->regionTable);
        return $result->row_array();
    }

    public function addRegion($name = '') {
        $this->db->set('name', $name);
        $this->db->insert($this->regionTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function editRegion($id = 0, $name = '') {
        $this->db->set('name', $name);
        $this->db->where('id', $id);
        $this->db->update($this->regionTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delereRegion($id = 0) {
        $this->db->where('id', $id);
        $this->db->delete($this->regionTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkRegionExists($name = '', $id = NULL) {
        $this->db->select('*');
        $this->db->where('name', $name);
        if ($id !== NULL) {
            $this->db->where('id !=', $id);
        }
        $result = $this->db->get($this->regionTable);
        return $result->row_array();
    }

    //market
    public function getAllMarket($limit = NULL, $offset = NULL, $region_id = NULL) {
        $this->db->select($this->marketTable . '.*')
                ->select($this->regionTable . '.name as region_name', FALSE)
                ->from($this->marketTable)
                ->join($this->regionTable, $this->regionTable . '.id=' . $this->marketTable . '.region_id');
        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        if ($region_id) {
            $this->db->where('region_id', $region_id);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getMarketCount() {
        return $this->db->count_all($this->marketTable);
    }

    public function getMarketByID($id = 0) {
        $this->db->select('*');
        $this->db->where('id', $id);
        $result = $this->db->get($this->marketTable);
        return $result->row_array();
    }

    public function addMarket($region_id = 0, $name = '') {
        $this->db->set('name', $name);
        $this->db->set('region_id', $region_id);
        $this->db->insert($this->marketTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function editMarket($id = 0, $region_id = 0, $name = '') {
        $this->db->set('name', $name);
        $this->db->set('region_id', $region_id);
        $this->db->where('id', $id);
        $this->db->update($this->marketTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function delereMarket($id = 0) {
        $this->db->where('id', $id);
        $this->db->delete($this->marketTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return 1;
        } else {
            return 0;
        }
    }

    public function checkMarketExists($name = '', $id = NULL) {
        $this->db->select('*');
        $this->db->where('name', $name);
        if ($id !== NULL) {
            $this->db->where('id !=', $id);
        }
        $result = $this->db->get($this->marketTable);
        return $result->row_array();
    }

}
