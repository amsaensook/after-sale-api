<?php defined('BASEPATH') or exit('No direct script access allowed');

class Issue_Model extends MY_Model
{

  /**
   * Issue
   * ---------------------------------
   * @param : null
   */
  public function select_issue()
  {

    $this->set_db('default');

    $sql = "
          select *,convert(nvarchar,Withdraw_ID)+'|'+Withdraw_No+'|'+convert(nvarchar,Location_ID)+'|'+Location+'|'+Quotation_No+'|'+Customer_Name as QuotationValue,
          Quotation_No+' - '+Customer_Name as Quotation_Customer
          from View_Quotation
        ";

    $query = $this->db->query($sql);

    $result = ($query->num_rows() > 0) ? $query->result_array() : false;

    return $result;
  }

  /**
   * Update Issue
   * ---------------------------------
   * @param : FormData
   */
  public function update_issue($param = [])
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
   * Issue Item
   * ---------------------------------
   * @param : Withdraw_ID
   */
  public function select_issue_Item($Withdraw_ID)
  {

    $this->set_db('default');

    $sql = "
    SELECT   RQ.Withdraw_No, Bal.QR_NO, Grade.ITEM_ID, Grade.ITEM_CODE, Grade.ITEM_DESCRIPTION, Grade.Product_ID, PType.Product_DESCRIPTION, RCI.Qty, Bal.QTY AS Bal_QTY, Bal.ReserveQTY, Bal.LOT, 0 AS Status, 
    Bal.Location_ID
    FROM   dbo.Tb_Withdraw AS RQ INNER JOIN
    dbo.Tb_Receive AS RC ON RC.Ref_DocNo_1 = RQ.Withdraw_No INNER JOIN
    dbo.Tb_ReceiveItem AS RCI ON RCI.Rec_ID = RC.Rec_ID INNER JOIN
    dbo.Tb_StockBalance AS Bal ON Bal.QR_NO = RCI.QR_Code AND Bal.QTY - Bal.ReserveQTY <> 0 INNER JOIN
    dbo.ms_Item AS Grade ON Grade.ITEM_ID = RCI.Item_ID INNER JOIN
    dbo.ms_ProductType AS PType ON PType.Product_ID = Grade.Product_ID
    WHERE        (RQ.status = '9') AND (RC.status = '9') and RQ.Withdraw_ID = ?    
         

        ";

    $query = $this->db->query($sql, [$Withdraw_ID]);

    $result = ($query->num_rows() > 0) ? $query->result_array() : false;

    return $result;
  }

  /**
   * Exec Issue Transaction
   * ---------------------------------
   * @param : Withdraw_ID, QR_NO, Tag_ID, Username
   */
  public function exec_issue_transaction($param = [])
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
   * Exec Issue Item
   * ---------------------------------
   * @param : Withdraw_ID, QR_NO, Tag_ID, Username
   */
  public function check_issue_item($param = [])
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


   /**
     * Check Store Bal
     * ---------------------------------
     * @param : null
     */
    public function select_stockbal($param = [])
    {

        $this->set_db('default');

        $sql = "
        select TOP 1 * from View_ItemForQuotation where QR_NO = ? and Location_ID = ? and Bal_QTY <> 0
        ";

        $query = $this->db->query($sql,[$param['QR_NO'],$param['Location_ID']]);

        $result = ($query->num_rows() > 0) ? $query->result_array() : false;

        return $result;

    }

    /**
     * Insert Issue Item
     * ---------------------------------
     * @param : FormData
     */
    public function insert_issue_item($param = [])
    {
        $this->set_db('default');

        $this->db->trans_begin();

        $UniqueKey = date('YmdHis');

        foreach ($param['items'] as $value) {

            $data = [
                'UniqueKey' => $UniqueKey,
                'Withdraw_ID' => null,
                'QR_NO' => $value['QR_NO'],
                'ITEM_ID' => $value['ITEM_ID'],
                'Qty' => $value['Qty'],
                'Create_Date' => $param['user']['Create_Date'],
                'Create_By' => $param['user']['Create_By'],
     
            ];

            $this->db->insert('Temp_WithdrawItem', $data);

        }

        $sql = "

            exec [dbo].[SP_WithdrawTrans] ?,?,?

        ";

        $this->db->query($sql, [$UniqueKey,$param['user']['Create_By'],'Issue']);

        return $this->check_begintrans();/*$this->db->error();*/
    }
    

  
}
