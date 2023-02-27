<?php defined('BASEPATH') or exit('No direct script access allowed');

class MenuType_Model extends MY_Model
{

    /**
     * Menu Type
     * ---------------------------------
     * @param : null
     */
    public function select_menu_type()
    {

        $this->set_db('default');

        $sql = "
           select * from se_MenuType
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

}
