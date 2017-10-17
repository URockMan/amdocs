<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/*
 *
 */

class Invoicemodel extends CI_Model {

    protected $invoiceStatus = array('active' => 1, 'inactive' => 0);
    //table
    protected $orderTable = 'order_log';
    protected $trackerTable = 'tracker';
    protected $regionTable = 'region';
    protected $marketTable = 'market';
    protected $vendorTable = 'vendor';
    protected $invoiceTable = 'invoice';

    public function __construct() {
        parent::__construct();
    }

    public function getInvoiceStatus() {
        return $this->invoiceStatus;
    }

    function addInvoice($po_number = '', $invoice_date = '', $vendor_id = 0, $region_id = 0, $market_id = 0, $amount = 0) {
        $this->db->set('po_number', $po_number);
        $this->db->set('invoice_date', date('Y-m-d', strtotime($invoice_date)));
        $this->db->set('invoice_due_date', date('Y-m-d', strtotime($invoice_date . ' + 60 days')));
        $this->db->set('vendor_id', $vendor_id);
        $this->db->set('market_id', $market_id);
        $this->db->set('region_id', $region_id);
        $this->db->set('amount', $amount);
        $this->db->insert($this->invoiceTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            $invoiceId = $this->db->insert_id();
            $this->db->from($this->invoiceTable)
                    ->where('MONTH(created_time)', 'MONTH(now())', FALSE)
                    ->where('YEAR(created_time)', 'YEAR(now())', FALSE)
                    ->where('invoice_id<=', $invoiceId);
            $count = $this->db->count_all_results();
            $this->db->set('invoice_number', date('ny') . str_pad(($count + 1), 4, '0', STR_PAD_LEFT));
            $this->db->where('invoice_id', $invoiceId);
            $this->db->update($this->invoiceTable);
            return $invoiceId;
        } else {
            return 0;
        }
    }

    public function getInvoiceById($invoice_id = 0, $isActive = FALSE) {
        $this->db->select($this->invoiceTable . '.*')
                ->from($this->invoiceTable)
                ->where('invoice_id', $invoice_id);
        if ($isActive) {
            $this->db->where('invoice_status', $this->invoiceStatus['active']);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getInvoiceDetailsByIds($invoiceArray, $isActive = FALSE) {
        if (!is_array($invoiceArray)) {
            return array();
        }
        $this->db->select($this->invoiceTable . '.*')
                ->select($this->regionTable . '.name as region_name')
                ->select($this->marketTable . '.name as market_name')
                ->select($this->vendorTable . '.name as vendor_name')
                ->from($this->invoiceTable)
                ->where_in($this->invoiceTable . '.invoice_id', $invoiceArray);
        $this->db->join($this->regionTable, $this->invoiceTable . '.region_id =' . $this->regionTable . '.id', 'left');
        $this->db->join($this->marketTable, $this->invoiceTable . '.market_id =' . $this->marketTable . '.id', 'left');
        $this->db->join($this->vendorTable, $this->invoiceTable . '.vendor_id =' . $this->vendorTable . '.id', 'left');
        if ($isActive) {
            $this->db->where($this->invoiceTable . '.invoice_status', $this->invoiceStatus['active']);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getInvoiceDetailsById($invoice_id = 0, $isActive = FALSE) {
        $this->db->select($this->invoiceTable . '.*')
                ->select($this->regionTable . '.name as region_name')
                ->select($this->marketTable . '.name as market_name')
                ->select($this->vendorTable . '.name as vendor_name')
                ->from($this->invoiceTable)
                ->where($this->invoiceTable . '.invoice_id', $invoice_id);
        $this->db->join($this->regionTable, $this->invoiceTable . '.region_id =' . $this->regionTable . '.id', 'left');
        $this->db->join($this->marketTable, $this->invoiceTable . '.market_id =' . $this->marketTable . '.id', 'left');
        $this->db->join($this->vendorTable, $this->invoiceTable . '.vendor_id =' . $this->vendorTable . '.id', 'left');
        if ($isActive) {
            $this->db->where($this->invoiceTable . '.invoice_status', $this->invoiceStatus['active']);
        }
        $query = $this->db->get();
        return $query->row_array();
    }

    public function getAllInvoice($limit = NULL, $offset = NULL, $vendor_id = 'All', $region_id = 'All', $market_id = 'All', $po_number = '', $invoice_number = '', $fromdate = '', $todate = '', $invoicefromdate = '', $invoicetodate = '', $orderfield = NULL, $rodertype = 'desc', $isActive = FALSE) {
        $this->db->select($this->invoiceTable . '.*')
                ->select($this->regionTable . '.name as region_name')
                ->select($this->marketTable . '.name as market_name')
                ->select($this->vendorTable . '.name as vendor_name')
                ->from($this->invoiceTable);
        $this->db->join($this->regionTable, $this->invoiceTable . '.region_id =' . $this->regionTable . '.id', 'left');
        $this->db->join($this->marketTable, $this->invoiceTable . '.market_id =' . $this->marketTable . '.id', 'left');
        $this->db->join($this->vendorTable, $this->invoiceTable . '.vendor_id =' . $this->vendorTable . '.id', 'left');
        if ($orderfield != NULL) {
            if ($orderfield == 'vendor_id') {
                $this->db->order_by($this->vendorTable . '.name', $rodertype);
            } else if ($orderfield == 'region_id') {
                $this->db->order_by($this->regionTable . '.name', $rodertype);
            } else if ($orderfield == 'market_id') {
                $this->db->order_by($this->marketTable . '.name', $rodertype);
            } else {
                $this->db->order_by($this->invoiceTable . '.' . $orderfield, $rodertype);
            }
        }
        if ($vendor_id != 'All') {
            $this->db->where($this->invoiceTable . '.vendor_id', $vendor_id);
        }

        if ($region_id != 'All') {
            $this->db->where($this->invoiceTable . '.region_id', $region_id);
        }

        if ($market_id != 'All') {
            $this->db->where($this->invoiceTable . '.market_id', $market_id);
        }

        if (!empty($po_number)) {
            $this->db->where($this->invoiceTable . '.po_number', $po_number);
        }
        if (!empty($invoice_number)) {
            $this->db->where($this->invoiceTable . '.invoice_number', $invoice_number);
        }

        if ($isActive) {
            $this->db->where($this->invoiceTable . '.invoice_status', $this->invoiceStatus['active']);
        }

        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('date(' . $this->db->dbprefix($this->invoiceTable) . '.created_time)  BETWEEN "' . date('Y-m-d', strtotime($fromdate)) . '" and "' . date('Y-m-d', strtotime($todate)) . '"');
        }

        if (!empty($invoicefromdate) && !empty($invoicetodate)) {
            $this->db->where($this->db->dbprefix($this->invoiceTable) . '.invoice_date  BETWEEN "' . date('Y-m-d', strtotime($invoicefromdate)) . '" and "' . date('Y-m-d', strtotime($invoicetodate)) . '"');
        }
        if ($limit !== NULL && $offset !== NULL) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function getInvoiceCount($vendor_id = 'All', $region_id = 'All', $market_id = 'All', $po_number = '', $invoice_number = '', $fromdate = '', $todate = '', $invoicefromdate = '', $invoicetodate = '', $isActive = FALSE) {
        $this->db->from($this->invoiceTable);

        if ($vendor_id != 'All') {
            $this->db->where($this->invoiceTable . '.vendor_id', $vendor_id);
        }

        if ($region_id != 'All') {
            $this->db->where($this->invoiceTable . '.region_id', $region_id);
        }

        if ($market_id != 'All') {
            $this->db->where($this->invoiceTable . '.market_id', $market_id);
        }

        if (!empty($po_number)) {
            $this->db->where($this->invoiceTable . '.po_number', $po_number);
        }
        if (!empty($invoice_number)) {
            $this->db->where($this->invoiceTable . '.invoice_number', $invoice_number);
        }

        if (!empty($fromdate) && !empty($todate)) {
            $this->db->where('date(' . $this->db->dbprefix($this->invoiceTable) . '.created_time)  BETWEEN "' . date('Y-m-d', strtotime($fromdate)) . '" and "' . date('Y-m-d', strtotime($todate)) . '"');
        }

        if ($isActive) {
            $this->db->where($this->invoiceTable . '.invoice_status', $this->invoiceStatus['active']);
        }

        if (!empty($invoicefromdate) && !empty($invoicetodate)) {
            $this->db->where($this->db->dbprefix($this->invoiceTable) . '.invoice_date  BETWEEN "' . date('Y-m-d', strtotime($invoicefromdate)) . '" and "' . date('Y-m-d', strtotime($invoicetodate)) . '"');
        }
        return $this->db->count_all_results();
    }

    public function changeInvoiceStatus($invoice_id = 0, $invoice_status = 1) {
        $data = array(
            "invoice_status" => $invoice_status,
        );
        $this->db->where('invoice_id', $invoice_id);
        $this->db->update($this->invoiceTable, $data);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->affected_rows();
        } else {
            return 0;
        }
    }

}
