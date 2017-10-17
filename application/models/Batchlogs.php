<?php

class Batchlogs extends CI_Model {

    protected $uploadLogTable = 'upload_log';
    protected $fileUploadmapTable = 'file_upload_map';
    protected $fileUploadStatus = array('error' => 0, 'uploaded' => 1, 'duplicate' => 2);

    public function __construct() {
        parent::__construct();
    }

    public function getFileUploadStatus() {
        return $this->fileUploadStatus;
    }

    public function addUploadLog() {
        $this->db->set('create_date', 'now()',FALSE);
        $this->db->insert($this->uploadLogTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return $this->db->insert_id();
        } else {
            return 0;
        }
    }

    public function addFileUploadMap($upload_id = 0, $file_name = '', $status = '') {
        $this->db->set('upload_id', $upload_id);
        $this->db->set('file_name', $file_name);
        $this->db->set('status', $status);
        $this->db->insert($this->fileUploadmapTable);
        $error = $this->db->error();
        if ($error['code'] == 0) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function getFileUploadMap($upload_id = 0) {
        $this->db->select($this->fileUploadmapTable . '.file_name,'.$this->fileUploadmapTable .'.status')
                ->from($this->fileUploadmapTable);
        $this->db->where('upload_id', $upload_id);
        $query = $this->db->get();
        if($query->result_array()){
        return $query->result_array();
        }else{
            return array();
        }
    }
    
    public function getFileUploadStatusCount($upload_id = 0) {
        $this->db->select('count(*) as file_count',FALSE)
                ->select($this->fileUploadmapTable .'.status')
                ->from($this->fileUploadmapTable)
                ->group_by($this->fileUploadmapTable .'.status');
        $this->db->where('upload_id', $upload_id);
        $query = $this->db->get();
        if($query->result_array()){
        return $query->result_array();
        }else{
            return array();
        }
    }

}
