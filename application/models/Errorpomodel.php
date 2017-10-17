<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Errorpomodel extends CI_Model {
    
    protected $trackerStatus = array('open' => 1, 'riv' => 5, 'invoiced' => 4, 'cancelled' => 3);
    protected $errorTable = 'tracker_error';
    protected $orderTable = 'order_log';
    protected $trackerTable = 'tracker';
    protected $regionTable = 'region';
    protected $marketTable = 'market';
    
     public function __construct() {
        parent::__construct();
    }
    
    public function errorpolist($limit = NULL, $offset = NULL, $vendor = 'All',$po_number = ''){
        $this->db->select('tracker_error.*,Count(*) cnt,vendor.id cus_id,vendor.name cus_name');
        $this->db->select($this->regionTable . '.name as region_name')
                ->select($this->marketTable . '.name as market_name');
        $this->db->join('vendor','tracker_error.vendor = vendor.id');
        $this->db->join($this->regionTable, $this->errorTable . '.region =' . $this->regionTable . '.id', 'left');
        $this->db->join($this->marketTable, $this->errorTable . '.market =' . $this->marketTable . '.id', 'left');
        $this->db->from($this->errorTable);
        $this->db->group_by('po_number');
        if ($vendor != 'All') {
            $this->db->where('tracker_error.vendor', $vendor);
        }
        if (!empty($po_number)) {
            $this->db->where('tracker_error.po_number', $po_number);
        }
        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }
    
    public function errorpolistbyID($po_number){
        $this->db->select('tracker_error.*,vendor.id cus_id,vendor.name cus_name,region.name reg_name,market.name market_name');
        $this->db->join('vendor','tracker_error.vendor = vendor.id');
        $this->db->join('region','tracker_error.region = region.id');
        $this->db->join('market','tracker_error.market = market.id');
        $this->db->from($this->errorTable);
        $this->db->where('po_number',$po_number);
        $query = $this->db->get();
        return $query->result_array();
    }
    
    public function updateErrorpo($data){
        $tracker_id = $data['tracker_id'];
        unset($data['tracker_id']);
        $this->db->where('tracker_id',$tracker_id);
        $this->db->update($this->errorTable,$data);
        
        $this->db->select('*');
        $this->db->from($this->errorTable);
        $this->db->where('tracker_id',$tracker_id);
        $query = $this->db->get(); 
        $ins_data = $query->row_array();
        unset($ins_data['tracker_id']);
        /* ====================== */
//        $customer = $ins_data['vendor'];
//        $ponumber = $ins_data['po_number'];
//        $rev = $ins_data['rev'];
//        $orderDetails = $this->getOrderDetils($customer, $ponumber);
        /*=======================*/
        if ($ins_data['amount']==0.00) {
            $ins_data['status'] = $this->trackerStatus['cancelled'];
        } else {
            $ins_data['status'] = $this->trackerStatus['open'];
        }
        
        $region = $ins_data['region'];
        $market = $ins_data['market'];
        $vendor = $ins_data['vendor'];
        $po_number = $ins_data['po_number'];
        $line = $ins_data['line'];
        $po_date = $ins_data['po_date'];
        $site_name = $ins_data['site_name'];
        $description = $ins_data['description'];
        $unit_price = $ins_data['unit_price'];
        $amount = $ins_data['amount'];
        $qty = $ins_data['qty'];
        $rev = $ins_data['rev'];
        $supplier_no = $ins_data['supplier_no'];
        $this->addTracker($region, $market , $vendor , $po_number , $line , $po_date , $site_name, $description , $unit_price, $amount, $qty, $rev, $supplier_no) ;
        
        //$this->db->insert('tracker',$ins_data);
        $this->deleteErrorpo($tracker_id);
        
        return true;
    }
    
    
    public function deleteErrorpo($tracker_id){
        $this->db->where('tracker_id',$tracker_id);
        $this->db->delete($this->errorTable);
        return true;
    }
    
    public function geterrortrackerCount($vendor = 'All',$po_number = '') {
        $this->db->select('tracker_error.*,Count(*) cnt,vendor.id cus_id,vendor.name cus_name');
        $this->db->join('vendor','tracker_error.vendor = vendor.id');
        $this->db->from($this->errorTable);
        $this->db->group_by('po_number');
        if ($vendor != 'All') {
            $this->db->where('tracker_error.vendor', $vendor);
        }
        if (!empty($po_number)) {
            $this->db->where('tracker_error.po_number', $po_number);
        }
        
        return $this->db->count_all_results();
    }
    
    public function getOrderDetils($vendor = 0, $po_number = NULL) {
        if ($vendor !== NULL) {
            $this->db->where($this->orderTable . '.vendor', $vendor);
        }
        if ($po_number !== NULL) {
            $this->db->where($this->orderTable . '.po_number', $po_number);
        }
        $this->db->select($this->orderTable . '.*')
                ->from($this->orderTable);
        $query = $this->db->get();
        return $query->row_array();
    }
    
    function addTracker($region = 0, $market = 0, $vendor = 0, $po_number = '', $line = '', $po_date = '', $site_name = '', $description = '', $unit_price = 0, $amount = 0, $qty = '', $rev = 0, $supplier_no='') {
        //echo $supplier_no;
        if ($amount == 0 || $unit_price == 0 || $unit_price == '0.00' || $unit_price == 0.00) {
            $status = $this->trackerStatus['cancelled'];
        } else {
            $status = $this->trackerStatus['open'];
        }
        $sql = 'INSERT INTO ' . $this->db->dbprefix($this->trackerTable) . ' (region, market, vendor, po_number, line, po_line_rev, po_date, site_name,description, unit_price, amount, qty, rev, status, supplier_no)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE 
                        po_date=?,
                        site_name=?,
                        description=?, 
                        unit_price=?, 
                        amount=?,
                        qty=?, 
                        rev=?,
                        status=?';
        $this->db->query($sql, array($region, $market, $vendor, $po_number, $line, $po_number . $line, $po_date, $site_name, $description, $unit_price, $amount, $qty, $rev, $status, $supplier_no, $po_date, $site_name, $description, $unit_price, $amount, $qty, $rev, $status));
        //echo $this->db->last_query(); die();
        return TRUE;
    }
}




?>