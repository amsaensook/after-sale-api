<?php defined('BASEPATH') or exit('No direct script access allowed');

class CountStock_Model extends MY_Model
{

    /**
     * CountStock
     * ---------------------------------
     * @param : null
     */
    public function select_count_stock()
    {

        $this->set_db('default');

        $sql = "
           select * from Tb_StockCount where Status in (1,3) order by CountStock_ID DESC
        ";

        $query = $this->db->query($sql);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Update CountStock
     * ---------------------------------
     * @param : FormData
     */
    public function update_count_stock($param = [])
    {
        $this->set_db('default');

        return ($this->db->update('Tb_StockCount', $param['data'], $param['where'])) ? true : false/*$this->db->error()*/;

    }

    /**
     * CountStockItem
     * ---------------------------------
     * @param : Rec_ID
     */
    public function select_count_stock_Item($Rec_ID)
    {

        $this->set_db('default');

        $sql = "

            select      ROW_NUMBER() Over (Order by s.CountStock_ID) as 'No'
                        ,i.ITEM_CODE as 'Item'
                        ,sb.Item_ID as 'Item_ID'
                        ,SUM(Total_QTY) as 'Balance'
                        ,(
                        
                        select count(*) from Tb_StockCount_Actual where CountStock_ID = s.CountStock_ID and Item_ID = sb.Item_ID 
                        ) as 'Actual'

            from	    Tb_StockCount s
                        inner join Tb_StockCount_Balance sb on s.CountStock_ID = sb.CountStock_ID
                        inner join ms_Item i on sb.Item_ID = i.ITEM_ID

            where		s.CountStock_ID = ?

            group by	s.CountStock_ID,i.ITEM_CODE,sb.Item_ID

        ";

        $query = $this->db->query($sql, [$Rec_ID]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Exec CountStock Transaction
     * ---------------------------------
     * @param : Rec_ID, QR_NO, Tag_ID, Username
     */
    public function exec_count_stock_transaction($param = [])
    {

        $this->set_db('default');

        $sql = "

            exec [dbo].[SP_CheckStockBalance] ?,?,?,?

        ";

        $query = $this->db->query($sql,[$param['QR_NO'],$param['Item_ID'],$param['CountStock_ID'],$param['Username']]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

}
