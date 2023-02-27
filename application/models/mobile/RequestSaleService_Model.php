<?php defined('BASEPATH') or exit('No direct script access allowed');

class RequestSaleService_Model extends MY_Model
{

  /**
   * RequestSaleService
   * ---------------------------------
   * @param : null
   */
  public function select_request_sale_service()
  {

    $this->set_db('default');

    $sql = "
          select * from Tb_Withdraw where Withdraw_type in (1,2)  and status in (1,3,4) order by Withdraw_ID DESC
        ";

    $query = $this->db->query($sql);

    $result = ($query->num_rows() > 0) ? $query->result_array() : false;

    return $result;
  }

  /**
   * Update RequestSaleService
   * ---------------------------------
   * @param : FormData
   */
  public function update_request_sale_service($param = [])
  {
    $this->set_db('default');

    $result_withdraw = ($this->db->update('Tb_Withdraw', $param['data'], $param['where'])) ? true : false;

    if (!$result_withdraw) {
      return false;
    }

    //? select team from Tb_Withdraw

    $sql_team = " select l.Unit from Tb_Withdraw w inner join ms_Location l on w.Plan_Team = l.Location_ID where Withdraw_ID = ? ";

    $query_team = $this->db->query($sql_team, [$param['where']['Withdraw_ID']]);

    $result_team = ($query_team->num_rows() > 0) ? $query_team->result_array() : false;

    if (!$result_team) {
      return false;
    }

    $request_type = "";

    if ($result_team[0]['Unit'] == 'Service') {
      $request_type = 'Request After Service';
    } else if ($result_team[0]['Unit'] == 'Sale') {
      $request_type = 'Request Sale';
    }

    //? exec SP_WithdrawAutoReceive

    $sql_exec = "

        exec [dbo].[SP_WithdrawAutoReceive] ?,?,?

    ";;

    return ($this->db->query($sql_exec, [$param['where']['Withdraw_ID'], $param['data']['Update_By'], $request_type])) ? true : false/*$this->db->error()*/;
  }

  /**
   * RequestSaleService Item
   * ---------------------------------
   * @param : Withdraw_ID
   */
  public function select_request_sale_service_Item($Withdraw_ID)
  {

    $this->set_db('default');

    $sql = "

         
     select
                ROW_NUMBER() Over (Order by w.Withdraw_ID) as 'No'
                ,i.ITEM_CODE as 'Part'
                ,i.ITEM_ID as 'Item_ID'
                ,(
                    select count(*) from Tb_WithdrawItem where Withdraw_ID = w.Withdraw_ID and Item_ID = i.ITEM_ID  and Status = 9
                ) as 'Request'
                ,COUNT(*) as 'Total'

      from		  Tb_Withdraw w
                inner join Tb_WithdrawItem wi on w.Withdraw_ID = wi.Withdraw_ID
                inner join ms_Item i on wi.Item_ID = i.ITEM_ID

      where		  w.Withdraw_ID = ?

      group by	w.Withdraw_ID,i.ITEM_CODE,i.ITEM_ID

        ";

    $query = $this->db->query($sql, [$Withdraw_ID]);

    $result = ($query->num_rows() > 0) ? $query->result_array() : false;

    return $result;
  }

  /**
   * Exec RequestSaleService Transaction
   * ---------------------------------
   * @param : Withdraw_ID, QR_NO, Tag_ID, Username
   */
  public function exec_request_sale_service_transaction($param = [])
  {

    $this->set_db('default');

    //? select data info from Tb_TagQR

    $sql_tag = " select * from Tb_TagQR where Tag_ID = ? ";

    $query_tag = $this->db->query($sql_tag, [$param['Tag_ID']]);

    $result_tag = ($query_tag->num_rows() > 0) ? $query_tag->result_array() : false;

    if (!$result_tag) {
      return false;
    }

    //? insert temp withdraw
    $data_temp = [
      'UniqueKey' => $param['Withdraw_ID'],
      'Withdraw_ID' => $param['Withdraw_ID'],
      'QR_NO' => $param['QR_NO'],
      'ITEM_ID' => $result_tag[0]['Item_ID'],
      'Qty' => $result_tag[0]['Qty'],
      'Create_Date' => $param['Create_Date'],
      'Create_By' => $param['Create_By'],
    ];

    $result_temp = ($this->db->insert('Temp_WithdrawItem', $data_temp)) ? true : false;

    if (!$result_temp) {
      return false;
    }

    //? update status withdraw item
    $data_item = [
      'Status' => 9,
      'Update_Date' => $param['Create_Date'],
      'Update_By' => $param['Create_By'],
    ];

    $where_item = [
      'Withdraw_ID' => $param['Withdraw_ID'],
      'QR_NO' => $param['QR_NO'],
      'ITEM_ID' => $result_tag[0]['Item_ID'],
    ];

    $result_item = ($this->db->update('Tb_WithdrawItem', $data_item, $where_item)) ? true : false;

    if (!$result_item) {
      return false;
    }

    //? update status withdraw 
    $data_withdraw = [
      'status' => 4,
      'Update_Date' => $param['Create_Date'],
      'Update_By' => $param['Create_By'],
    ];

    $where_withdraw = [
      'Withdraw_ID' => $param['Withdraw_ID'],
    ];

    $result_withdraw = ($this->db->update('Tb_Withdraw', $data_withdraw, $where_withdraw)) ? true : false;

    if (!$result_withdraw) {
      return false;
    }

    return true;
  }

  /**
   * Exec RequestSaleService Item
   * ---------------------------------
   * @param : Withdraw_ID, QR_NO, Tag_ID, Username
   */
  public function check_request_sale_service_item($param = [])
  {

    $this->set_db('default');

    $sql = " 

      declare @Result_status bit
      declare @Result_Desc varchar(200)

      declare @Withdraw_ID int
      declare @QR_NO varchar(200)

      set @Result_status = 1
      set @Result_Desc = ''

      set @Withdraw_ID = ?
      set @QR_NO = ?

      if ( select COUNT(*) as CountItem from Tb_WithdrawItem where Withdraw_ID = @Withdraw_ID and QR_NO = @QR_NO ) <= 0
      begin 

        set @Result_status = 0    
        set @Result_Desc = 'QR not found in This Order'
    
      end

      if ( select COUNT(*) as CountItem from Temp_WithdrawItem where ( Withdraw_ID = @Withdraw_ID or UniqueKey = @Withdraw_ID ) and QR_NO = @QR_NO ) > 0
      begin 

        set @Result_status = 0    
        set @Result_Desc = 'QR has been scanned in This Order'
    
      end

      select 
		    @Result_status as Result_status
		   ,@Result_Desc as Result_Desc
    
    ";

    $query = $this->db->query($sql,[$param['Withdraw_ID'],$param['QR_NO']]);

    $result = ($query->num_rows() > 0) ? $query->result_array() : false;

    return $result;
  }
  
}
