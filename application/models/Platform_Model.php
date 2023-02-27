<?php defined('BASEPATH') or exit('No direct script access allowed');

class Platform_Model extends MY_Model
{

    /**
     * Platform
     * ---------------------------------
     * @param : null
     */
    public function select_platform()
    {

        $this->set_db('default');

        $sql = "
           select * from se_Platform
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

}
