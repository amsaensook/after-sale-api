<?php

    class MY_Model extends CI_Model
    {
        protected $db;

        protected function set_db($db_name)
        {
            $CI =& get_instance();

            $this->db = $CI->load->database($db_name, true);
        }

        protected function check_query($query)
        {
            if($query)
            {
                return true;        
            }
            else
            {
                return false;
            }
        }

        protected function check_begintrans()
        {
            if($this->db->trans_status() === false)
            {
                $this->db->trans_rollback();
                return false;
            }
            else
            {
                $this->db->trans_commit();
                return true;
            } 
        }

        /**
         * ?คำสั่งที่ใช้ใน Model ของ CI
         * $this->db->trans_begin();    = start begin trans
         * $query->result_array();      = return array row data
         * $query->list_fields();       = return name column table
         * $query->num_rows();          = return num array row data
         * $query->num_fields();        = return num column table
         */
    }
