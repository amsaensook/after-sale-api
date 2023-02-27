<?php defined('BASEPATH') or exit('No direct script access allowed');

class CheckPart_Model extends MY_Model
{

    /**
     * CheckPart
     * ---------------------------------
     * @param : QR_NO
     */
    public function select_check_part($QR_NO)
    {

        $this->set_db('default');

        $sql = "
            select  * 
            from    View_StockBalance
            where   QR_NO = ?
        ";

        $query = $this->db->query($sql, [$QR_NO]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

}
