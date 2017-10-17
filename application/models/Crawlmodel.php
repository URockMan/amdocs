<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Crawlmodel extends CI_Model {

    protected $crawlStatus = array('failed' => 0, 'pending' => 1, 'inprocess' => 2, 'processed' => 3);
    //table
    protected $crawlLogTable = 'crawl_log';
    protected $providerTable = 'provider';

    public function __construct() {
        parent::__construct();
    }

    public function getCrawlStatus() {
        return $this->crawlStatus;
    }

    public function addCrawlLog($file_name = '', $pdf_name = '', $revision = '', $vendor = '', $status = 1, $log = '') {
        $data['file_name'] = $file_name;
        $data['pdf_name'] = $pdf_name;
        $data['revision'] = $revision;
        $data['vendor'] = $vendor;
        $data['status'] = $status;
        $data['log'] = $log;
        $this->db->insert($this->crawlLogTable, $data);
        return $this->db->insert_id();
    }

    public function updatelog($id = 0, $status = 1, $po_number = NULL, $revision = NULL, $file_name = NULL) {
        $this->db->set('status', $status);
        $this->db->where('id', $id);
        if ($po_number !== NULL) {
            $this->db->set('po_number', $po_number);
        }
        if ($revision !== NULL) {
            $this->db->set('revision', $revision);
        }
        if ($file_name !== NULL) {
            $this->db->set('file_name', $file_name);
        }
        $this->db->update($this->crawlLogTable);
        return $this->db->affected_rows();
    }

    public function checkFileNameExists($pdf_name = '') {
        $this->db->select('id, file_name, po_number, revision');
        $this->db->where('pdf_name', $pdf_name);
        $result = $this->db->get($this->crawlLogTable);
        return $result->row_array();
    }

}
